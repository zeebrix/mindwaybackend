<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedSlotLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'slot_id',
        'google_event_id',
        'counselor_id',
        'start_time',
        'end_time',
        'date',
        'deleted_at',
    ];
}
