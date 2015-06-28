<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Helpers;

use Cme\Install\InstallTable;
use CmeData\UserData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Exceptions\InvalidDataException;
use Illuminate\Config\EnvironmentVariables;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class InstallerHelper
{
  public static $domain;
  public static $dbName;
  public static $dbHost;
  public static $dbUser;
  public static $dbPassword;
  public static $awsKey;
  public static $awsSecret;
  public static $awsRegion;
  private static $_key;

  private static function _getInstallFile()
  {
    return implode(
      DIRECTORY_SEPARATOR,
      [base_path(), 'installed.json']
    );
  }

  public static function isCMEInstalled()
  {
    $installFile = self::_getInstallFile();
    return file_exists($installFile);
  }

  public static function writeInstallFlag()
  {
    $installFile = self::_getInstallFile();
    file_put_contents($installFile, "{}");
    $readyFile = implode(DIRECTORY_SEPARATOR, [base_path(), 'ready']);
    if(file_exists($readyFile))
    {
      unlink($readyFile);
    }
  }

  /**
   * Get Install Classes
   *
   * @return mixed
   */
  public static function getInstallClasses()
  {
    $installPath = implode(
      DIRECTORY_SEPARATOR,
      [app_path(), 'Cme', 'Install', '']
    );

    $files   = glob($installPath . '*.php');
    $classes = [];
    foreach($files as $file)
    {
      if(basename($file) != 'InstallTable.php')
      {
        require_once $file;
        $className = str_replace('.php', '', basename($file));
        $classes[] = $className;
      }
    }

    return $classes;
  }

  /**
   * @param array $classes
   */
  public static function installDb($classes)
  {
    foreach($classes as $installClass)
    {
      $installClass = "Cme\\Install\\" . $installClass;
      $m            = new $installClass;
      if($m instanceof InstallTable)
      {
        $m->up();
      }
    }
  }

  /**
   * @param array $classes
   */
  public static function unInstallDb($classes)
  {
    foreach($classes as $installClass)
    {
      $installClass = "Cme\\Install\\" . $installClass;
      $m            = new $installClass;
      if($m instanceof InstallTable)
      {
        $m->down();
      }
    }
  }

  /**
   * @param string $username
   * @param string $password
   */
  public static function createUser($username, $password)
  {
    $user           = new UserData();
    $user->email    = $username;
    $user->password = $password;
    try
    {
      CmeKernel::User()->create($user);
    }
    catch(InvalidDataException $e)
    {
      $message = "";
      foreach($user->getValidationErrors() as $error)
      {
        $message .= $error->message . PHP_EOL;
      }

      throw new \Exception($message);
    }
  }

  /**
   * $env could be any of the following: development|stage|production
   *
   * @param string $env
   */
  public static function createEnvFile($env)
  {
    $envFile = ".env";
    if($env !== 'production')
    {
      $envFile = ".env." . $env;
    }

    $envFile = implode(
      DIRECTORY_SEPARATOR,
      [base_path(), strtolower($envFile . '.php')]
    );

    self::$_key = Str::random(32);
    $template   = self::_getEvnFileTemplate();
    $template   = str_replace('[DOMAIN]', self::$domain, $template);
    $template   = str_replace('[HOST]', self::$dbHost, $template);
    $template   = str_replace('[DATABASE]', self::$dbName, $template);
    $template   = str_replace('[USERNAME]', self::$dbUser, $template);
    $template   = str_replace('[PASSWORD]', self::$dbPassword, $template);
    $template   = str_replace('[AWS_KEY]', self::$awsKey, $template);
    $template   = str_replace('[AWS_SECRET]', self::$awsSecret, $template);
    $template   = str_replace('[AWS_REGION]', self::$awsRegion, $template);
    $template   = str_replace('[KEY]', self::$_key, $template);

    file_put_contents($envFile, $template);
    self::_reloadEnvConfig($env);
  }

  /**
   * Creates the commander config file, which will be SCPed across to EC2
   * when we deploy commander
   */
  public static function createCommanderConfigFile()
  {
    $configFile = "commander.config.php";

    $configFile = implode(
      DIRECTORY_SEPARATOR,
      [base_path(), strtolower($configFile)]
    );

    $template = self::_getCommanderConfigTemplate();
    $template = str_replace('[HOST]', self::$dbHost, $template);
    $template = str_replace('[DATABASE]', self::$dbName, $template);
    $template = str_replace('[USERNAME]', self::$dbUser, $template);
    $template = str_replace('[PASSWORD]', self::$dbPassword, $template);
    $template = str_replace('[KEY]', self::$_key, $template);

    file_put_contents($configFile, $template);
  }

  private static function _reloadEnvConfig($env)
  {
    with(
      $envVariables = new EnvironmentVariables(
        App::getEnvironmentVariablesLoader()
      )
    )->load($env);

    App::instance(
      'config',
      $config = new Repository(
        App::getConfigLoader(), $env
      )
    );
  }

  private static function _getEvnFileTemplate()
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

  private static function _getCommanderConfigTemplate()
  {
    $template = "<?php
return  [
  'database' => [
    'host'     => '[HOST]',
    'database' => '[DATABASE]'
    'username' => '[USERNAME]',
    'password' => '[PASSWORD]',
  ],
  'key' => '[KEY]'
];
";
    return $template;
  }

  private static function _getBackgroundProcesses()
  {
    $processes = [
      'list-import'    => 'ListImporter',
      'list-refresh'   => 'ListRefresher',
      'queue-messages' => 'QueueMessages'
    ];

    return $processes;
  }

  public static function generateCrontabConfig($instances = 1)
  {
    $config = "";
    foreach(self::_getBackgroundProcesses() as $p)
    {
      for($i = 0; $i < $instances; $i++)
      {
        $x = $i + 1;
        $config .= "* * * * * /usr/bin/php " . base_path()
          . "/artisan " . $p . " inst" . $x . PHP_EOL;
      }
    }

    return $config;
  }

  public static function generateMonitConfig($instances = 1)
  {
    $config = "";
    foreach(self::_getBackgroundProcesses() as $p => $className)
    {
      for($i = 0; $i < $instances; $i++)
      {
        $x = $i + 1;
        $config .= "check process " . $className . "-Inst" . $x . PHP_EOL;
        $config .= "\t" . 'with pidfile "' . storage_path()
          . '/monit/Cme/Cli/' . $className . '/inst' . $x . '.pid"' . PHP_EOL;
        $config .= "\t" . "group CME" . PHP_EOL;
        $config .= "\t" . 'start program = "/usr/bin/php ' . base_path()
          . '/artisan --env=production cme:' . $p . ' inst' . $x . '"' . PHP_EOL;
        $config .= "\t" . 'stop program = "/bin/bash -c \'/bin/kill `/bin/cat '
          . storage_path(
          ) . '/monit/Cme/Cli/' . $className . '/inst' . $x . '.pid`\'"' . PHP_EOL;
        $config .= "\t" . "if mem > 5% for 3 cycles then alert" . PHP_EOL;
        $config .= "\t" . "if mem > 10% for 5 cycles then restart" . PHP_EOL;
        $config .= PHP_EOL . PHP_EOL;
      }
    }

    return $config;
  }

  public static function hostMeetsRequirements()
  {
    return (PHP_VERSION >= '5.4.0') && extension_loaded('mcrypt')
    && extension_loaded('mbstring') && extension_loaded('curl')
    && is_writable(storage_path()) && is_writable(base_path());
  }
}
