<?php
namespace Cme\Cli;

use Cme\Helpers\DbUpdate;
use Cme\Helpers\SchemaHelper;
use Cme\Install\InstallTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpgradeDb extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:upgrade-db';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Upgrade Database for CME';

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
    $update = new DbUpdate();

    $installDir = app_path() . '/Cme/Install/';
    //loop through _added
    //and run install class
    foreach($update->modified() as $table => $renames)
    {
      $className   = 'Create' . ucfirst($table) . 'Table';
      $installFile = $installDir . $className . '.php';
      if(file_exists($installFile))
      {
        require_once $installFile;
        $className = "Cme\\Install\\" . $className;
        $m         = new $className;
        if($m instanceof InstallTable)
        {
          $newTable = $table . '-new';
          if(!Schema::hasTable($newTable))
          {
            $this->info("Creating $newTable");
            $m->setTable($newTable);
            $m->up();

            $newColumns = SchemaHelper::getColumnNames($newTable);
            //copy data from old table into new
            do
            {
              $rows   = DB::select("SELECT * FROM $table LIMIT 1000");
              $insert = [];
              foreach($rows as $row)
              {
                foreach($newColumns as $column)
                {
                  if(property_exists($row, $column))
                  {
                    $insert[$column] = $row->$column;
                  }
                  else
                  {
                    $this->info("New column detected: $column");
                    //new column detected, is this a field rename?
                    if(isset($renames[$column]))
                    {
                      $oldField = $renames[$column];
                      if(isset($row->$oldField))
                      {
                        $insert[$column] = $row->$oldField;
                      }
                    }
                    else
                    {
                      $this->info("no rename found for $column");
                    }
                  }
                }

                DB::table($newTable)->insert($insert);
              }
              break;
            }
            while($rows);
            //drop old table
            Schema::drop($table);
            //rename new table
            Schema::rename($newTable, $table);
          }
          else
          {
            $this->error("$newTable already exists");
            Schema::drop($newTable);
          }
        }
      }
      else
      {
        $this->error("Could not find $installFile");
      }
    }
  }
}
