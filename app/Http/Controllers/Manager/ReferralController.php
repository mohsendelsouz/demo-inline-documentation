<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReferralResource;
use App\Models\JobModel;
use App\Models\Referral;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        return ReferralResource::collection(
            executeQuery(Referral::query()->with('job'))
        );
    }

    public function sendBonus(Referral $referral, Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $job = JobModel::find($referral->job_model_id);

        $referral->amount = $data['amount'];
        $referral->date = Carbon::now();
        $referral->sent = 1;
        $referral->save();

        $job->referral_amount = $data['amount'];
        $job->save();

        Transaction::create([
            'date' => Carbon::now(),
            'type' => 'Referral Bonus',
            'job_model_id' => $job->id,
            'amount' => $data['amount']
        ]);
    }
}
