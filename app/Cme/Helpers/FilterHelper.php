<?php
/**
 * @author  oke.ugwu
 */
namespace Cme\Helpers;

class FilterHelper
{
  public static function buildSql($filters)
  {
    $sql = "";
    if($filters)
    {
      $filtersCount = count($filters->filter_field);
      $filterGroups = [];
      for($i = 0; $i < $filtersCount; $i++)
      {
        $field    = $filters->filter_field[$i];
        $operator = $filters->filter_operator[$i];
        $value    = $filters->filter_value[$i];

        $filterGroups[$field][] = "`" . $field . "`" . $operator . "'" . $value . "'";
      }

      //OR filters in the same group
      //AND the rest
      foreach($filterGroups as $field => $group)
      {
        $or = implode(' OR', $group);
        if(count($filterGroups[$field]) > 1)
        {
          $or = "(" . $or . ")";
        }
        $temp[] = $or;
      }

      $sql = implode(' AND', $temp);
    }

    return $sql;
  }
}
