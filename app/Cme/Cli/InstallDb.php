<?php
namespace Cme\Cli;

use Illuminate\Database\Migrations\Migration;

class InstallDb extends CmeDbCommand
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
    $classes = $this->getMigrationClasses();
    foreach($classes as $migrationClass)
    {
      $m = new $migrationClass;
      if($m instanceof Migration)
      {
        $m->up();
      }
    }
  }
}
