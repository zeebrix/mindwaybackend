<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Counselor extends Model
{
    use HasFactory , Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
        'gender',
        'timezone',
        'specialization',
        'communication_method',
        'google_webhook_channel_id',
        'google_webhook_resource_id',
        'google_webhook_expiration'
    ];
    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }
    public function CounsellingSession()
    {
        return $this->hasMany(CounsellingSession::class);
    }
    public function slots()
    {
        return $this->hasMany(Slot::class)->where('start_time', '>', now()->utc()->toDateString());
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function googleToken()
    {
        return $this->hasOne(GoogleToken::class, 'counseller_id', 'id');
    }


}
