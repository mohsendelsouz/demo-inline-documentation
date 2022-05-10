<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTechnician extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function job()
    {
        return $this->belongsTo(JobModel::class, 'job_model_id');
    }
}
