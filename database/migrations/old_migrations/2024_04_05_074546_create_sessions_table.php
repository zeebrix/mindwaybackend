<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->date('session_date')->nullable(false); // Set session_date as not nullable
            $table->string('session_type')->nullable(false); // Set session_type as not nullable
            $table->unsignedBigInteger('program_id')->nullable(false); // Set program_id as not nullable
            $table->foreign('program_id')->references('id')->on('programs');
            $table->text('reason')->nullable(); // Add the reason column
            $table->string('new_user')->nullable(); // Add the new_user column
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
        Schema::dropIfExists('sessions');
    }
}
