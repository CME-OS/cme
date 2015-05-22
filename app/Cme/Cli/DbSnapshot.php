<?php
namespace Cme\Cli;

use Cme\Lib\Cli\CmeCommand;
use Illuminate\Support\Facades\DB;

class DbSnapshot extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:db-snapshot';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Take snapshot of database';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    $snapshotFile = implode(
      DIRECTORY_SEPARATOR,
      [base_path(), 'snapshot.json']
    );

    $result = DB::select(
      "SHOW TABLES FROM cme"
    );

    $json = [];
    foreach($result as $obj)
    {
      $table = $obj->Tables_in_cme;
      if(!starts_with($table, 'list_'))
      {
        //get create code
        $columns = DB::select("SHOW COLUMNS FROM $table");
        //var_dump($columns); die;
        $columnsHash = [];
        foreach($columns as $columnObj)
        {
          $columnsHash[$columnObj->Field] = md5(json_encode($columnObj));
        }

        $json[$table] = $columnsHash;
      }
    }

    echo "Creating snapshot file...".PHP_EOL;
    file_put_contents($snapshotFile, json_encode($json));
  }
}
