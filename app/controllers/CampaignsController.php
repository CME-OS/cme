<?php
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class CampaignsController extends BaseController
{
  public function index()
  {
    $data['campaigns'] = CMECampaign::all();

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
    $data              = Input::all();
    $data['send_time'] = strtotime($data['send_time']);
    $data['created']   = time();
    DB::table('campaigns')->insert($data);

    return Redirect::to('/campaigns');
  }

  public function edit($id)
  {
    $campaign = CMECampaign::find($id);
    if ($campaign)
    {
      $campaign->send_time = date('Y-m-d H:i:s', $campaign->send_time);
      $data['campaign']    = $campaign;
      $data['brands']      = DB::select("SELECT * FROM brands");
      $data['lists']       = DB::select("SELECT * FROM lists");

      return View::make('campaigns.edit', $data);
    }

    return Redirect::route('campaigns');
  }

  public function preview($id)
  {
    $campaign = CMECampaign::find($id);
    if ($campaign)
    {
      echo '<h1>' . $campaign->subject . '</h1>';
      echo $campaign->html_content;
      die;
    }
  }

  public function update()
  {
    $data              = Input::all();
    $data['send_time'] = strtotime($data['send_time']);
    $this->_updateCampaign($data);

    return Redirect::to('/campaigns/edit/' . $data['id']);
  }

  private function _updateCampaign($data)
  {
    DB::table('campaigns')->where('id', '=', $data['id'])
      ->update($data);
  }

  public function delete()
  {
    $id = Route::input('id');
    echo "Deleting $id";
  }

  public function send()
  {
    $id = Input::get('id');

    //build ranges to be consumed through the QueueMessages Command
    $campaign = CMECampaign::find($id);
    if ($campaign)
    {
      //get min and max id of campaign list
      $listId    = $campaign->list_id;
      $listTable = 'list_' . $listId;
      $listInfo  = DB::select(
        sprintf("SELECT min(id) as minId, max(id) as maxId FROM %s", $listTable)
      );
      $minId     = $listInfo[0]->minId;
      $maxId     = $listInfo[0]->maxId;

      //build ranges
      for ($i = $minId; $i <= $maxId; $i++)
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
        try
        {
          DB::table('ranges')->insert($range);
        }
        catch (Exception $e)
        {
          Log::error($e->getMessage());
        }
      }

      //update status of campaign
      DB::table('campaigns')->where(['id' => $id])->update(
        ['status' => 'queuing']
      );
    }

    return Redirect::to("/campaigns");
  }

  public function getPlaceHolders()
  {
    $listId       = Input::get('listId');
    $tableName    = ListHelper::getTable($listId);
    $brand        = (array)head(DB::select("SELECT * FROM brands LIMIT 1"));
    $placeholders = array_keys($brand);
    if ($listId)
    {
      $list = (array)head(DB::select("SELECT * FROM $tableName LIMIT 1"));

      $placeholders = array_merge($placeholders, array_keys($list));
    }

    $final = [];
    foreach ($placeholders as $k => $v)
    {
      if (in_array($v, ListHelper::inBuiltFields()))
      {
        unset($placeholders[$k]);
      }
      else
      {
        $final[] = ['name' => "[$v]"];
      }
    }

    return Response::json($final);
  }

  public function getDefaultSender()
  {
    $brandId = Input::get('brandId');
    $brand   = head(
      DB::select(
        sprintf(
          "SELECT brand_sender_email, brand_sender_name
           FROM brands WHERE id=%d",
          $brandId
        )
      )
    );

    $sender = $brand->brand_sender_name . ' <' . $brand->brand_sender_email . '>';

    return Response::json($sender);
  }
}
