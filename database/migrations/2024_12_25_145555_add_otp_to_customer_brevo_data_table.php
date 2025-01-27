<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpToCustomerBrevoDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customre_brevo_data', function (Blueprint $table) {
            $table->string('otp')->nullable();
            $table->timestamp('otp_expiry')->nullable();
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
            $table->dropColumn('otp');
            $table->dropColumn('otp_expiry');
        });
    }
}
