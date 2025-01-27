<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'counselor_id',
        'slot_id',
        'status',
        'communication_method',
        'event_id',
        'meeting_link'
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class,'user_id','id');
    }
    public function brevoUser()
    {
        return $this->belongsTo(CustomreBrevoData::class,'user_id','app_customer_id');
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }
}