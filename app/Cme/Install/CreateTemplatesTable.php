<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTemplatesTable extends Migration
{
	public $table = 'templates';

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
			$table->string('name', 500);
			$table->text('content', 65535)->nullable();
			$table->string('screenshot', 500)->nullable();
			$table->integer('created');
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
