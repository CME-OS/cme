<?php
/**
 * @author  oke.ugwu
 */

namespace App\Cme\Install;

abstract class InstallTable
{
  /**
   * The name of the database connection to use.
   *
   * @var string
   */
  protected $connection;

  /**
   * Get the migration connection name.
   *
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }

  /**
   * @param string $table
   *
   * @return mixed
   */
  abstract public function setTable($table);
  abstract public function up();
  abstract public function down();
}
