<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name")->nullable();
            $table->string("email")->nullable();
            $table->string("password")->nullable();
            $table->string("image")->nullable();
            $table->string("improve")->nullable();
            $table->string("notify_time")->nullable();
            $table->string("notify_day")->nullable();
            $table->string("verification_code")->nullable();
            $table->timestamp("verified_at")->nullable();
            $table->string("api_auth_token")->nullable();
            $table->string("and_device_id")->nullable();
            $table->string("ios_device_id")->nullable();
            $table->boolean("status")->default(false);
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
        Schema::dropIfExists('customers');
    }
}
