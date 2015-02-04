<?php
namespace Cme\Web\Controllers;
use Cme\Helpers\ListHelper;
use Cme\Models\CMEList;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ListsController extends BaseController
{
  public function index()
  {
    $result = DB::select("SELECT * FROM lists WHERE deleted_at IS NULL");
    foreach($result as $k => $list)
    {
      $size      = 0;
      $tableName = ListHelper::getTable($list->id);
      //check if list table exists/
      if(Schema::hasTable($tableName))
      {
        $size = DB::table($tableName)->count();
      }
      $result[$k]->size = $size;
    }

    $data['lists'] = $result;

    return View::make('lists.list', $data);
  }

  public function neww()
  {
    return View::make('lists.new');
  }

  public function add()
  {
    $data = Input::all();
    if((int)$data['refresh_interval'] == 0)
    {
      unset($data['refresh_interval']);
    }
    $listId = DB::table('lists')->insertGetId($data);

    return Redirect::to('/lists/view/' . $listId);
  }

  public function view($id)
  {
    $tableName = ListHelper::getTable($id);
    //check if list table exists/
    $subscribers = [];
    $columns     = [];
    if(Schema::hasTable($tableName))
    {
      //if it does, fetch all subscribers and display
      if(DB::table($tableName)->count())
      {
        $subscribers = DB::table($tableName)->simplePaginate(1000);
        $columns     = array_keys((array)$subscribers->offSetGet(1));
      }
    }
    //else suggest to user to import users by CSV/API

    $list = DB::table('lists')->where('id', $id)->first();
    if($list)
    {
      $data['list']        = $list;
      $data['columns']     = $columns;
      $data['subscribers'] = $subscribers;
      return View::make('lists.subscribers', $data);
    }
    else
    {
      return Redirect::to('/lists')->with('msg', 'List does not exist');
    }
  }

  public function edit($id)
  {
    $data['list'] = CMEList::find($id);

    return View::make('lists.edit', $data);
  }

  public function update()
  {
    $data = Input::all();
    DB::table('lists')->where('id', '=', $data['id'])
      ->update($data);

    return Redirect::to('/lists/edit/' . $data['id'])->with(
      'msg',
      'List has been updated'
    );
  }

  public function delete($id)
  {
    $data['deleted_at'] = time();
    DB::table('lists')->where('id', '=', $id)
      ->update($data);

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
        $importRequest = [
          'list_id' => $listId,
          'type'    => $type,
          'source'  => $source
        ];
        DB::table('import_queue')->insert($importRequest);
      }

      //show user a progress bar, or tell them import request has been
      //queued up
      return Redirect::to('/lists/view/' . $listId);
    }
  }
}