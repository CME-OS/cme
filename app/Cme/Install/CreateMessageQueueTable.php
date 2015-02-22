<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_queue', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('subject', 500);
			$table->string('from_email', 225);
			$table->string('from_name', 225);
			$table->string('to', 225)->nullable();
			$table->text('html_content', 65535);
			$table->text('text_content', 65535);
			$table->integer('subscriber_id')->default(0);
			$table->integer('list_id')->default(0)->index('list_id');
			$table->integer('brand_id')->default(0)->index('brand_id');
			$table->integer('campaign_id')->default(0)->index('campaign_id');
			$table->enum('status', array('Pending','Sent','Failed','Paused'))->default('Pending')->index('status');
			$table->integer('send_time')->nullable();
			$table->integer('send_priority')->nullable();
			$table->string('locked_by', 225)->nullable()->index('locked_by');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('message_queue');
	}

}
