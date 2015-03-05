<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Helpers;

class DbUpdate
{
  /**
   * Returns a list of tables that have been added in this update
   * @return array
   */
  public function added()
  {
    return [
      'templates',
      'bounces',
      'unsubscribes'
    ];
  }

  /**
   * Returns a list of tables that have been removed in this update
   * @return array
   */
  public function removed()
  {
    return [

    ];
  }

  /**
   * Returns a list of tables that have been modified in this update
   * @return array
   */
  public function modified()
  {
    return [
      'campaigns' => ['subject' => 'name']
    ];
  }

  /**
   * Returns a list of tables that have been renamed in this update
   * @return array
   */
  public function renamed()
  {
    return [
      'ranges' => 'ranges_queue'
    ];
  }

  public function run()
  {

  }
}
