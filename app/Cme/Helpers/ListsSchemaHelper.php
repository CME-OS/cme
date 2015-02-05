<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Helpers;

use Illuminate\Support\Facades\DB;

class ListsSchemaHelper extends SchemaHelper
{
  private static $_numberOperators = [
    ['value' => '=', 'text' => 'Equals'],
    ['value' => '!=', 'text' => 'Not Equals'],
    ['value' => '<', 'text' => 'Less Than'],
    ['value' => '>', 'text' => 'Greater Than']
  ];

  private static $_stringOperators = [
    ['value' => '=', 'text' => 'Equals'],
    ['value' => '!=', 'text' => 'Not Equals']
  ];

  private static $_excludeColumns = [
    'id',
    'email',
    'bounced',
    'unsubscribed',
    'test_subscriber',
  ];

  public static function getColumnNames($table)
  {
    $columns = parent::getColumnNames($table);

    $temp   = array_diff($columns, self::$_excludeColumns);
    $options = [];
    foreach($temp as $k => $v)
    {
      $options[] = ['value' => $v, 'text' => $v];
    }

    return $options;
  }

  public static function getColumnsTypes($table)
  {
    $columns     = self::getColumns($table);
    $columnTypes = [];
    foreach($columns as $columnObj)
    {
      if(!in_array($columnObj->Field, self::$_excludeColumns))
      {
        $columnTypes[$columnObj->Field] = head(explode('(', $columnObj->Type));
      }
    }

    return $columnTypes;
  }


  public static function getColumnValues($table)
  {
    $columnNames = parent::getColumnNames($table);
    $values      = [];
    foreach($columnNames as $column)
    {
      if(!in_array($column, self::$_excludeColumns))
      {
        $temp    = DB::table($table)->groupBy($column)->lists($column);
        $options = [];
        foreach($temp as $k => $v)
        {
          $options[] = ['value' => $v, 'text' => $v];
        }
        $values[$column] = $options;
      }
    }

    return $values;
  }

  public static function getColumnOperators($table)
  {
    $operatorByType = [
      'varchar'   => self::$_stringOperators,
      'char'      => self::$_stringOperators,
      'enum'      => self::$_stringOperators,
      'int'       => self::$_numberOperators,
      'double'    => self::$_numberOperators,
      'timestamp' => self::$_numberOperators,
    ];

    $columnTypes = self::getColumnsTypes($table);
    $operators   = [];
    foreach($columnTypes as $columnName => $type)
    {
      if(!in_array($columnName, self::$_excludeColumns))
      {
        $operators[$columnName] = $operatorByType[$type];
      }
    }

    return $operators;
  }
}
