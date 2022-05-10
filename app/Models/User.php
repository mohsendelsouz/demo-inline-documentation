<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public static function boot()
    {
        parent::boot();
        static::created(function($user)
        {
            Goal::create([
                'user_id' => $user->id,
                'date' => Carbon::now()->startOfWeek(Carbon::SUNDAY),
                'end_date' => Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(6),
                'week' => Carbon::now()->startOfWeek(Carbon::SUNDAY)->weekOfYear,
                'scorecard_goal' => $user->scorecard_goal ?? 0,
                'wows_goal' => $user->wows_goal ?? 0,
                'pay_goal' => $user->production_goal ?? 0,
                'job_goal' => $user->job_goal ?? 0,
            ]);
        });

        static::deleting(function($user)
        {
            $user->email = $user->email.'_'.random_int(0, 9999999);
            $user->save();
        });
    }
}
