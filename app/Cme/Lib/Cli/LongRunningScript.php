<?php
/**
 * @author  oke.ugwu
 */

namespace App\Cme\Lib\Cli;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;

abstract class LongRunningScript extends CmeCommand
{
  protected $_cronMode;
  protected $_monitDir;
  protected $_pidFileName;

  protected function _init()
  {
    $className          = str_replace(
      '\\',
      DIRECTORY_SEPARATOR,
      get_class($this)
    );
    $this->_monitDir    = implode(
      DIRECTORY_SEPARATOR,
      [storage_path(), 'monit', $className]
    );
    $this->_pidFileName = implode(
      DIRECTORY_SEPARATOR,
      [$this->_monitDir, $this->argument('inst') . '.pid']
    );
    $this->_cronMode    = ($this->option('cron-mode') == 'true');
    if($this->_cronMode && File::exists($this->_pidFileName))
    {
      die('Exiting a process is already running');
    }
  }

  protected function _cronBailOut()
  {
    if($this->_cronMode)
    {
      $this->info("Done - Cron Mode");
      //delete the PID file so that another process can be started by crontab
      unlink($this->_pidFileName);
      die;
    }
  }

  protected function _createPIDFile()
  {
    //create log file
    if(!File::exists($this->_monitDir))
    {
      File::makeDirectory($this->_monitDir, $mode = 0777, true);
    }

    File::put($this->_pidFileName, getmypid());
  }

  protected function getOptions()
  {
    return [
      [
        'cron-mode',
        'c',
        InputOption::VALUE_OPTIONAL,
        'Set to true if script is run by crontab'
      ]
    ];
  }
}
