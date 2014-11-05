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
    if(in_array('email', $columns))
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
            $table->string(Str::slug($column, '_'), 225);
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

  public static function addSubscribers($listId, array $subscribers)
  {
    if(!empty($subscribers))
    {
      $tableName = self::getTable($listId);
      if(self::tableExists($listId))
      {
        $batch = [];
        foreach($subscribers as $subscriber)
        {
          $values = array_values($subscriber);
          foreach($values as $k => $v)
          {
            $values[$k] = DB::getPdo()->quote($v);
          }
          $batch[] = "(" . implode(",", $values) . ")";
        }

        DB::insert(
          sprintf(
            "INSERT IGNORE INTO %s (%s) VALUES %s",
            $tableName,
            implode(',', array_keys($subscribers[0])),
            implode(',', $batch)
          )
        );
      }
    }
  }
}
