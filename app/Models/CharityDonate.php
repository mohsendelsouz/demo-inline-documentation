<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharityDonate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    public function job()
    {
        return $this->belongsTo(JobModel::class, 'job_model_id');
    }
}
