<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ranges', function(Blueprint $table)
		{
			$table->integer('list_id');
			$table->integer('campaign_id');
			$table->integer('start');
			$table->integer('end')->nullable();
			$table->string('locked_by', 225)->nullable();
			$table->integer('created')->nullable();
			$table->primary(['list_id','campaign_id','start']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ranges');
	}

}
