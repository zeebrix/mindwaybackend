<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCounsellingsessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counsellingsession', function (Blueprint $table) {
            $table->unsignedBigInteger('customre_brevo_data_id')->nullable();

            // Adding the foreign key constraint
            $table->foreign('customre_brevo_data_id')->references('id')->on('customre_brevo_data')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counsellingsession', function (Blueprint $table) {
            $table->dropForeign(['customre_brevo_data_id']);
            $table->dropColumn('customre_brevo_data_id');
        });
    }
}
