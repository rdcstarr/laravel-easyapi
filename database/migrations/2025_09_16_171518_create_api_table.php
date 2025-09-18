<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations
	 */
	public function up()
	{
		Schema::create('api', function (Blueprint $table)
		{
			$table->id();
			$table->string('key', 64)->unique()->index();
			$table->bigInteger('access_count')->default(0);
			$table->timestamps();
		});

		Schema::create('api_logs', function (Blueprint $table)
		{
			$table->id();
			$table->foreignId('api_id')->constrained('api')->onDelete('cascade');
			$table->string('endpoint')->nullable();
			$table->ipAddress()->index();
			$table->string('user_agent')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations
	 */
	public function down(): void
	{
		Schema::dropIfExists('api_logs');
		Schema::dropIfExists('api');
	}
};
