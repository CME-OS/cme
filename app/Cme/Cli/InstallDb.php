<?php
namespace Cme\Cli;

use Cme\Helpers\InstallerHelper;
use Cme\Lib\Cli\CmeCommand;
use Symfony\Component\Console\Input\InputArgument;

class InstallDb extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:install-db';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Install Database for CME';

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
    $table   = $this->argument('table');
    $classes = [];
    if($table)
    {
      $className = ucwords(camel_case('create_' . $table . '_table'));
      $classFile = implode(
        DIRECTORY_SEPARATOR,
        [app_path(), 'Cme', 'Install', $className . '.php']
      );
      if(file_exists($classFile))
      {
        require_once $classFile;
        $classes[] = $className;
      }
      else
      {
        $this->error("Could not class file for table: " . $table);
      }
    }
    else
    {
      $classes = InstallerHelper::getInstallClasses();
    }

    InstallerHelper::installDb($classes);
  }

  protected function getArguments()
  {
    return array(
      array('table', InputArgument::OPTIONAL, 'Table Name'),
    );
  }
}
