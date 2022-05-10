<?php

namespace App\Jobs;

use App\Models\Goal;
use App\Models\JobModel;
use App\Models\JobTechnician;
use App\Models\User;
use App\Models\Wow;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WeeklyGoalCalculation
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $previousWeekOfYear = Carbon::now()->subWeek()->startOfWeek(Carbon::SUNDAY)->weekOfYear;
        $startDate = Carbon::now()
            ->subWeek()
            ->startOfWeek(Carbon::SUNDAY)
            ->startOfDay();

        $endDate = Carbon::now()
            ->subWeek()
            ->startOfWeek(Carbon::SUNDAY)
            ->endOfWeek(Carbon::SATURDAY)
            ->endOfDay();

        $users = User::all();
        $technicians = JobTechnician::whereRelation('job', 'date', '>=', $startDate)
            ->whereRelation('job', 'date', '<=', $endDate)
            ->get();

        foreach ($users as $user) {
            $avgSc = $technicians->where('technician_id', $user->id)->avg('avg_sc');
            $goal = Goal::where('user_id', $user->id)
                ->where('date', $startDate)->first();

            if ($user->type === 'company') {
                $wows = Wow::whereIn('job_model_id', $technicians->pluck('job_model_id')->toArray())
                    ->whereRelation('job', 'company_id', '=', $user->id)
                    ->count();

                $payGoal = JobModel::whereIn('id', $technicians->pluck('job_model_id')->toArray())
                    ->where('company_id', $user->id)
                    ->sum('amount');

                $jobGoal = JobModel::where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->where('company_id', $user->id)->count();

            } else {
                $wows = Wow::whereIn('job_model_id', $technicians->where('technician_id', $user->id)->pluck('job_model_id')->toArray())
                    ->whereNull('charity_id')
                    ->count();

                $jobGoal = $technicians->where('technician_id', $user->id)->count();

                $payGoal = $technicians->where('technician_id', $user->id)->sum('commission');
            }

            if ($goal) {
                $goal->scorecard_actual = $avgSc ?: 0;
                $goal->wows_actual = $wows ?: 0;
                $goal->job_actual = $jobGoal ?: 0;
                $goal->pay_actual = $payGoal ?: 0;
                $goal->save();
            }
        }
    }
}
