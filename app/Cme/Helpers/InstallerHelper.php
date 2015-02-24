<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Helpers;

use Illuminate\Config\EnvironmentVariables;
use Illuminate\Config\Repository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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

    $files   = File::glob($installPath . '*.php');
    $classes = [];
    foreach($files as $file)
    {
      require_once $file;
      $className = str_replace('.php', '', basename($file));
      $classes[] = $className;
    }

    return $classes;
  }

  public static function installDb($classes)
  {
    foreach($classes as $migrationClass)
    {
      $m = new $migrationClass;
      if($m instanceof Migration)
      {
        $m->up();
      }
    }
  }

  public static function unInstallDb($classes)
  {
    foreach($classes as $migrationClass)
    {
      $m = new $migrationClass;
      if($m instanceof Migration)
      {
        $m->down();
      }
    }
  }

  public static function createUser($username, $password)
  {
    $data['email']      = $username;
    $data['password']   = Hash::make($password);
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');
    $data['active']     = 1;
    DB::table('users')->insert($data);
  }

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

    $template = self::_getEvnFileTemplate();
    $template = str_replace('[DOMAIN]', self::$domain, $template);
    $template = str_replace('[HOST]', self::$dbHost, $template);
    $template = str_replace('[DATABASE]', self::$dbName, $template);
    $template = str_replace('[USERNAME]', self::$dbUser, $template);
    $template = str_replace('[PASSWORD]', self::$dbPassword, $template);
    $template = str_replace('[AWS_KEY]', self::$awsKey, $template);
    $template = str_replace('[AWS_SECRET]', self::$awsSecret, $template);
    $template = str_replace('[AWS_REGION]', self::$awsRegion, $template);
    $template = str_replace('[KEY]', Str::random(32), $template);

    file_put_contents($envFile, $template);
    self::_reloadEnvConfig($env);
  }

  private static function _reloadEnvConfig($env)
  {
    with($envVariables = new EnvironmentVariables(
      App::getEnvironmentVariablesLoader()))->load($env);

    App::instance('config', $config = new Repository(
      App::getConfigLoader(), $env
    ));
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
}
