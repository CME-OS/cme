<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImportQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('import_queue', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('list_id')->default(0);
			$table->enum('type', array('api','csv','file'));
			$table->string('source', 225)->nullable();
			$table->string('locked_by', 225)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('import_queue');
	}

}
