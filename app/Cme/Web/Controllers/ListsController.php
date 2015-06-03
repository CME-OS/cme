<?php
namespace Cme\Web\Controllers;

use CmeData\ListData;
use CmeData\ListImportQueueData;
use CmeData\SubscriberData;
use CmeKernel\Core\CmeDatabase;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Exceptions\InvalidDataException;
use CmeKernel\Helpers\ListHelper;
use CmeKernel\Helpers\ListsSchemaHelper;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Paginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ListsController extends BaseController
{
  private $_perPage = 500;

  public function index()
  {
    $data['lists'] = CmeKernel::EmailList()->all();
    return View::make('lists.list', $data);
  }

  public function neww()
  {
    $data = Session::get('formData', ['input' => null, 'errors' => null]);
    return View::make('lists.new', $data);
  }

  public function add()
  {
    $listData = ListData::hydrate(Input::all());
    try
    {
      $listId = CmeKernel::EmailList()->create($listData);
      return Redirect::to('/lists/view/' . $listId);
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/lists/new')->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $listData->getValidationErrors()
        ]
      );
    }
  }

  public function newSubscriber($id)
  {
    $list = CmeKernel::EmailList()->get($id);
    if($list)
    {
      $table           = ListHelper::getTable($id);
      $data['id']      = $id;
      $data['columns'] = ListsSchemaHelper::getColumnNames($table);
      $data            = array_merge(
        $data,
        Session::get('formData', ['input' => null, 'errors' => null])
      );

      return View::make('lists.new-subscriber', $data);
    }
    return Redirect::to('/lists');
  }

  public function addSubscriber()
  {
    $listId         = (int)Input::get('id');
    $subscriberData = SubscriberData::hydrate(Input::all());
    try
    {
      $added = CmeKernel::EmailList()->addSubscriber(
        $subscriberData,
        $listId
      );
      if($added)
      {
        return Redirect::to('/lists/view/' . $listId);
      }
      return Redirect::to('/lists');
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/lists/new-subscriber/' . $listId)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $subscriberData->getValidationErrors()
        ]
      );
    }
  }

  public function deleteSubscriber()
  {
    $listId       = (int)Route::input('listId');
    $subscriberId = (int)Route::input('id');
    $deleted      = CmeKernel::EmailList()->deleteSubscriber(
      $subscriberId,
      $listId
    );
    if($deleted)
    {
      return Redirect::to('/lists/view/' . $listId);
    }
    return Redirect::to('/lists');
  }

  public function view($id)
  {
    if(CmeKernel::EmailList()->exists($id))
    {
      $page = Input::get('page', 1);
      Paginator::setViewName('pagination::simple');
      $subscriberTotal = CmeDatabase::conn()
        ->table(ListHelper::getTable($id))
        ->count();
      $pager           = Paginator::make(
        [],
        $subscriberTotal,
        $this->_perPage
      );
      $data['page']    = $page;
      $data['pager']   = $pager;
      $data['list']    = CmeKernel::EmailList()->get($id);

      $data['columns']     = $this->_getListColumns($id);
      $data['subscribers'] = $subscriberTotal > 0;
      return View::make('lists.subscribers', $data);
    }
    else
    {
      return Redirect::to('/lists')->with('msg', 'List does not exist');
    }
  }

  public function subscribers()
  {
    $listId              = Input::get('list_id');
    $page                = Input::get('page', 1);
    $data['subscribers'] = $this->_subscribers($listId, $page, $this->_perPage);
    $data['columns']     = $this->_getListColumns($listId);
    return Response::json($data);
  }

  public function search()
  {
    $listId = Input::get('list_id');
    $query  = Input::get('q');
    $result = CmeDatabase::conn()
      ->table(ListHelper::getTable($listId))
      ->whereRaw("email LIKE '$query%'")
      ->get();

    $subscribers = [];
    foreach($result as $row)
    {
      $subscribers[] = SubscriberData::hydrate($row);
    }

    $data['subscribers'] = $subscribers;
    $data['columns']     = $this->_getListColumns($listId);
    return $data;
  }

  private function _subscribers($listId, $page, $perPage)
  {
    $subscribers = [];
    if(CmeKernel::EmailList()->exists($listId))
    {
      $offset      = ($page - 1) * $perPage;
      $subscribers = CmeKernel::EmailList()->getSubscribers(
        $listId,
        $offset,
        $perPage
      );
    }
    return $subscribers;
  }

  private function _getListColumns($listId)
  {
    $tempColumns = CmeKernel::EmailList()->getColumns($listId);
    $columns     = [];
    foreach($tempColumns as $c)
    {
      $columns[] = ['name' => camel_case($c)];
    }

    return $columns;
  }

  public function edit($id)
  {
    $data['list'] = CmeKernel::EmailList()->get($id);
    $data         = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );
    return View::make('lists.edit', $data);
  }

  public function update()
  {
    $listData = ListData::hydrate(Input::all());
    try
    {
      CmeKernel::EmailList()->update($listData);
      return Redirect::to('/lists/edit/' . $listData->id)->with(
        'msg',
        'List has been updated'
      );
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/lists/edit/' . $listData->id)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $listData->getValidationErrors()
        ]
      );
    }
  }

  public function delete($id)
  {
    CmeKernel::EmailList()->delete($id);
    return Redirect::to('/lists')->with('msg', 'List has been deleted');
  }

  public function import()
  {
    $type   = Route::input('type');
    $listId = Input::get('listId');
    if($listId)
    {
      $source = null;
      switch($type)
      {
        case 'api':
          $source = Input::get('endpoint');
          break;
        case 'csv':
          echo "csv upload";
          if(Input::hasFile('listFile'))
          {
            echo "uploading..";
            $csvFile = Input::file('listFile');
            $csvFile->move(
              storage_path() . '/tmp/',
              $csvFile->getClientOriginalName()
            );
            $source = storage_path() .
              '/tmp/' . $csvFile->getClientOriginalName();
          }
          break;
      }

      if($source)
      {
        //queue up
        $importRequest         = new ListImportQueueData();
        $importRequest->listId = $listId;
        $importRequest->type   = $type;
        $importRequest->source = $source;
        CmeKernel::EmailList()->import($importRequest);
      }

      //show user a progress bar, or tell them import request has been
      //queued up
      return Redirect::to('/lists/view/' . $listId);
    }
  }
}
