<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->string('subject', 200)->nullable();
			$table->text('message', 65535)->nullable();
			$table->string('recipient', 50)->nullable()->index('recipient');
			$table->enum('status', array('Read','Unread'))->nullable()->index('status');
			$table->integer('time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notifications');
	}

}
