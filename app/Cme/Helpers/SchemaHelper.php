<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Helpers;

use Illuminate\Support\Facades\DB;

class SchemaHelper
{
  protected static $_columns;

  public static function getColumns($table)
  {
    if(self::$_columns === null)
    {
      self::$_columns = DB::select("SHOW COLUMNS FROM $table");
    }

    return self::$_columns;
  }

  public static function getColumnNames($table)
  {
    $columns = self::getColumns($table);

    $columnNames = [];
    foreach($columns as $columnObj)
    {
      $columnNames[] = $columnObj->Field;
    }

    return $columnNames;
  }

  public static function getColumnsTypes($table)
  {
    $columns = self::getColumns($table);

    $columnTypes = [];
    foreach($columns as $columnObj)
    {
      $columnTypes[$columnObj->Field] = head(explode('(', $columnObj->Type));
    }

    return $columnTypes;
  }


  public static function getColumnValues($table)
  {
    $columnNames = self::getColumnNames($table);
    $values = [];
    foreach($columnNames as $column)
    {
      $values[$column] = DB::table($table)->group_by($column)->get();
    }

    return $values;
  }
}
