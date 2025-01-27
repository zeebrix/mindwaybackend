<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = [
        'counselor_id',
        'date',
        'start_time',
        'end_time',
        'is_booked',
        'customer_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_booked' => 'boolean',
    ];

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}