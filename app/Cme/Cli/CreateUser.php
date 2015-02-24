<?php
namespace Cme\Cli;

use Cme\Helpers\InstallerHelper;
use Symfony\Component\Console\Input\InputArgument;

class CreateUser extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:create-user';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create user for CME';

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
    $username = $this->argument('username');
    $password = $this->argument('password');

    InstallerHelper::createUser($username, $password);

    $this->info("User $username created successfully!");
  }

  protected function getArguments()
  {
    return array(
      array('username', InputArgument::REQUIRED, 'User Name'),
      array('password', InputArgument::REQUIRED, 'Password'),
    );
  }
}
