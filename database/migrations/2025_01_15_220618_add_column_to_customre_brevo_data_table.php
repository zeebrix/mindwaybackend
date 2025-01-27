<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToCustomreBrevoDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customre_brevo_data', function (Blueprint $table) {
            $table->string('level')->default('member');
            $table->boolean('is_app_user')->default(0);
            $table->boolean('is_counselling_user')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customre_brevo_data', function (Blueprint $table) {
            $table->dropColumn('level');
            $table->dropColumn('is_app_user');
            $table->dropColumn('is_counselling_user');
        });
    }
}
