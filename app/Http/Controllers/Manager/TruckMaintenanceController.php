<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\TruckMaintenanceResource;
use App\Models\Truck;
use App\Models\TruckMaintenance;
use Illuminate\Http\Request;

class TruckMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Truck  $truck
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TruckMaintenanceResource::collection(executeQuery(TruckMaintenance::query()->with('truck')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Truck  $truck
     * @return TruckMaintenanceResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'truck_id' => 'required|integer|exists:trucks,id',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'type' => 'required|string|in:Gas,Regular Service,Major Service,Emergency Service,Supply',
            'amount' => 'required|numeric|min:0',
            'millage' => 'required|numeric|min:0'
        ]);

        Truck::where('id', $data['truck_id'])->update([
            'millage' => $data['millage']
        ]);

        return new TruckMaintenanceResource(TruckMaintenance::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Truck  $truck
     * @param  \App\Models\TruckMaintenance  $truckMaintenance
     * @return TruckMaintenanceResource
     */
    public function show(TruckMaintenance $trucksMaintenance)
    {
        return new TruckMaintenanceResource($trucksMaintenance->load('truck'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Truck  $truck
     * @param  \App\Models\TruckMaintenance  $truckMaintenance
     * @return bool
     */
    public function update(Request $request, TruckMaintenance $trucksMaintenance)
    {
        $data = $request->validate([
            'truck_id' => 'required|integer|exists:trucks,id',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'type' => 'required|string|in:Gas,Regular Service,Major Service,Emergency Service,Supply',
            'amount' => 'required|numeric|min:0',
            'millage' => 'required|numeric|min:0'
        ]);

        return $trucksMaintenance->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Truck  $truck
     * @param  \App\Models\TruckMaintenance  $truckMaintenance
     * @return bool
     */
    public function destroy(TruckMaintenance $trucksMaintenance)
    {
        return $trucksMaintenance->delete();
    }
}
