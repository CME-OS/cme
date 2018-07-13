<?php
/**
 * @author  oke.ugwu
 */
namespace App\Cme\Helpers;

use CmeKernel\Helpers\ListHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ApiImporter
{
  private $_columns;
  public $_batchSize = 100;
  private $_timeout = 300; //max time to spend importing leads at any time

  /**
   * Import subscribers from source into list.
   * Stop importing leads once we hit the timeout. This is to protect CME
   * from badly implemented API lists (avoid an infinite import)
   *
   * @param $source
   * @param $listId
   */
  public function import($source, $listId)
  {
    $stopTime = time() + $this->_timeout;
    while($subscribers = $this->_getDataFromApi($source, $listId))
    {
      ListHelper::createListTable($listId, $this->_columns);
      ListHelper::addSubscribers($listId, $subscribers);

      if(time() >= $stopTime)
      {
        break;
      }
    }
  }

  /**
   * Return data from API in batches.
   * Batch size is specified by $batchSize property and can be passed as an
   * argument to this script
   *
   * @param string $source
   *
   * @return array|bool
   */
  private function _getDataFromApi($source, $listId)
  {
    //get lastId if we have one
    $lastId = $this->_getLastId($listId);
    $params  = [
      'limit'   => $this->_batchSize,
      'last_id' => $lastId
    ];
    $headers = [
      'CME-ID' => md5('$$%%cmeisgreat%%$$')
    ];
    $data    = false;
    try
    {
      $response    = \Requests::post($source, $headers, $params);
      $contentType = $response->headers->offsetGet('Content-Type');
      if(in_array($contentType, ['text/json', 'application/json']))
      {
        $result = json_decode($response->body, true);
        $data   = $result['list'];
        if(isset($result['last_id']) && $result['last_id'] > 0)
        {
          $this->_storeLastId($listId, $result['last_id']);

          if(count($data))
          {
            if($this->_columns == null)
            {
              $this->_columns = array_keys((array)$data[0]);
            }
          }
          else
          {
            $data = false;
          }
        }
        else
        {
          Log::error("Last ID must be set and greater than 0");
        }
      }
      else
      {
        Log::error("Response is not JSON");
      }
    }
    catch(\Exception $e)
    {
      Log::error($e->getMessage());
    }

    return $data;
  }

  private function _storeLastId($listId, $lastId)
  {
    $importerDir = storage_path() . '/importer';
    $fileName    = $importerDir . '/' . $listId;
    //create log file
    if(!File::exists($importerDir))
    {
      File::makeDirectory($importerDir, $mode = 0777, true);
    }

    File::put($fileName, $lastId);
  }

  private function _getLastId($listId)
  {
    $importerDir = storage_path() . '/importer';
    $fileName    = $importerDir . '/' . $listId;
    //create log file
    $lastId = 0;
    if(File::exists($fileName))
    {
      $lastId = (int)File::get($fileName);
    }

    return $lastId;
  }
}
