<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
             $table->enum('program_type', [0,1,2])->default(1);
            $table->unsignedBigInteger('admin_id')->nullable();
            // 0 - Deactivated --- 1 - active ---- 2 - On trial

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('program_type');
        });
    }
}
