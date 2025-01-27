<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->boolean('is_booked')->default(false);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('slots');
    }
};