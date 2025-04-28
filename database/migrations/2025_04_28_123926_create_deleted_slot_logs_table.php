<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedSlotLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_slot_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slot_id')->nullable();
            $table->string('google_event_id');
            $table->unsignedBigInteger('counselor_id');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->date('date');
            $table->timestamp('deleted_at')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deleted_slot_logs');
    }
}
