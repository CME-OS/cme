<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCampaignEventsTable extends Migration
{
	public $table = 'campaign_events';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
		{
			$table->increments('event_id');
			$table->integer('campaign_id')->nullable()->default(0)->index('campaign_id');
			$table->integer('list_id')->nullable()->default(0)->index('list_id');
			$table->integer('subscriber_id')->nullable()->default(0)->index('subscriber_id');
			$table->enum('event_type', array('queued','failed','sent','opened','bounced','unsubscribed','clicked','test'))->nullable()->index('event_type');
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
		Schema::drop($this->table);
	}

	public function setTable($table)
	{
	  $this->table = $table;
	}

}
