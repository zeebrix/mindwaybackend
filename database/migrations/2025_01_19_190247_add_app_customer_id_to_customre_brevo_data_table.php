<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppCustomerIdToCustomreBrevoDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customre_brevo_data', function (Blueprint $table) {
            $table->bigInteger('app_customer_id')->nullable();
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
            $table->dropColumn('app_customer_id');
        });
    }
}
