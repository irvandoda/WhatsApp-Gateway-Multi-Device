<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
	{
		Schema::table('devices', function (Blueprint $table) {
			$table->string('bexa_company_website')->nullable();
			$table->string('bexa_company_address')->nullable();
			$table->string('bexa_company_phone')->nullable();
			$table->string('bexa_company_email')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('devices', function (Blueprint $table) {
			$table->dropColumn([
				'bexa_company_website',
				'bexa_company_address',
				'bexa_company_phone',
				'bexa_company_email',
			]);
		});
	}
};
