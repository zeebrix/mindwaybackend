<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customre_brevo_data_id')->nullable();
            $table->foreign('customre_brevo_data_id')->references('id')->on('customre_brevo_data')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('counselor_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->integer('request_days');
            $table->text('reasons');
            $table->text('status');
            $table->date('request_date')->nullable();
            $table->date('denied_date')->nullable();
            $table->date('accepted_date')->nullable();
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
        Schema::dropIfExists('request_sessions');
    }
}
