<?php
namespace Cme\Cli;

use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Input\InputArgument;

class UninstallDb extends CmeDbCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:uninstall-db';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Uninstall Database for CME';

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
    $table = $this->argument('table');
    if($table)
    {
      $className = ucwords(camel_case('create_'.$table.'_table'));
      $classFile = implode(
        DIRECTORY_SEPARATOR,
        [app_path(), 'Cme', 'Install', $className.'.php']
      );
      if(file_exists($classFile))
      {
        require_once $classFile;
        $classes[] = $className;
      }
      else
      {
        $this->error("Could not class file for table: ".$table);
      }
    }
    else
    {
      $classes = $this->getMigrationClasses();
    }

    foreach($classes as $migrationClass)
    {
      $m = new $migrationClass;
      if($m instanceof Migration)
      {
        $m->down();
      }
    }
  }

  protected function getArguments()
  {
    return array(
      array('table', InputArgument::OPTIONAL, 'Table Name'),
    );
  }
}
