<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmtpProvidersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('smtp_providers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 250)->default('0');
			$table->string('host', 250)->default('0');
			$table->string('username', 250)->default('0');
			$table->string('password', 250)->default('0');
			$table->integer('port')->default(0);
			$table->integer('default')->default(0);
			$table->integer('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('smtp_providers');
	}

}
