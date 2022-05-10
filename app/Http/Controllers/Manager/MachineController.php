<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\MachineResource;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return MachineResource::collection(executeQuery(Machine::query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return MachineResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'hour' => 'required|numeric|min:0'
        ]);

        return new MachineResource(Machine::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Machine  $machine
     * @return MachineResource
     */
    public function show(Machine $machine)
    {
        return new MachineResource($machine);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Machine  $machine
     * @return bool
     */
    public function update(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'hour' => 'required|numeric|min:0'
        ]);

        return $machine->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Machine  $machine
     * @return bool
     */
    public function destroy(Machine $machine)
    {
        return $machine->delete();
    }
}
