<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\JobModel;
use App\Models\JobTechnician;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function topWidgets()
    {
        $user = Auth::user();

        $ytdJobTechnicians = JobTechnician::whereRelation('job', 'date', '>=', Carbon::now()->startOfYear())
            ->whereRelation('job', 'date', '<=', Carbon::now())
            ->where('technician_id', Auth::user()->id)
            ->get();

        return response()->json([
            'data' => [
                'tip_wallet' => '$'.number_format($user->tip_wallet, 2),
                'wow_wallet' => '$'.number_format($user->wow_wallet, 2),
                'wow_ytd' => $ytdJobTechnicians->sum('wow'),
                'tip_ytd' => $ytdJobTechnicians->sum('tip'),
                'commission_ytd' => $ytdJobTechnicians->sum('commission'),
                'commission_wallet' => '$'.number_format($user->commission_wallet, 2),
                'job' => JobModel::whereHas('technicians', function ($q) {
                    $q->where('technician_id', Auth::user()->id);
                })->count(),
                'job_ytd' => JobModel::where('date', '>=', Carbon::now()->startOfYear())
                    ->where('date', '<=', Carbon::now())
                    ->whereHas('technicians', function ($q) {
                        $q->where('technician_id', Auth::user()->id);
                    })->count(),
            ]
        ]);
    }
}
