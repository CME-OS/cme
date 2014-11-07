<?php

return array(

  /*
  |--------------------------------------------------------------------------
  | Database Connections
  |--------------------------------------------------------------------------
  |
  | Here are each of the database connections setup for your application.
  | Of course, examples of configuring each database platform that is
  | supported by Laravel is shown below to make development simple.
  |
  |
  | All database work in Laravel is done through the PHP PDO facilities
  | so make sure you have the driver for your particular database of
  | choice installed on your machine before you begin development.
  |
  */

  'connections' => array(

    'mysql' => array(
      'driver'    => 'mysql',
      'host'      => $_ENV['mysql.host'],
      'database'  => $_ENV['mysql.database'],
      'username'  => $_ENV['mysql.username'],
      'password'  => $_ENV['mysql.password'],
      'charset'   => $_ENV['mysql.charset'],
      'collation' => $_ENV['mysql.collation'],
      'prefix'    => $_ENV['mysql.prefix'],
    )
  )
);
