<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Resources\MachineResource;
use App\Http\Resources\TruckResource;
use App\Models\Machine;
use App\Models\MachineMaintenance;
use App\Models\Truck;
use App\Models\TruckMaintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function trucks()
    {
        return TruckResource::collection(Truck::all());
    }

    public function machines()
    {
        return MachineResource::collection(Machine::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_type' => 'required|string|in:truck,machine',
            'maintenance_type' => 'required|in:Gas,Regular Service,Major Service,Emergency Service,Supply',
            'equipment' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'millage_hour' => 'required|numeric|min:0'
        ]);

        if ($data['vehicle_type'] === 'truck') {
            $data['truck_id'] = $data['equipment'];
            $data['type'] = $data['maintenance_type'];
            $data['millage'] = $data['millage_hour'];

            unset($data['equipment']);
            unset($data['maintenance_type']);
            unset($data['vehicle_type']);
            unset($data['millage_hour']);

            Truck::where('id', $data['truck_id'])->update([
                'millage' => $data['millage']
            ]);

            TruckMaintenance::create($data);
        } else {
            $data['machine_id'] = $data['equipment'];
            $data['type'] = $data['maintenance_type'];
            $data['hour'] = $data['millage_hour'];

            unset($data['equipment']);
            unset($data['maintenance_type']);
            unset($data['vehicle_type']);
            unset($data['millage_hour']);

            Machine::where('id', $data['machine_id'])->update([
                'hour' => $data['hour']
            ]);

            MachineMaintenance::create($data);
        }

        return true;
    }
}
