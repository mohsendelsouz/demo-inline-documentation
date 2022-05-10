<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Truck extends Model
{
    use HasFactory, SoftDeletes, PowerJoins;

    protected $guarded = [];

    protected $dates = ['last_inspection'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function maintenances()
    {
        return $this->hasMany(TruckMaintenance::class);
    }
}
