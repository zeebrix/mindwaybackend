<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SleepAudio extends Model
{
    use HasFactory;
    protected $fillable = ['audio','course_id','duration','title','image','color','description','total_play'];
}
