<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\JobModel;
use App\Models\Machine;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function topWidgets()
    {
        return response()->json([
            'data' => [
                'technician' => User::where('type', 'technician')->count(),
                'truck' => Truck::count(),
                'machine' => Machine::count(),
                'job' => JobModel::count()
            ]
        ]);
    }

    public function salesChart()
    {
        $labels = [];
        $data = [];

        for($i=5; $i >= 0; $i--) {
            $labels[] = strtoupper(Carbon::now()->subMonths($i)->format('M'));
            $data[] = JobModel::where('date', '>=', Carbon::now()->subMonths($i)->startOfMonth())
                ->where('date', '<=', Carbon::now()->subMonths($i)->endOfMonth())
                ->sum('amount');
        }

        return response()->json([
            'data' => [
                'labels' => $labels,
                'data' => $data,
            ]
        ]);
    }

    public function jobCountChart()
    {
        $labels = [];
        $data = [];

        for($i=5; $i >= 0; $i--) {
            $labels[] = strtoupper(Carbon::now()->subMonths($i)->format('M'));
            $data[] = JobModel::where('date', '>=', Carbon::now()->subMonths($i)->startOfMonth())
                ->where('date', '<=', Carbon::now()->subMonths($i)->endOfMonth())
                ->count();
        }

        return response()->json([
            'data' => [
                'labels' => $labels,
                'data' => $data,
            ]
        ]);
    }
}
