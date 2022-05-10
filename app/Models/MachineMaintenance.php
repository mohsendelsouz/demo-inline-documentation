<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class MachineMaintenance extends Model
{
    use HasFactory, PowerJoins;

    protected $guarded = [];

    protected $dates = ['date'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
