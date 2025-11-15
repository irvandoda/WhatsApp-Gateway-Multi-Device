<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            // add wh_read, wh_typing , delay,set_available,gptkey,geminikey,reject_call
            $table->boolean('wh_read')->default(0);
            $table->boolean('wh_typing')->default(0);
            $table->integer('delay')->default(0);
            $table->boolean('set_available')->default(0);
            $table->string('gptkey')->nullable();
            $table->string('geminikey')->nullable();
            $table->boolean('reject_call')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            //
        });
    }
}
