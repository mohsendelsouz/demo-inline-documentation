<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\TruckInspection;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function truckInspection(Request $request)
    {
        $data = $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'date' => 'required|date',
            'comment' => 'nullable|string|max:255'
        ]);

        TruckInspection::create($data);

        return Truck::find($data['truck_id'])->update([
            'last_inspection' => $data['date']
        ]);
    }
}
