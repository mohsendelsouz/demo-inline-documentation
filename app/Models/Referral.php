<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Referral extends Model
{
    use HasFactory, PowerJoins;

    protected $guarded = [];

    protected $dates = ['date'];

    public function job()
    {
        return $this->belongsTo(JobModel::class, 'job_model_id');
    }
}
