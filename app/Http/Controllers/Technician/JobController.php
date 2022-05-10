<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobModelResource;
use App\Models\JobModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index()
    {
        return JobModelResource::collection(executeQuery(
                JobModel::query()->whereHas('technicians', function ($q) {
                    $q->where('technician_id', Auth::user()->id);
                })->with([
                    'company',
                    'technicians' => function($q) {
                        $q->where('technician_id', Auth::user()->id);
                    },
                    'allWows' => function($q) {
                        $q->whereNull('charity_id');
                    }
                ]))
        );
    }
}
