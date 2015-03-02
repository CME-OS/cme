<?php
namespace Cme\Install;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRangesTable extends InstallTable
{
	public $table = 'ranges';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
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
		Schema::drop($this->table);
	}

	public function setTable($table)
	{
	  $this->table = $table;
	}

}
