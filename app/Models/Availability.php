<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;
    protected $fillable = ['counselor_id', 'day', 'available', 'start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }
    
}
