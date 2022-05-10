<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobModelResource;
use App\Models\Goal;
use App\Models\JobModel;
use App\Models\JobTechnician;
use App\Models\Wow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $allGoals = Goal::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('user_id', Auth::user()->id)
            ->get();

        $allJobTechnicians = JobTechnician::where('technician_id', Auth::user()->id)
//            ->where('wow', '>', 0)
            ->whereRelation('job', 'date', '>=', $start)
            ->whereRelation('job', 'date', '<=', $end)
            ->get();

        $wowsGoal = $allGoals->sum('wows_goal');
        $wowsActual = Wow::whereIn('job_model_id', $allJobTechnicians->where('technician_id', Auth::user()->id)->pluck('job_model_id')->toArray())
            ->whereNull('charity_id')
            ->count();
        $wowsPercentage = $wowsGoal !== 0 ? (($wowsActual * 100) / $wowsGoal) : 0;


        $productionGoal = $allGoals->sum('pay_goal');
        $productionActual = $allJobTechnicians->sum('commission');
        $productionPercentage = ($productionGoal != 0) ? (($productionActual * 100) / $productionGoal) : 0;

        $scGoal = $allGoals->avg('scorecard_goal') ?: 0;
        $scActual = $allJobTechnicians->avg('avg_sc') ?: 0;
        $scPercentage = $scGoal !== 0 ? (($scActual * 100) / $scGoal) : 0;

        $jobGoal = $allGoals->sum('job_goal') ?: 0;
        $jobActual = $allJobTechnicians->count() ?: 0;

        return response()->json([
            'data' => [
                'wows_goal' => $wowsGoal,
                'wows_actual' => $wowsActual,
                'wows_percentage' => min($wowsPercentage, 100),
                'production_goal' => $productionGoal,
                'production_actual' => $productionActual,
                'production_percentage' => min($productionPercentage, 100),
                'sc_goal' => $scGoal,
                'sc_actual' => $scActual,
                'job_goal' => $jobGoal,
                'job_actual' => $jobActual,
                'sc_percentage' => min($scPercentage, 100),
            ]
        ]);
    }

    public function jobs(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $query = JobModel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->whereHas('technicians', function ($q) use ($request) {
                $q->where('technician_id', Auth::user()->id);
            })->with(['company', 'allWows', 'technicians' => function($q) {
                $q->where('technician_id', Auth::user()->id);
            }]);

        if ($request->type === 'Wow') {
            $query->where('wows', '>', 0);
        } elseif ($request->type === 'Production') {
            $query->where('amount', '>', 0);
        }

        return JobModelResource::collection($query->get());
    }
}
