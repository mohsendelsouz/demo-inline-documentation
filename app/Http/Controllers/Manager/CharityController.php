<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharityResource;
use App\Models\Charity;
use App\Models\CharityDonate;
use App\Models\JobModel;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CharityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CharityResource::collection(
            executeQuery(Charity::query())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CharityResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'default_amount' => 'nullable|numeric|min:0',
        ]);

        return new CharityResource(Charity::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Charity  $charity
     * @return CharityResource
     */
    public function show(Charity $charity)
    {
        return new CharityResource($charity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Charity  $charity
     * @return bool
     */
    public function update(Request $request, Charity $charity)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'default_amount' => 'nullable|numeric|min:0',
        ]);

        return $charity->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Charity  $charity
     * @return bool
     */
    public function destroy(Charity $charity)
    {
        return $charity->delete();
    }

    public function addDonation(Request $request)
    {
        $data = $request->validate([
            'charity_id' => 'required|exists:charities,id',
            'amount' => 'required|numeric|min:0',
            'job_model_id' => 'required|exists:job_models,id',
        ]);

        $data['date'] = Carbon::now();

        CharityDonate::create($data);

        $job = JobModel::find($data['job_model_id']);
        $job->increment('charity_donate', $data['amount']);

        Transaction::create([
            'date' => Carbon::now(),
            'type' => 'Donate to charity',
            'job_model_id' => $data['job_model_id'],
            'amount' => $data['amount'],
        ]);
    }
}
