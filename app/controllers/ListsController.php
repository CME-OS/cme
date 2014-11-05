<?php
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class ListsController extends BaseController
{
  public function index()
  {
    $result = DB::select("SELECT * FROM lists");

    $data['lists'] = $result;

    return View::make('lists.list', $data);
  }

  public function neww()
  {
    return View::make('lists.new');
  }

  public function add()
  {
    $data   = Input::all();
    $listId = DB::table('lists')->insertGetId($data);

    return Redirect::to('/lists/view/' . $listId);
  }

  public function view()
  {
    $id        = Route::input('id');
    $tableName = 'list_' . $id;
    //check if list table exists/
    $subscribers = [];
    $columns     = [];
    if(Schema::hasTable($tableName))
    {
      //if it does, fetch all subscribers and display
      $subscribers = DB::select(sprintf("SELECT * FROM %s LIMIT 1000", $tableName));
      $columns     = array_keys((array)$subscribers[0]);
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

  public function import()
  {
    $type   = Route::input('type');
    $listId = Input::get('listId');
    if($listId)
    {
      switch($type)
      {
        case 'api':
          $endPoint = Input::get('endpoint');
          $importer = new ApiImporter();
          $importer->import($endPoint, $listId);
          break;
        case 'csv':
          if(Input::hasFile('listFile'))
          {
            $csvFile  = Input::file('listFile');
            $importer = new CsvImporter();
            $importer->import($csvFile, $listId);
          }
          break;
        default:
          echo "not supported";
      }
      return Redirect::to('/lists/view/' . $listId);
    }
  }
}
