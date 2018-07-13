<?php
namespace App\Cme\Install;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportQueueTable extends InstallTable
{
	public $table = 'import_queue';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('list_id')->default(0);
			$table->enum('type', array('api','csv','file'));
			$table->string('source', 225)->nullable();
			$table->string('locked_by', 225)->nullable();
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
