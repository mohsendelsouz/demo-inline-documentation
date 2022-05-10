<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobModelResource;
use App\Models\CashDeposit;
use App\Models\Charity;
use App\Models\CharityDonate;
use App\Models\Goal;
use App\Models\JobModel;
use App\Models\JobTechnician;
use App\Models\Machine;
use App\Models\MachineMaintenance;
use App\Models\Setting;
use App\Models\Truck;
use App\Models\TruckMaintenance;
use App\Models\User;
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

        $jobs = JobModel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        $truckMaintenances = TruckMaintenance::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        $machineMaintenances = MachineMaintenance::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        $allJobTechnicians = JobTechnician::whereIn('job_model_id', $jobs->pluck('id')->toArray())
            ->get();

        $totalAmount = $jobs->sum('amount');
        $totalTip = $jobs->sum('tip');
        $totalWow = $jobs->sum('wows');
        $totalTruckMaintenance = $truckMaintenances->sum('amount');
        $totalMachineMaintenance = $machineMaintenances->sum('amount');
        $technicianCommission = $jobs->sum('technician_commission');
        $gmCommission = $jobs->sum('general_manager_commission');
        $omCommission = $jobs->sum('operational_manager_commission');
        $salesCommission = $jobs->sum('sales_commission');

        $profit = $totalAmount - $totalWow - $totalTruckMaintenance -
            $totalMachineMaintenance - $technicianCommission - $gmCommission -
            $omCommission - $salesCommission;

        return response()->json([
            'data' => [
                'jobs' => $jobs->count(),
                'total_amount' => $totalAmount,
                'total_tip' => $totalTip,
                'total_wow' => $totalWow,
                'active_technician' => $allJobTechnicians->unique('technician_id')->count(),
                'truck_maintenance' => $totalTruckMaintenance,
                'machine_maintenance' => $totalMachineMaintenance,
                'profit' => $profit,
                'technician_commission' => $technicianCommission,
                'general_manager_commission' => $gmCommission,
                'operational_manager_commission' => $omCommission,
                'sales_commission' => $salesCommission,
            ]
        ]);
    }

    public function trucks(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $jobs = JobModel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        $trucks = Truck::all();

        foreach ($trucks as $truck) {
            $truck->job_count = $jobs->where('truck_id', $truck->id)->count();
        }

        return response()->json([
            'data' => $trucks
        ]);
    }

    public function machines(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $jobs = JobModel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        $machines = Machine::all();

        foreach ($machines as $machine) {
            $machine->job_count = $jobs->where('machine_id', $machine->id)->count();
        }

        return response()->json([
            'data' => $machines
        ]);
    }

    public function companies(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $jobs = JobModel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        $companies = User::where('type', 'company')->get();

        foreach ($companies as $company) {
            $company->job_count = $jobs->where('company_id', $company->id)->count();
        }

        return response()->json([
            'data' => $companies
        ]);
    }

    public function cash()
    {
        $jobs = JobModel::where('payment_method', 'Cash')->with('technicians.technician')->get();
        $deposit = CashDeposit::sum('amount');

        $jobsArray = [];

        foreach ($jobs->where('manager_received', 0) as $job) {
            $jobsArray[] = $job;
        }

        return response()->json([
            'data' => [
                'jobs' => $jobsArray,
                'technician_hand' => $jobs->where('manager_received', 0)->sum('amount'),
                'manager_hand' => $jobs->where('manager_received', 1)->sum('amount') - $deposit,
                'deposit' => $deposit
            ]
        ]);
    }

    public function charities(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $charities = Charity::all();

        $donates = CharityDonate::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();

        foreach ($charities as $charity) {
            $charity->total_amount = $donates->where('charity_id', $charity->id)->sum('amount');
        }

        return response()->json([
            'data' => $charities
        ]);
    }

    public function technicianWallet(Request $request)
    {
        $user = User::where('id', $request->technician)->first();

        $ytdJobTechnicians = JobTechnician::whereRelation('job', 'date', '>=', Carbon::now()->startOfYear())
            ->whereRelation('job', 'date', '<=', Carbon::now())
            ->where('technician_id', $user->id)
            ->get();

        return response()->json([
            'data' => [
                'tip_wallet' => '$'.number_format($user->tip_wallet, 2),
                'wow_wallet' => '$'.number_format($user->wow_wallet, 2),
                'wow_ytd' => $ytdJobTechnicians->sum('wow'),
                'tip_ytd' => $ytdJobTechnicians->sum('tip'),
                'commission_ytd' => $ytdJobTechnicians->sum('commission'),
                'commission_wallet' => '$'.number_format($user->commission_wallet, 2),
                'job' => JobModel::whereHas('technicians', function ($q) use ($user) {
                    $q->where('technician_id', $user->id);
                })->count(),
                'job_ytd' => JobModel::where('date', '>=', Carbon::now()->startOfYear())
                    ->where('date', '<=', Carbon::now())
                    ->whereHas('technicians', function ($q) {
                        $q->where('technician_id', Auth::user()->id);
                    })->count(),
            ]
        ]);
    }

    public function technicianGoalReport($technician, Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $allGoals = Goal::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('user_id', $technician)
            ->get();

        $allJobTechnicians = JobTechnician::where('technician_id', $technician)
//            ->where('wow', '>', 0)
            ->whereRelation('job', 'date', '>=', $start)
            ->whereRelation('job', 'date', '<=', $end)
            ->get();

        $wowsGoal = $allGoals->sum('wows_goal');
        $wowsActual = Wow::whereIn('job_model_id', $allJobTechnicians->where('technician_id', $technician)->pluck('job_model_id')->toArray())
            ->whereNull('charity_id')
            ->count();


        $productionGoal = $allGoals->sum('pay_goal');
        $productionActual = $allJobTechnicians->sum('commission');

        $scGoal = $allGoals->avg('scorecard_goal') ?: 0;
        $scActual = $allJobTechnicians->avg('avg_sc') ?: 0;

        $jobGoal = $allGoals->sum('job_goal') ?: 0;
        $jobActual = $allJobTechnicians->count() ?: 0;

        return response()->json([
            'data' => [
                'wows_goal' => $wowsGoal,
                'wows_actual' => $wowsActual,
                'production_goal' => $productionGoal,
                'production_actual' => $productionActual,
                'sc_goal' => $scGoal,
                'sc_actual' => $scActual,
                'job_goal' => $jobGoal,
                'job_actual' => $jobActual,
            ]
        ]);
    }

    public function companiesGoal(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $companies = User::where('type', 'company')->get();

        foreach ($companies as $company) {
            $allGoals = Goal::where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->where('user_id', $company->id)
                ->get();

            $wowsGoal = $allGoals->sum('wows_goal');
            $wowsActual = Wow::whereRelation('job', 'company_id', '=', $company->id)
                ->whereRelation('job', 'date', '>=', $start)
                ->whereRelation('job', 'date', '<=', $end)
                ->count();

            $wowsPercentage = $wowsGoal !== 0 ? (($wowsActual * 100) / $wowsGoal) : 0;

            $jobGoal = $allGoals->sum('job_goal');
            $jobActual = JobModel::where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->where('company_id', $company->id)
                ->count();

            $productionGoal = $allGoals->sum('pay_goal');
            $productionActual = JobModel::where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->where('company_id', $company->id)
                ->sum('amount');

            $productionPercentage = ($productionGoal != 0) ? (($productionActual * 100) / $productionGoal) : 0;

            $company->wows_goal = $wowsGoal;
            $company->wows_actual = $wowsActual;
            $company->wows_percentage = min($wowsPercentage, 100);

            $company->job_goal = $jobGoal;
            $company->job_actual = $jobActual;

            $company->production_goal = $productionGoal;
            $company->production_actual = $productionActual;
            $company->production_percentage = min($productionPercentage, 100);
        }

        return response()->json([
            'data' => $companies
        ]);
    }

    public function jobs(Request $request)
    {
        $start = Carbon::parse(explode('/', $request->date)[0]);
        $end = Carbon::parse(explode('/', $request->date)[1]);

        $query = JobModel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->whereHas('technicians', function ($q) use ($request) {
                $q->where('technician_id', $request->technician_id);
            })->with(['company', 'allWows', 'technicians' => function($q) use ($request) {
                $q->where('technician_id', $request->technician_id);
            }]);

        if ($request->type === 'Wow') {
            $query->where('wows', '>', 0);
        } elseif ($request->type === 'Production') {
            $query->where('amount', '>', 0);
        }

        return JobModelResource::collection($query->get());
    }

    public function truckDetails(Request $request)
    {
        $trucks = Truck::all();
        $setting = Setting::first();


        $jobs = JobModel::all();
        $truckMaintenance = TruckMaintenance::all();
        $machineMaintenance = MachineMaintenance::all();

        foreach ($trucks as $truck) {
            $nextInspection = '-';
            $nextRegularMaintenance = '-';
            $nextMajorMaintenance = '-';

            if ($setting->truck_regular_maintenance_millage) {
                $maintenances = $truck->maintenances;

                $maxMillage = $maintenances->max('millage');

                $lastRegularMaintenance = $maintenances->where('type', 'Regular Service')->max('millage');
                $nextRegularMaintenance = ($lastRegularMaintenance + $setting->truck_regular_maintenance_millage) - $maxMillage;

                $lastMajorMaintenance = $maintenances->where('type', 'Major Service')->max('millage');
                $nextMajorMaintenance = ($lastMajorMaintenance + $setting->truck_major_maintenance_millage) - $maxMillage;
            }

            if ($truck->last_inspection) {
                if ($setting->truck_inspection === 'Weekly')
                    $nextInspection = $truck->last_inspection->addWeek()->format('M j, Y');
                elseif ($setting->truck_inspection === 'Monthly') {
                    $nextInspection = $truck->last_inspection->addMonth()->format('M j, Y');
                }
            }

            $truck->total_job = $jobs->where('truck_id', $truck->id)->count();
            $truck->total_amount = $jobs->where('truck_id', $truck->id)->sum('amount');
            $truck->total_maintenance = $truckMaintenance->where('truck_id', $truck->id)->sum('amount');
            $truck->total_maintenance_gas = $truckMaintenance->where('truck_id', $truck->id)->where('type', 'Gas')->sum('amount');
            $truck->total_machine_gas = $machineMaintenance->where('machine_id', $truck->machine_id)->where('type', 'Gas')->sum('amount');
            $truck->total_maintenance_supply = $truckMaintenance->where('truck_id', $truck->id)->where('type', 'Supply')->sum('amount');
            $truck->total_maintenance_service = $truckMaintenance->where('truck_id', $truck->id)->where('type', '!=', 'Supply')->where('type', '!=', 'Gas')->sum('amount');
            $truck->total_profit = $truck->total_amount - $truck->total_maintenance;
            $truck->next_inspection = $nextInspection;
            $truck->next_regular_maintenance = $nextRegularMaintenance;
            $truck->next_major_maintenance = $nextMajorMaintenance;
        }

        return response()->json([
            'data' => $trucks
        ]);
    }
}
