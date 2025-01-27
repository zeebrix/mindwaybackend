<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounsellingSession extends Model
{
    use HasFactory;

    protected $table = 'counsellingsession';

    protected $fillable = [
        'session_date',
        'session_type',
        'reason',
        'new_user',
        'program_id',
        'company_name',
        'name',
        'email',
        'max_session',
        'created_at',
        'counselor_id'
    ];
    public function counselor()
    {
        return $this->hasOne(Counselor::class,'id','counselor_id');
    }
    public function brevoUser()
    {
        return $this->hasOne(CustomreBrevoData::class,'email','email');
    }
    public $timestamps = false; // Since you have a created_at field already, you may want to manage timestamps manually
}
