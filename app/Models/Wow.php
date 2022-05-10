<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wow extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function charity()
    {
        return $this->belongsTo(Charity::class);
    }

    public function job()
    {
        return $this->belongsTo(JobModel::class, 'job_model_id');
    }
}
