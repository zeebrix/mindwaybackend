<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomreBrevoData extends Model
{
    protected $table = 'customre_brevo_data';

    protected $fillable = [
        'name',
        'email',
        'program_id',
        'app_customer_id',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'app_customer_id','id');
    }
    public function program()
    {
        return $this->hasOne(Program::class, 'id','program_id');
    }

    public function MultiLoginProgram()
    {
        return $this->hasOne(ProgramMultiLogin::class,'customre_brevo_data_id','id');
    }
}
