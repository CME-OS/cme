<?php
namespace Cme\Cli;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\InputArgument;

class CreateUser extends CmeDbCommand
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

    $data['email']      = $username;
    $data['password']   = Hash::make($password);
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');
    $data['active']     = 1;
    DB::table('users')->insert($data);

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
