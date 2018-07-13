<?php
namespace App\Cme\Install;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnsubscribesTable extends InstallTable
{
	public $table = 'unsubscribes';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
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
		Schema::drop($this->table);
	}

	public function setTable($table)
	{
	  $this->table = $table;
	}

}
