<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleToken extends Model
{
    protected $table = 'google_tokens';
    public $timestamps = false; // Disable timestamps if not used
    protected $fillable = []; // Add fillable fields if necessary
}

?>