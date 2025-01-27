<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAudio extends Model
{
    use HasFactory;

    protected $fillable = ['audio','session_id','audio_title','duration','total_play','course_order_by'];


}
