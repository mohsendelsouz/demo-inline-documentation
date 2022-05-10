<?php

namespace App\Jobs;

use App\Models\Goal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WeeklyGoalCreate
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
        $users = User::all();

        foreach ($users as $user) {
            Goal::create([
                'user_id' => $user->id,
                'date' => Carbon::now()->startOfWeek(Carbon::SUNDAY),
                'end_date' => Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(6),
                'week' => Carbon::now()->startOfWeek(Carbon::SUNDAY)->weekOfYear,
                'scorecard_goal' => $user->scorecard_goal,
                'wows_goal' => $user->wows_goal,
                'pay_goal' => $user->production_goal,
                'job_goal' => $user->job_goal,
            ]);
        }
    }
}
