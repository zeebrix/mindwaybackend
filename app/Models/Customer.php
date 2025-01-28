<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens,
        Notifiable;

    use HasFactory;

    protected $guard = 'customers';
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'improve',
        'goal_id',
        'notify_time',
        'notify_day',
        'verification_code',
        'verified_at',
        'api_auth_token',
        'and_device_id',
        'ios_device_id',
        'status',
        'max_session',
        'deleted_at',
        'created_at',
        'updated_at',
        'gender_preference',
        'max_session',
        'phone',
        'meditation_experience',
        'nick_name',
        'department_id',
    ];
    
    protected $appends = ['single_program'];
    
    public function getSingleProgramAttribute()
    {
        return $this->Program()->first(); // Returns the first program as an object
    }
    public function reserveSlot()
    {
        return $this->hasOne(Slot::class, 'customer_id', 'id')->whereNotNull('customer_id')->where('is_booked',false);
    }
    public function Program()
    {
        return $this->belongsToMany(Program::class, 'customers_programs', 'customers_id', 'programs_id')->withPivot('session');
    }
    public function relatedPrograms()
    {
        return $this->hasMany(CustomerRelatedProgram::class, 'customer_id');
    }
}
