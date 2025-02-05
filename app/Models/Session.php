<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $fillable = ['session_date', 'session_type', 'program_id','reason', 'new_user','counselor_id','department_id'];
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function counselor()
    {
        return $this->hasOne(Counselor::class,'id','counselor_id');
    }
}
