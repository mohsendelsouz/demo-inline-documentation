<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return new SettingResource(Setting::first());
    }

    public function truckSettings(Request $request)
    {
        $data = $request->validate([
            'truck_regular_maintenance_millage' => 'required|numeric|min:0',
            'truck_major_maintenance_millage' => 'required|numeric|min:0',
            'truck_inspection' => 'required|in:Weekly,Monthly',
        ]);

        return Setting::first()->update($data);
    }
}
