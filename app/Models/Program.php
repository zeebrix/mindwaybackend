<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Program extends Authenticatable
{
    use HasApiTokens,
        Notifiable;

   use HasFactory;
    protected $guard = 'programs';
    protected $fillable = [
    'company_name',
    'email',
    'password',
    'max_lic',
    'code',
    'logo',
    'link',
    'max_session'
];
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customers_programs','programs_id', 'customers_id')->withPivot('session');
    }
    public function programPlan()
    {
        return $this->hasOne(ProgramPlan::class,'program_id','id');
    }
    
    public function programDepartment()
    {
        return $this->hasMany(ProgramDepartment::class,'program_id','id');
    }
    
}
