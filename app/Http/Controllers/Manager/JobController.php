<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobModelResource;
use App\Models\Job;
use App\Models\JobModel;
use App\Models\JobTechnician;
use App\Models\Referral;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = JobModel::query();

        if ($request->company_id && $request->company_id != '') {
            $query->whereIn('company_id', explode(',', $request->company_id));
        }

        return JobModelResource::collection(executeQuery(
            $query->with('company', 'technicians.technician', 'allWows', 'donates'))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JobModelResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'company_id' => 'required|integer|exists:users,id',
            'invoice_no' => 'required',
            'truck_id' => 'required|integer|exists:trucks,id',
            'client' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'payment_method' => 'nullable|string|max:255|in:Cash,Credit Card,Cheque',
            'payment_received_at' => 'nullable|date|max:255',
            'tip' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'truck_technician_id' => 'required|integer|exists:users,id',
            'sales_person_id' => 'nullable|integer|exists:users,id',
            'operational_manager_id' => 'required|integer|exists:users,id',
            'general_manager_id' => 'required|integer|exists:users,id',
            'technicians' => 'required|array',
            'technicians.*' => 'integer|exists:users,id',
            'commissions.*' => 'nullable|numeric|min:0|max:100',
            'sales_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'operational_manager_commission_percentage' => 'required|numeric|min:0|max:100',
            'general_manager_commission_percentage' => 'required|numeric|min:0|max:100',
            'referred' => 'required|boolean',
            'referral.name' => 'required_if:referred,true',
            'referral.email' => 'required_if:referred,true',
        ]);

        $exists = JobModel::where('company_id', $data['company_id'])
            ->where('invoice_no', $data['invoice_no'])->first();

        if ($exists) {
            throw ValidationException::withMessages([
                'invoice_no' => ['This invoice no already exists.'],
            ]);
        }

        if (!$data['amount'])
            unset($data['amount']);
        else
            $data['payment_received_at'] = Carbon::now();

        $tip = 0;
        if ($data['tip'])
            $tip = $data['tip'];

        unset($data['tip']);

        $referral = $data['referral'];

        $truck = Truck::find($data['truck_id']);
        $data['machine_id'] = $truck->machine_id;
        $technicians = [];
        $techniciansSc = [];

        foreach ($data['technicians'] as $technician) {
            //$t = User::find($technician);

            $technicians[] = new JobTechnician([
                'technician_id' => $technician,
                'default_percentage' => $data['commissions'][$technician]
            ]);

            $techniciansSc[] = [
                'id' => $technician,
                'comment' => '',
                'reliable' => 4,
                'team_player' => 4,
                'integrity' => 4,
                'great_communicator' => 4,
                'proactive' => 4,
            ];
        }
        unset($data['technicians']);
        unset($data['commissions']);
        unset($data['referral']);

        // Sales commission
        if (!$data['sales_person_id']) {
            unset($data['sales_commission_percentage']);
        }

        $job = JobModel::create($data);
        $job->technicians()->saveMany($technicians);

        if ($data['referred']) {
            $referral['job_model_id'] = $job->id;
            $r = Referral::create($referral);

            $job->referral_id = $r->id;
            $job->save();
        }

        if (isset($data['amount']) && $data['amount'] != 0) {
            $commonJobController = new \App\Http\Controllers\Common\JobController;
            $commonJobController->pay($job, $request);
        }

        if ($tip != 0) {
            $commonJobController = new \App\Http\Controllers\Common\JobController;

            $req = new Request([
                'amount' => $tip,
            ]);

            $commonJobController->tip($job, $req);
        }

        $commonJobController = new \App\Http\Controllers\Common\JobController;
        $req = new Request([
            'technicians' => $techniciansSc,
        ]);

        $commonJobController->sc($job, $req);

        return new JobModelResource($job);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return JobModelResource
     */
    public function show(JobModel $job)
    {
        return new JobModelResource($job->load('company', 'truck', 'machine', 'truckTechnician', 'salesPerson',
            'operationalManager', 'generalManager', 'technicians', 'technicians.technician', 'transactions',
            'transactions.user', 'referral', 'allWows'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobModel $job)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'company_id' => 'required|integer|exists:users,id',
            'invoice_no' => 'required',
            'truck_id' => 'required|integer|exists:trucks,id',
            'client' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'payment_method' => 'nullable|string|max:255',
            'payment_received_at' => 'nullable|date|max:255',
            'tip' => 'nullable|numeric|min:0',
            'truck_technician_id' => 'required|integer|exists:users,id',
            'sales_person_id' => 'nullable|integer|exists:users,id',
            'operational_manager_id' => 'required|integer|exists:users,id',
            'general_manager_id' => 'required|integer|exists:users,id',
            'technicians' => 'required|array',
            'technicians.*' => 'integer|exists:users,id',
        ]);

        if (!$data['tip'])
            $data['tip'] = 0;

        // Technicians
        $existingTechniciansId = $job->technicians->pluck('technician_id')->toArray();
        $newTechnicians = [];

        foreach ($data['technicians'] as $technician) {
            if (in_array($technician, $existingTechniciansId)) {
                $existingTechniciansId = array_filter($existingTechniciansId, function ($t) use ($technician) {
                   return $t != $technician;
                });
            } else {
                $t = User::find($technician);

                $newTechnicians[] = new JobTechnician([
                    'technician_id' => $technician,
                    'default_percentage' => $t->default_percentage
                ]);
            }
        }

        $job->technicians()->saveMany($newTechnicians);

        foreach ($existingTechniciansId as $id)
            $job->technicians()->where('technician_id', $id)->delete();

        unset($data['technicians']);

        // Sales commission
        if ($data['sales_person_id']) {
            $user = User::find($data['sales_person_id']);

            $data['sales_commission_percentage'] = $user->sales_commission;
        }

        // Operational manager commission
        $user = User::find($data['operational_manager_id']);
        $data['operational_manager_commission_percentage'] = $user->operational_manager_commission;

        // General manager commission
        $user = User::find($data['general_manager_id']);
        $data['general_manager_commission_percentage'] = $user->general_manager_commission;

        return $job->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobModel $job)
    {
        return $job->delete();
    }
}
