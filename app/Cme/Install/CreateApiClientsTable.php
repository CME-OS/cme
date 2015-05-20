<?php
namespace Cme\Install;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiClientsTable extends InstallTable
{
	public $table = 'api_clients';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
		{
			$table->integer('client_id', true);
			$table->string('client_name', 50)->nullable();
			$table->string('client_key', 50)->nullable();
			$table->string('client_secret', 50)->nullable();
			$table->integer('time')->nullable();
			$table->index(['client_key','client_secret'], 'client_key_client_secret');
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
