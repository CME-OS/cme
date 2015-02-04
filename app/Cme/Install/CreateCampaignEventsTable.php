<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCampaignEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaign_events', function(Blueprint $table)
		{
			$table->integer('event_id', true);
			$table->integer('campaign_id')->nullable()->default(0)->index('campaign_id');
			$table->integer('list_id')->nullable()->default(0)->index('list_id');
			$table->integer('subscriber_id')->nullable()->default(0)->index('subscriber_id');
			$table->enum('event_type', array('queued','failed','sent','opened','bounced','unsubscribed','clicked'))->nullable()->index('event_type');
			$table->string('reference', 500)->nullable();
			$table->integer('time')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaign_events');
	}

}
