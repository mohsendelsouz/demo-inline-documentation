<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class JobModel extends Model
{
    use HasFactory, PowerJoins;

    protected $guarded = [];

    protected $dates = ['date', 'payment_received_at'];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($jobModel)
        {
            $jobModel->technicians()->delete();
            $jobModel->transactions()->delete();
            $jobModel->allWows()->delete();
            $jobModel->referral()->delete();
            $jobModel->donates()->delete();
        });
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function truckTechnician()
    {
        return $this->belongsTo(User::class, 'truck_technician_id');
    }

    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    public function operationalManager()
    {
        return $this->belongsTo(User::class, 'operational_manager_id');
    }

    public function generalManager()
    {
        return $this->belongsTo(User::class, 'general_manager_id');
    }

    public function technicians()
    {
        return $this->hasMany(JobTechnician::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function allWows()
    {
        return $this->hasMany(Wow::class);
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function donates()
    {
        return $this->hasMany(CharityDonate::class);
    }
}
