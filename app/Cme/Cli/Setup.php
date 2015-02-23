<?php
namespace Cme\Cli;

use Illuminate\Support\Str;

class Setup extends CmeDbCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:setup';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Install and Setup CME';

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
    $env = $this->option('env');
    if($env === null)
    {
      $this->error("You must specifiy an environment to install CME on.");
      $this->info("Type  php artisan --env=[ENV] cme:setup instead.");
      $this->info("Where [ENV] is your environment");
    }
    else
    {
      $this->info(
        "Welcome to CME Setup. This tool will help you install CME on your system"
      );
      $this->info(
        "Before proceeding please ensure that you have ran composer update"
      );

      $this->info(
        "You are installing CME on " . $this->option('env') . ' Environment'
      );
      $this->info("===================");
      $this->info("=    CME DOMAIN   =");
      $this->info("===================");
      $domain = $this->ask(
        "Enter CME domain as specified in vhost e.g cme.domain.com: "
      );

      $this->info("\n===================");
      $this->info("=   CME DATABASE  =");
      $this->info("===================");
      $this->info(
        "Before proceeding, please ensure you have created a database for this installation"
      );
      $this->ask("Press any key if you have already done this");

      $dbDetailsAreWrong = true;
      do
      {
        $dbName     = $this->ask(
          "Enter CME database name. If blank, defaults to cme: ",
          'cme'
        );
        $dbHost     = $this->ask(
          "Enter CME database host. If blank, defaults to localhost: ",
          'localhost'
        );
        $dbUser     = $this->ask(
          "Enter CME database user. If blank, defaults to root: ",
          'root'
        );
        $dbPassword = $this->ask(
          "Enter CME database password. If blank, defaults to no password: ",
          ''
        );
        //test db connection
        if(mysql_connect($dbHost, $dbUser, $dbPassword))
        {
          $dbDetailsAreWrong = false;
        }
        else
        {
          $this->error(
            "I can't seem to connect to your database. " . "Please check that you have entered the right details"
          );
          $this->ask("Press any key to retry");
        }
      }
      while($dbDetailsAreWrong);

      $this->info("\n===================");
      $this->info("=    CME AWS      =");
      $this->info("===================");
      $awsKey    = $this->ask("Enter AWS key. Leave blank to skip: ", '');
      $awsSecret = $this->ask("Enter AWS secret. Leave blank to skip: ", '');
      $awsRegion = $this->ask("Enter AWS region. Leave blank to skip: ", '');

      $key = Str::random(32);

      $envFile = ".env";
      if($env !== 'production')
      {
        $envFile = ".env." . $env;
      }

      $envFile = strtolower($envFile . '.php');

      $this->info("Welldone! I am now generating your env file - " . $envFile);
      $template = $this->_getEvnFileTemplate();
      $template = str_replace('[DOMAIN]', $domain, $template);
      $template = str_replace('[HOST]', $dbHost, $template);
      $template = str_replace('[DATABASE]', $dbName, $template);
      $template = str_replace('[USERNAME]', $dbUser, $template);
      $template = str_replace('[PASSWORD]', $dbPassword, $template);
      $template = str_replace('[AWS_KEY]', $awsKey, $template);
      $template = str_replace('[AWS_SECRET]', $awsSecret, $template);
      $template = str_replace('[AWS_REGION]', $awsRegion, $template);
      $template = str_replace('[KEY]', $key, $template);

      file_put_contents($envFile, $template);

      //install db
      $this->info("Installing Database");
      $this->call('cme:install-db');

      //create user account
      $this->info("Creating a user account");
      $this->call(
        'cme:create-user',
        ['username' => 'admin', 'password' => 'admin']
      );
      $this->info("Username: admin");
      $this->info("Password: admin");
      $this->info(
        "Please make sure you create a different user and delete this one"
      );
    }
  }


  private function _getEvnFileTemplate()
  {
    $template = "<?php

return array(
  'domain'           => '[DOMAIN]',
  'mysql'            => array(
    'driver'    => 'mysql',
    'host'      => '[HOST]',
    'database'  => '[DATABASE]',
    'username'  => '[USERNAME]',
    'password'  => '[PASSWORD]',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => ''
  ),
  'key'              => '[KEY]',
  'cipher'           => MCRYPT_RIJNDAEL_128,
  'aws_key'          => '[AWS_KEY]',
  'aws_secret'       => '[AWS_SECRET]',
  'aws_region'       => '[AWS_REGION]',
);
";
    return $template;
  }
}
