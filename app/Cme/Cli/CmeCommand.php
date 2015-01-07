<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Cli;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

abstract class CmeCommand extends Command
{
  protected function _createPIDFile()
  {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, get_class($this));
    $monitDir = implode(
      DIRECTORY_SEPARATOR,
      [storage_path(), 'monit', $className]
    );
    $fileName = implode(
      DIRECTORY_SEPARATOR,
      [$monitDir, $this->argument('inst') . '.pid']
    );

    //create log file
    if(!File::exists($monitDir))
    {
      File::makeDirectory($monitDir, $mode = 0777, true);
    }

    File::put($fileName, getmypid());
  }
}
