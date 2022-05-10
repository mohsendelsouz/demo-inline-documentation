<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashDepositResource;
use App\Models\CashDeposit;
use Illuminate\Http\Request;

class CashDepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CashDepositResource::collection(
            executeQuery(CashDeposit::query())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CashDepositResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255'
        ]);

        return new CashDepositResource(CashDeposit::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CashDeposit  $cashDeposit
     * @return CashDepositResource
     */
    public function show(CashDeposit $cashDeposit)
    {
        return new CashDepositResource($cashDeposit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CashDeposit  $cashDeposit
     * @return bool
     */
    public function update(Request $request, CashDeposit $cashDeposit)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255'
        ]);

        return $cashDeposit->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CashDeposit  $cashDeposit
     * @return bool
     */
    public function destroy(CashDeposit $cashDeposit)
    {
        return $cashDeposit->delete();
    }
}
