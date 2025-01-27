<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleCourse extends Model
{
    use HasFactory;
    protected $fillable =['id','title','subtitle','duration','image','color','single_audio','total_play'];

}
