<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmtpProvidersTable extends Migration
{
	public $table = 'smtp_providers';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
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
		Schema::drop($this->table);
	}

	public function setTable($table)
	{
	  $this->table = $table;
	}

}
