<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramMultiLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_multi_logins', function (Blueprint $table) {
            $table->id();

   $table->unsignedBigInteger('customre_brevo_data_id')->nullable();
        $table->foreign('customre_brevo_data_id')->references('id')->on('customre_brevo_data_id')->onDelete('cascade')->onUpdate('cascade');

        $table->unsignedBigInteger('program_id')->nullable();
        $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade')->onUpdate('cascade');
        $table->string('password')->default('Test123');

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
        Schema::dropIfExists('program_multi_logins');
    }
}
