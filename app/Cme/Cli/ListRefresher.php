<?php
namespace Cme\Cli;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;

class ListRefresher extends Command
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'command:list-refresh';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Checks lists table for list that need to be refreshed';

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
    $className    = get_class($this);
    $instanceName = $this->_getInstanceName();
    $monitDir     = storage_path() . '/monit/' . $className;
    $fileName     = $monitDir . '/' . $this->argument('inst') . '.pid';
    //create log file
    if(!File::exists($monitDir))
    {
      File::makeDirectory($monitDir, $mode = 0777, true);
    }

    File::put($fileName, getmypid());

    while(true)
    {
      $result = DB::select(
        sprintf(
          "SELECT * FROM lists
          WHERE refresh_interval IS NOT NULL
          AND locked_by='%s'
          ORDER BY id ASC LIMIT 1",
          $instanceName
        )
      );

      if($result)
      {
        $refreshRequest = $result[0];
        $importer       = new ApiImporter();
        $this->info("Refreshing " . $refreshRequest->name . " List");
        $importer->import(
          $refreshRequest->endpoint,
          $refreshRequest->id
        );

        DB::update(
          "UPDATE lists SET locked_by=NULL, last_refresh_time=?
          WHERE id=? ",
          [time(), $refreshRequest->id]
        );
      }
      else
      {
        //only lock list that is due/overdue for a refresh
        $lockedARow = DB::update(
          "UPDATE lists SET locked_by=?
          WHERE locked_by IS NULL
          AND refresh_interval IS NOT NULL
          AND
          (
            (last_refresh_time IS NULL)
            OR (last_refresh_time + refresh_interval <= ?)
          )
          ORDER BY id ASC LIMIT 1",
          [$instanceName, time()]
        );
        if(!$lockedARow)
        {
          $this->info("sleeping for a bit");
          sleep(2);
        }
      }
    }
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('inst', InputArgument::REQUIRED, 'Instance Name'),
    );
  }

  private function _getInstanceName()
  {
    $inst = $this->argument('inst');
    return gethostname() . '-' . $inst;
  }
}
