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
    $data = Input::all();
    DB::table('lists')->insert($data);

    return Redirect::to('/lists');
  }

  public function view()
  {
    $id = Route::input('id');
    $tableName = 'list_' . $id;
    //check if list table exists/
    $subscribers = [];
    $columns = [];
    if(Schema::hasTable($tableName))
    {
      //if it does, fetch all subscribers and display
      $subscribers = DB::select(sprintf("SELECT * FROM %s LIMIT 1000", $tableName));
      $columns = array_keys((array)$subscribers[0]);
    }
    //else suggest to user to import users by CSV/API

    $data['listName'] = DB::table('lists')
      ->where('id', $id)
      ->pluck('name');
    $data['listId'] = $id;
    $data['columns'] = $columns;
    $data['subscribers'] = $subscribers;
    return View::make('lists.subscribers', $data);
  }

  public function import()
  {
    $type = Route::input('type');
    $listId = Input::get('listId');
    if($listId)
    {
      switch($type)
      {
        case 'api':
          $endPoint = Input::get('endpoint');
          $subscribers = json_decode(file_get_contents($endPoint));
          $tableName = 'list_' . $listId;

          $columns = array_keys((array)$subscribers[0]);
          //check if list table exists/
          if(!Schema::hasTable($tableName))
          {
            Schema::create($tableName, function($table) use($columns)
              {
                //add other additional columns needed
                $table->increments('id');
                $table->unique('email');
                foreach($columns as $column)
                {
                  $table->string($column, 225);
                }
                $table->integer('bounced', 0);
                $table->integer('unsubscribed', 0);
                $table->integer('test_subscriber', 0);
                $table->timestamp('date_created');
              });
          }

          $write = [];
          foreach($subscribers as $subscribers)
          {
            $write[] = (array)$subscribers;
          }

          DB::table($tableName)->insert($write);

          break;
        default:
          echo "not supported";
      }
      return Redirect::to('/lists/view/'.$listId);
    }

  }
}
