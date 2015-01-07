<?php
namespace Cme\Cli;

use Cme\Helpers\ApiImporter;
use Cme\Helpers\CsvImporter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;

class ListImporter extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'command:list-import';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Consumes import queue to import a list of subscribers';

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
    $this->_createPIDFile();
    $instanceName = $this->_getInstanceName();
    while(true)
    {
      $result = DB::select(
        sprintf(
          "SELECT * FROM import_queue WHERE locked_by='%s' ORDER BY id ASC LIMIT 1",
          $instanceName
        )
      );

      if($result)
      {
        $importRequest = $result[0];
        $importer      = null;
        switch($importRequest->type)
        {
          case 'api':
            $importer = new ApiImporter();
            break;
          case 'csv':
            $importer = new CsvImporter();
            break;
        }

        if($importer !== null)
        {
          echo "Attempting the import" . PHP_EOL;
          $importer->import(
            $importRequest->source,
            $importRequest->list_id
          );
        }
        else
        {
          Log::error("Import Failed. Invalid import type.");
        }

        DB::table('import_queue')
          ->where(['id' => $importRequest->id])
          ->delete();
      }
      else
      {
        //lock a row
        $lockedARow = DB::update(
          "UPDATE import_queue SET locked_by=?
          WHERE locked_by IS NULL ORDER BY id ASC LIMIT 1",
          [$instanceName]
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
