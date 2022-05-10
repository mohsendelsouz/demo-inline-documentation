<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\TruckResource;
use App\Models\Setting;
use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TruckResource::collection(executeQuery(Truck::query()->with('machine')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return TruckResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'machine_id' => 'nullable|integer|exists:machines,id',
            'millage' => 'required|numeric|min:0'
        ]);

        return new TruckResource(Truck::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Truck  $truck
     * @return TruckResource
     */
    public function show(Truck $truck)
    {
        return new TruckResource($truck->load('machine'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Truck  $truck
     * @return bool
     */
    public function update(Request $request, Truck $truck)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'machine_id' => 'nullable|integer|exists:machines,id',
            'millage' => 'required|numeric|min:0'
        ]);

        return $truck->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Truck  $truck
     * @return bool
     */
    public function destroy(Truck $truck)
    {
        return $truck->delete();
    }
}
