<?php
/**
 * @author  oke.ugwu
 */
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ListHelper
{
  public static function getTable($listId)
  {
    return 'list_' . $listId;
  }

  public static function tableExists($listId)
  {
    return Schema::hasTable(self::getTable($listId));
  }

  public static function createListTable($listId, $columns)
  {
    if(isset($columns['email']))
    {
      $tableName = self::getTable($listId);
      Schema::create(
        $tableName,
        function ($table) use ($columns)
        {
          //add other additional columns needed
          $table->increments('id');
          $table->unique('email');
          foreach($columns as $column)
          {
            $table->string(Str::snake($column), 225);
          }
          $table->integer('bounced', 0);
          $table->integer('unsubscribed', 0);
          $table->integer('test_subscriber', 0);
          $table->timestamp('date_created');
        }
      );
    }
    else
    {
      throw new Exception("List must have a field called email");
    }
  }

  public static function addSubscribers($listId, $subscribers)
  {
    $tableName = self::getTable($listId);
    if(self::tableExists($listId))
    {
      DB::table($tableName)->insert($subscribers);
    }
  }
}
