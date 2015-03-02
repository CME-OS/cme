<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
	public $table = 'users';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email', 100);
			$table->string('password', 60);
			$table->smallInteger('active');
			$table->softDeletes();
			$table->string('remember_token', 100)->nullable();
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
		Schema::drop($this->table);
	}

	public function setTable($table)
	{
	  $this->table = $table;
	}

}
