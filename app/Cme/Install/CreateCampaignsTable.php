<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCampaignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaigns', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('subject', 500);
			$table->string('from', 225);
			$table->text('html_content', 65535);
			$table->text('text_content', 65535)->nullable();
			$table->integer('list_id')->default(0);
			$table->integer('brand_id')->default(0);
			$table->integer('send_time');
			$table->integer('send_priority')->default(0);
			$table->enum('status', array('Pending','Queuing','Queued','Sent'))->default('Pending');
			$table->integer('created');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaigns');
	}

}
