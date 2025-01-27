<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCounselorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counselors', function (Blueprint $table) {
            $table->string('gender', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('intake_link')->nullable();
            $table->string('timezone', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->text('specialization')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counselors', function (Blueprint $table) {
              $table->dropColumn([
                'gender',
                'description',
                'intake_link',
                'timezone',
                'avatar',
                'specialization',
            ]);
        });
    }
}
