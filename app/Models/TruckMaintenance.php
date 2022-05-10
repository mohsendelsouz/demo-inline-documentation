<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class TruckMaintenance extends Model
{
    use HasFactory, PowerJoins;

    protected $guarded = [];

    protected $dates = ['date'];

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }
}
