<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('course_title')->nullable();
            $table->string('course_description')->nullable();
            $table->string('course_thumbnail')->nullable();
            $table->string('course_duration')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('session_uploads');
    }
}
