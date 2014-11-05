<?php
/**
 * @author  oke.ugwu
 */

class ApiImporter
{
  private $_start;
  private $_columns;
  public $_batchSize = 100;

  /**
   * Import subscribers from source into list
   *
   * @param $source
   * @param $listId
   */
  public function import($source, $listId)
  {
    while($subscribers = $this->_getDataFromApi($source))
    {
      if(!ListHelper::tableExists($listId))
      {
        ListHelper::createListTable($listId, $this->_columns);
      }

      ListHelper::addSubscribers($listId, $subscribers);
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
  private function _getDataFromApi($source)
  {
    $params   = [
      'start' => $this->_start,
      'limit' => $this->_batchSize
    ];
    $headers  = [
      'CME-ID' => md5('$$%%cmeisgreat%%$$')
    ];
    $data     = false;
    $response = Requests::post($source, $headers, $params);
    $contentType = $response->headers->offsetGet('Content-Type');
    if(in_array($contentType, ['text/json', 'application/json']))
    {
      $data = json_decode($response->body, true);
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
      $this->_start += $this->_batchSize;
    };

    return $data;
  }
}
