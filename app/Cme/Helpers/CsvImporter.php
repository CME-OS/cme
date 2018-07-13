<?php
/**
 * @author  oke.ugwu
 */
namespace App\Cme\Helpers;
use CmeKernel\Helpers\ListHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CsvImporter
{
  private $_handle;
  private $_columns;
  public $batchSize = 1000;

  /**
   * Import subscribers from source into list
   *
   * @param $source
   * @param $listId
   */
  public function import($source, $listId)
  {
    while($subscribers = $this->_getDataFromCsv($source))
    {
      ListHelper::createListTable($listId, $this->_columns);
      ListHelper::addSubscribers($listId, $subscribers);
    }
    File::delete($source);
  }

  /**
   * Return data from CSV in batches.
   * Batch size is specified by $batchSize property and can be passed as an
   * argument to this script
   *
   * @param string $source
   *
   * @return array|bool
   */
  private function _getDataFromCsv($source)
  {
    $rowsRead  = 0;
    $insertRow = false;

    if($this->_handle === null)
    {
      $this->_handle = fopen($source, "r");
    }

    if($this->_handle)
    {
      while($rowsRead < $this->batchSize
        && ($data = fgetcsv($this->_handle, 1000, ',')) !== false)
      {
        foreach($data as $i => $text)
        {
          $data[$i] = trim($text);
        }

        if($this->_columns == null)
        {
          $this->_columns = $data;
          foreach($this->_columns as $k => $v)
          {
            $this->_columns[$k] = Str::slug($v, '_');
          }
          continue;
        }
        else
        {
          if(count($this->_columns) != count($data))
          {
            Log::debug("Skipping ...");
            continue;
          }
          $csvData = array_combine($this->_columns, $data);
          $email   = isset($csvData['email']) ? $csvData['email'] : null;
          if($email != null)
          {
            Log::debug("Importing: $email");
            $insertRow[] = $csvData;

            $rowsRead++;
          }
        }
      }
    }
    return $insertRow;
  }
}
