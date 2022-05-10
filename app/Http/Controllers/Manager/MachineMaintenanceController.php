<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\MachineMaintenanceResource;
use App\Models\Machine;
use App\Models\MachineMaintenance;
use Illuminate\Http\Request;

class MachineMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return MachineMaintenanceResource::collection(executeQuery(MachineMaintenance::query()->with('machine')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return MachineMaintenanceResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'machine_id' => 'required|integer|exists:machines,id',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'type' => 'required|string|in:Gas,Regular Service,Major Service,Emergency Service,Supply',
            'amount' => 'required|numeric|min:0',
            'hour' => 'required|numeric|min:0'
        ]);

        Machine::where('id', $data['machine_id'])->update([
            'hour' => $data['hour']
        ]);

        return new MachineMaintenanceResource(MachineMaintenance::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MachineMaintenance  $machineMaintenance
     * @return MachineMaintenanceResource
     */
    public function show(MachineMaintenance $machinesMaintenance)
    {
        return new MachineMaintenanceResource($machinesMaintenance->load('machine'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MachineMaintenance  $machineMaintenance
     * @return bool
     */
    public function update(Request $request, MachineMaintenance $machinesMaintenance)
    {
        $data = $request->validate([
            'machine_id' => 'required|integer|exists:machines,id',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'type' => 'required|string|in:Gas,Regular Service,Major Service,Emergency Service,Supply',
            'amount' => 'required|numeric|min:0',
            'hour' => 'required|numeric|min:0'
        ]);

        return $machinesMaintenance->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MachineMaintenance  $machineMaintenance
     * @return bool
     */
    public function destroy(MachineMaintenance $machinesMaintenance)
    {
        return $machinesMaintenance->delete();
    }
}
