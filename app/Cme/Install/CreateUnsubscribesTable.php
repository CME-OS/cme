<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUnsubscribesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('unsubscribes', function(Blueprint $table)
		{
			$table->string('email', 200)->primary();
			$table->integer('brand_id')->nullable()->index('brand_id');
			$table->integer('campaign_id')->nullable()->index('campaign_id');
			$table->integer('list_id')->nullable()->index('list_id');
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
		Schema::drop('unsubscribes');
	}

}
