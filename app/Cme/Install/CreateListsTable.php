<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListsTable extends Migration
{
	public $table = 'lists';

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
			$table->string('name', 225)->default('0');
			$table->string('description', 225)->default('0');
			$table->string('endpoint', 225)->nullable();
			$table->integer('refresh_interval')->nullable();
			$table->integer('last_refresh_time')->nullable();
			$table->string('locked_by', 225)->nullable();
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
