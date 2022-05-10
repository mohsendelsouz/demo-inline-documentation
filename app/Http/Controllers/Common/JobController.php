<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Charity;
use App\Models\CharityDonate;
use App\Models\JobModel;
use App\Models\JobTechnician;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wow;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function pay(JobModel $job, Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $job->amount = $data['amount'];
        $job->payment_received_at = Carbon::now();

        // Sales commission
        if ($job->sales_person_id) {
            $user = User::find($job->sales_person_id);
            $amount = ((float) $data['amount']) * (((float) $job->sales_commission_percentage) / 100);

            if ($amount > 0) {
                Transaction::create([
                    'user_id' => $job->sales_person_id,
                    'date' => Carbon::now(),
                    'type' => 'Sales Commission',
                    'job_model_id' => $job->id,
                    'amount' => $amount
                ]);

                $user->increment('commission_wallet', $amount);
                $job->sales_commission = $amount;
            }
        }

        // Operational manager commission
        $user = User::find($job->operational_manager_id);
        $amount = ((float) $data['amount']) * (((float) $job->operational_manager_commission_percentage) / 100);

        if ($amount) {
            Transaction::create([
                'user_id' => $job->operational_manager_id,
                'date' => Carbon::now(),
                'type' => 'Operational Manager Commission',
                'job_model_id' => $job->id,
                'amount' => $amount
            ]);

            $user->increment('commission_wallet', $amount);
            $job->operational_manager_commission = $amount;
        }

        // General manager commission
        $user = User::find($job->general_manager_id);
        $amount = ((float) $data['amount']) * (((float) $job->general_manager_commission_percentage) / 100);

        if ($amount > 0) {
            Transaction::create([
                'user_id' => $job->general_manager_id,
                'date' => Carbon::now(),
                'type' => 'General Manager Commission',
                'job_model_id' => $job->id,
                'amount' => $amount
            ]);

            $user->increment('commission_wallet', $amount);
            $job->general_manager_commission = $amount;
        }

        // Technicians percentage
        foreach ($job->technicians as $technician) {
            $user = User::find($technician->technician_id);
            $amount = ((float) $data['amount']) * (((float) $technician->default_percentage) / 100);

            if ($amount > 0) {
                $technician->commission = $amount;
                $technician->save();

                Transaction::create([
                    'user_id' => $technician->technician_id,
                    'date' => Carbon::now(),
                    'type' => 'Technician Commission',
                    'job_model_id' => $job->id,
                    'amount' => $amount
                ]);

                $user->increment('commission_wallet', $amount);
                $job->technician_commission += $amount;
            }
        }

        return $job->save();
    }

    public function tip(JobModel $job, Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $technicians = $job->technicians;
        $amount = round((float) $data['amount'] / count($technicians), 2);

        // Technicians percentage
        foreach ($technicians as $technician) {
            $user = User::find($technician->technician_id);

            if ($amount > 0) {
                Transaction::create([
                    'user_id' => $technician->technician_id,
                    'date' => Carbon::now(),
                    'type' => 'Tip',
                    'job_model_id' => $job->id,
                    'amount' => $amount
                ]);

                $technician->tip = $amount;
                $technician->save();

                $user->increment('tip_wallet', $amount);
            }
        }

        $job->tip += $data['amount'];
        $job->save();

        return true;
    }

    public function sc(JobModel $job, Request $request)
    {
        foreach ($request->technicians as $technician) {
            $t = JobTechnician::where('job_model_id', $job->id)
                ->where('technician_id', $technician['id'])
                ->first();

            $t->comment = $technician['comment'] ?? null;
            $t->reliable = $technician['reliable'];
            $t->team_player = $technician['team_player'];
            $t->integrity = $technician['integrity'];
            $t->great_communicator = $technician['great_communicator'];
            $t->proactive = $technician['proactive'];

            $totalSc = $technician['reliable'] + $technician['team_player'] +
                $technician['integrity'] + $technician['great_communicator'] +
                $technician['proactive'];

            $t->avg_sc = round($totalSc/5, 2);
            $t->save();
        }

        return true;
    }

    public function wow(JobModel $job, Request $request)
    {
        if ($request->homeStars['review']) {
            $this->insertWow($job, 'Home Stars', $request->homeStars);

            if ($request->homeStars['charity_id'] != '' && $request->homeStars['donate_amount'] != '') {
                $this->insertCharity($job, 'Home Stars', $request->homeStars);
            }
        }

        if ($request->googleReviews['review']) {
            $this->insertWow($job, 'Google Reviews', $request->googleReviews);

            if ($request->googleReviews['charity_id'] != '' && $request->googleReviews['donate_amount'] != '') {
                $this->insertCharity($job, 'Google Reviews', $request->googleReviews);
            }
        }

        if ($request->fiveAround['review']) {
            $this->insertWow($job, '5 Around', $request->fiveAround);
        }

        return true;
    }

    public function insertWow($job, $type, $data)
    {
        $wowAmount = 25;

        $exists = Wow::where('job_model_id', $job->id)
            ->where('type', $type)
            ->first();

        if (!$exists) {
            if ($data['to'] == '') {
                $technicians = $job->technicians;
                $amount = round($wowAmount / count($technicians), 2);

                // Technicians percentage
                foreach ($technicians as $technician) {
                    $user = User::find($technician->technician_id);

                    if ($amount > 0) {
                        Transaction::create([
                            'user_id' => $technician->technician_id,
                            'date' => Carbon::now(),
                            'type' => $type.' Wow',
                            'job_model_id' => $job->id,
                            'amount' => $amount
                        ]);

                        $technician->increment('wow', $amount);
                        $user->increment('wow_wallet', $amount);
                    }
                }
            }

            Wow::create([
                'job_model_id' => $job->id,
                'type' =>  $type,
                'amount' => $wowAmount,
                'charity_id' => $data['to'],
                'review' => $data['review']
            ]);

            $job->increment('wows', $wowAmount);
        }
    }

    public function insertCharity($job, $type, $data)
    {
        $exists = CharityDonate::where('job_model_id', $job->id)
            ->where('type', $type)
            ->first();

        if (!$exists) {
            CharityDonate::create([
                'job_model_id' => $job->id,
                'charity_id' => $data['charity_id'],
                'type' => $type,
                'date' => Carbon::now(),
                'amount' => $data['donate_amount']
            ]);

            $charity = Charity::find($data['charity_id']);

            $job->increment('charity_donate', $data['donate_amount']);

            Transaction::create([
                'date' => Carbon::now(),
                'type' => 'Donate to charity ('.$charity->name.')',
                'job_model_id' => $job->id,
                'amount' => $data['donate_amount'],
            ]);
        }
    }

    public function managerReceived(JobModel $job)
    {
        return $job->update([
            'manager_received' => 1
        ]);
    }
}
