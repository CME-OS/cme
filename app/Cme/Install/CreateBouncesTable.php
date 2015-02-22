<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBouncesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bounces', function(Blueprint $table)
		{
			$table->string('email', 200)->primary();
			$table->integer('campaign_id')->nullable()->index('campaign_id');
			$table->integer('time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bounces');
	}

}
