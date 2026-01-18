<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBexaFieldsToDevicesTable extends Migration
{
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('bexa_api_key')->nullable();
            $table->string('bexa_name')->nullable();
            $table->string('bexa_company_name')->nullable();
            $table->boolean('bexa_custom')->default(false);
            $table->string('bexa_language')->nullable();
            $table->string('bexa_function')->nullable();
            $table->string('bexa_industry')->nullable();
            $table->string('bexa_product_input_type')->nullable();
            $table->text('bexa_product_link')->nullable();
            $table->json('bexa_products')->nullable();
            $table->boolean('bexa_system_custom_instructions')->default(false);
            $table->text('bexa_system_instructions')->nullable();
        });
    }

    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn([
                'bexa_api_key',
                'bexa_name',
                'bexa_company_name',
                'bexa_custom',
                'bexa_language',
                'bexa_function',
                'bexa_industry',
                'bexa_product_input_type',
                'bexa_product_link',
                'bexa_products',
                'bexa_system_custom_instructions',
                'bexa_system_instructions'
            ]);
        });
    }
}
