<?php
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class CampaignsController extends BaseController
{
  public function index()
  {
    $data = [
      'campaigns' => DB::select("SELECT * FROM campaigns")
    ];

    return View::make('campaigns.list', $data);
  }

  public function neww()
  {
    $data['brands'] = DB::select("SELECT * FROM brands");
    $data['lists']  = DB::select("SELECT * FROM lists");
    return View::make('campaigns.new', $data);
  }

  public function add()
  {
    $data            = Input::all();
    $data['created'] = time();
    DB::table('campaigns')->insert($data);

    return Redirect::to('/campaigns');
  }

  public function edit()
  {
    $id       = Route::input('id');
    $campaign = DB::select(sprintf("SELECT * FROM %s WHERE id=%d", 'campaigns', $id));
    if($campaign)
    {
      $data['campaign'] = $campaign[0];
      $data['brands']   = DB::select("SELECT * FROM brands");
      $data['lists']    = DB::select("SELECT * FROM lists");
    }

    return View::make('campaigns.edit', $data);
  }

  public function preview()
  {
    $id       = Route::input('id');
    $campaign = DB::select(sprintf("SELECT * FROM %s WHERE id=%d", 'campaigns', $id));
    if($campaign)
    {
      echo '<h1>' . $campaign[0]->subject . '</h1>';
      echo $campaign[0]->html_content;
      die;
    }
  }

  public function update()
  {
    $data = Input::all();
    DB::table('campaigns')->where('id', '=', $data['id'])
      ->update($data);
    return Redirect::to('/campaigns/edit/' . $data['id']);
  }

  public function delete()
  {
    $id = Route::input('id');
    echo "Deleting $id";
  }

  public function send()
  {
    $id = Input::get('id');

    //build ranges;
    $campaign = DB::select(sprintf("SELECT * FROM %s WHERE id=%d", 'campaigns', $id));
    if($campaign)
    {
      //get min and max id of campaign list
      $listId    = $campaign[0]->list_id;
      $listTable = 'list_' . $listId;
      $listInfo  = DB::select(sprintf("SELECT min(id) as minId, max(id) as maxId FROM %s", $listTable));
      $minId     = $listInfo[0]->minId;
      $maxId     = $listInfo[0]->maxId;

      //build ranges
      for($i = $minId; $i <= $maxId; $i++)
      {
        $start = $i;
        $end   = $i = $i + 1000;
        $range = [
          'list_id'     => $listId,
          'campaign_id' => $id,
          'start'       => $start,
          'end'         => $end,
          'created'     => time()
        ];
        DB::table('ranges')->insert($range);
      }

      //update status of campaign
      DB::table('campaigns')->where(['id' => $id])->update(['status' => 'queuing']);
    }

    return Redirect::to("/campaigns");
  }
}
