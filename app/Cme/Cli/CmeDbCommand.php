<?php
namespace Cme\Cli;

use Cme\Helpers\ApiImporter;
use Cme\Helpers\CsvImporter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;

abstract class CmeDbCommand extends CmeCommand
{

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function getMigrationClasses()
  {
    $installPath = implode(
      DIRECTORY_SEPARATOR,
      [app_path(), 'Cme', 'Install', '']
    );

    $files = File::glob($installPath.'*.php');
    $classes = [];
    foreach($files as $file)
    {
      require_once $file;
      $className = str_replace('.php','',basename($file));
      echo $className.PHP_EOL;
      $classes[] = $className;
    }

    return $classes;
  }
}
