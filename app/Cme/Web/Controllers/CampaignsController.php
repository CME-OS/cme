<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\ListHelper;
use Cme\Helpers\ListsSchemaHelper;
use Cme\Models\CMEBrand;
use Cme\Models\CMECampaign;
use Cme\Models\CMEList;
use Illuminate\Support\Facades\Config;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class CampaignsController extends BaseController
{
  public function index()
  {
    $data['campaigns'] = CMECampaign::all();

    return View::make('campaigns.list', $data);
  }

  public function neww()
  {
    $step       = Input::get('step', Route::input('step', 1));
    $campaignId = Session::get('newCampaignId', Input::get('id'));

    switch($step)
    {
      case 1:
        $stepView = $this->_step1($campaignId);
        break;
      case 2:
        $campaign = ($campaignId) ?
          CMECampaign::find($campaignId) : new CMECampaign();
        if(Request::isMethod('post'))
        {
          $campaign->subject  = Input::get('subject');
          $campaign->from     = Input::get('from');
          $campaign->list_id  = Input::get('list_id');
          $campaign->brand_id = Input::get('brand_id');
          $campaign->type     = Input::get('type');
          if(Input::get('filters'))
          {
            $campaign->filters = json_encode(Input::get('filters'));
          }
          $campaign->created = time();
          $campaign->save();
          Session::put('newCampaignId', $campaign->id);
        }

        $stepView = $this->_step2($campaign->id);
        break;
      case 3:
        $campaign = CMECampaign::find($campaignId);
        if($campaign)
        {
          $campaign->html_content = Input::get('html_content');
          $campaign->save();
          $stepView = $this->_step3($campaign->id);
        }
        else
        {
          $stepView = $this->_step1($campaign->id);
        }
        break;
      default:
        $stepView = $this->_step1();
    }
    return $stepView;
  }

  private function _step1($campaignId = null)
  {
    if($campaignId === null)
    {
      $data['campaign'] = new CMECampaign();
    }
    else
    {
      $campaign           = CMECampaign::find($campaignId);
      $data['campaign']   = $campaign;
      $data['filterData'] = $this->_getSegmentOptions($campaign->list_id);
    }
    $data['brands'] = CMEBrand::getAllActive();
    $data['lists']  = CMEList::getAllActive();

    return View::make('campaigns.step1', $data);
  }

  private function _step2($campaignId)
  {
    $campaign = CMECampaign::find($campaignId);;
    $data['campaign']     = $campaign;
    $data['placeholders'] = $this->_getPlaceHolders($campaign->list_id);

    return View::make('campaigns.step2', $data);
  }

  private function _step3($campaignId)
  {
    $data['campaign'] = CMECampaign::find($campaignId);

    return View::make('campaigns.step3', $data);
  }

  public function add()
  {
    $campaign                   = CMECampaign::find(Input::get('id'));
    $campaign->send_priority    = Input::get('send_priority');
    $campaign->send_time        = strtotime(Input::get('send_time'));
    $campaign->smtp_provider_id = Input::get('smtp_provider_id');
    $campaign->save();

    Session::forget('newCampaignId');
    return Redirect::to('/campaigns/preview/' . $campaign->id);
  }

  public function edit($id)
  {
    $campaign = CMECampaign::find($id);
    if($campaign)
    {
      $campaign->send_time  = date('Y-m-d H:i:s', $campaign->send_time);
      $data['campaign']     = $campaign;
      $data['brands']       = CMEBrand::getAllActive();
      $data['lists']        = CMEList::getAllActive();
      $data['placeholders'] = $this->_getPlaceHolders($campaign->list_id);
      $data['filterData']   = $this->_getSegmentOptions($campaign->list_id);

      return View::make('campaigns.edit', $data);
    }

    return Redirect::route('campaigns');
  }

  public function preview($id)
  {
    $campaign         = CMECampaign::find($id);
    $data['campaign'] = $campaign;
    return View::make('campaigns.preview', $data);
  }

  public function content($id)
  {
    $campaign = CMECampaign::find($id);
    if($campaign)
    {
      echo $campaign->html_content;
    }
    else
    {
      //show 404 page
      echo "";
    }
  }

  public function update()
  {
    $data     = Input::all();
    $campaign = CMECampaign::find($data['id']);
    //if content changed, force user to test & preview campaign as a
    //safety measure
    if($campaign->html_content != $data['html_content'])
    {
      $data['tested']    = 0;
      $data['previewed'] = 0;
    }
    if($data['filters'])
    {
      $data['filters'] = json_encode($data['filters']);
    }

    $data['send_time'] = strtotime($data['send_time']);
    CMECampaign::saveData($data);

    return Redirect::to('/campaigns/preview/' . $data['id']);
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
    if($this->_buildQueueRanges($id))
    {
      //update status of campaign
      DB::table('campaigns')->where(['id' => $id])->update(
        ['status' => 'Queuing']
      );
    }

    return Redirect::to("/campaigns/preview/" . $id);
  }

  public function pause()
  {
    $id = Input::get('id');

    DB::table('message_queue')
      ->where(['campaign_id' => $id, 'status' => 'pending'])
      ->update(
        ['status' => 'Paused']
      );

    //update status of campaign
    DB::table('campaigns')->where(['id' => $id])->update(
      ['status' => 'Paused']
    );

    return Redirect::to("/campaigns/preview/" . $id);
  }

  public function resume()
  {
    $id = Input::get('id');

    DB::table('message_queue')
      ->where(['campaign_id' => $id, 'status' => 'Paused'])
      ->update(
        ['status' => 'Pending']
      );

    //update status of campaign
    DB::table('campaigns')->where(['id' => $id])->update(
      ['status' => 'Queued']
    );

    return Redirect::to("/campaigns/preview/" . $id);
  }


  public function abort()
  {
    $id = Input::get('id');

    //delete pending messages from the queue
    DB::table('message_queue')
      ->where(['campaign_id' => $id, 'status' => 'pending'])
      ->delete();

    //update status of campaign
    DB::table('campaigns')->where(['id' => $id])->update(
      ['status' => 'Aborted']
    );

    return Redirect::to("/campaigns/preview/" . $id);
  }

  private function _buildQueueRanges($campaignId)
  {
    $built    = false;
    $campaign = CMECampaign::find($campaignId);
    if($campaign)
    {
      if($campaign->tested > 0)
      {
        //get min and max id of campaign list
        $Ids   = ListHelper::getMinMaxIds($campaign->list_id);
        $minId = $Ids->minId;
        $maxId = $Ids->maxId;

        //build ranges
        for($i = $minId; $i <= $maxId; $i++)
        {
          $start = $i;
          $end   = $i = $i + 1000;
          $range = [
            'list_id'     => $campaign->list_id,
            'campaign_id' => $campaignId,
            'start'       => $start,
            'end'         => $end,
            'created'     => time()
          ];
          try
          {
            DB::table('ranges')->insert($range);
          }
          catch(\Exception $e)
          {
            Log::error($e->getMessage());
          }
        }
        $built = true;
      }
      else
      {
        throw new \Exception(
          "You cannot queue a campaign you have not tested. "
          . "Please test campaign before queuing"
        );
      }
    }

    return $built;
  }

  public function test()
  {
    $email      = Input::get('test_email');
    $campaignId = Input::get('id');
    $campaign   = CMECampaign::find($campaignId);

    $subscriber = ListHelper::getRandomSubscriber($campaign->list_id);
    if($subscriber)
    {

      $placeHolders = [];
      $columns      = array_keys((array)$subscriber);
      foreach($columns as $c)
      {
        $placeHolders[$c] = "[$c]";
      }
      //add brand attributes as placeholders too
      $result  = DB::select(
        sprintf(
          "SELECT * FROM brands WHERE id=%d",
          $campaign->brand_id
        )
      );
      $brand   = $result[0];
      $columns = array_keys((array)$brand);
      foreach($columns as $c)
      {
        $placeHolders[$c] = "[$c]";
      }

      //parse and compile message (replacing placeholders if any)
      $html = $campaign->html_content;
      $text = $campaign->text_content;
      foreach($placeHolders as $prop => $placeHolder)
      {
        $replace = false;
        if(property_exists($subscriber, $prop))
        {
          $replace = $subscriber->$prop;
        }
        elseif(property_exists($brand, $prop))
        {
          if($prop == 'brand_unsubscribe_url')
          {
            $replace = $this->_getUnsubscribeUrl(
              $brand->$prop,
              $campaign->id,
              $campaign->list_id,
              $subscriber->id
            );
          }
          else
          {
            $replace = $brand->$prop;
          }
        }

        if($replace !== false)
        {
          $html = str_replace($placeHolder, $replace, $html);
          $text = str_replace($placeHolder, $replace, $text);
        }
      }

      //append pixel to html content, so we can track opens
      $domain   = Config::get('app.domain');
      $pixelUrl = "http://" . $domain . "/track/open/" . $campaign->id
        . "_" . $campaign->list_id . "_" . $subscriber->id;
      $html .= '<img src="' . $pixelUrl
        . '" style="display:none;" height="1" width="1" />';

      list($fromName, $fromEmail) = explode(' <', $campaign->from);

      //write to message queue
      $message = [
        'subject'       => $campaign->subject,
        'from_name'     => $fromName,
        'from_email'    => trim($fromEmail, '<>'),
        'to'            => $email,
        'html_content'  => $html,
        'text_content'  => $text,
        'subscriber_id' => $subscriber->id,
        'list_id'       => $campaign->list_id,
        'brand_id'      => $campaign->brand_id,
        'campaign_id'   => $campaign->id,
        'send_time'     => strtotime('-365 days'),
        'send_priority' => 4
      ];
      DB::table('message_queue')->insert($message);
      $campaign->tested = 1;
      $campaign->save();

      return Redirect::to("/campaigns");
    }
    else
    {
      throw new \Exception("Could not get a random subscriber");
    }
  }

  private function _getUnsubscribeUrl($url, $campaignId, $listId, $subscriberId)
  {
    $domain = Config::get('app.domain');
    $url    = "http://" . $domain . "/track/unsubscribe/" . $campaignId
      . "_" . $listId . "_" . $subscriberId . "/" . base64_encode($url);

    return $url;
  }

  public function getPlaceHolders()
  {
    $listId       = Input::get('listId');
    $placeholders = $this->_getPlaceHolders($listId);
    $final        = [];
    foreach($placeholders as $k => $v)
    {
      $final[] = ['name' => "[$v]"];
    }

    return Response::json($final);
  }

  private function _getPlaceHolders($listId)
  {
    $brand        = (array)CMEBrand::getAnyBrand();
    $placeholders = array_keys($brand);
    if($listId)
    {
      $list = (array)CMEList::getAnySubscriber($listId);

      $placeholders = array_merge($placeholders, array_keys($list));
    }

    return array_diff($placeholders, ListHelper::inBuiltFields());
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

  public function getSegmentOptions()
  {
    $listId   = Input::get('listId');
    $response = $this->_getSegmentOptions($listId);
    return Response::json($response);
  }

  private function _getSegmentOptions($listId)
  {
    $table    = ListHelper::getTable($listId);
    $response = [
      'columns'   => ListsSchemaHelper::getColumnNames($table),
      'operators' => ListsSchemaHelper::getColumnOperators($table),
      'values'    => ListsSchemaHelper::getColumnValues($table),
    ];

    return $response;
  }
}
