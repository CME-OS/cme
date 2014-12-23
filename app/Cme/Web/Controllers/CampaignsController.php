<?php
namespace Cme\Web\Controllers;

use Cme\Helpers\ListHelper;
use Cme\Models\CMEBrand;
use Cme\Models\CMECampaign;
use Cme\Models\CMEList;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
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
    $data['brands'] = CMEBrand::getAllActive();
    $data['lists']  = CMEList::getAllActive();

    return View::make('campaigns.new', $data);
  }

  public function add()
  {
    $data              = Input::all();
    $data['send_time'] = strtotime($data['send_time']);
    $data['created']   = time();

    CMECampaign::saveData($data);

    return Redirect::to('/campaigns');
  }

  public function edit($id)
  {
    $campaign = CMECampaign::find($id);
    if($campaign)
    {
      $campaign->send_time = date('Y-m-d H:i:s', $campaign->send_time);
      $data['campaign']    = $campaign;
      $data['brands']      = CMEBrand::getAllActive();
      $data['lists']       = CMEList::getAllActive();

      return View::make('campaigns.edit', $data);
    }

    return Redirect::route('campaigns');
  }

  public function preview($id)
  {
    $campaign = CMECampaign::find($id);
    if($campaign)
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
    CMECampaign::saveData($data);

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

    //build ranges to be consumed through the QueueMessages Command
    $campaign = CMECampaign::find($id);
    if($campaign)
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
          'campaign_id' => $id,
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

      //update status of campaign
      DB::table('campaigns')->where(['id' => $id])->update(
        ['status' => 'queuing']
      );
    }

    return Redirect::to("/campaigns");
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
    $brand        = (array)CMEBrand::getAnyBrand();
    $placeholders = array_keys($brand);
    if($listId)
    {
      $list = (array)CMEList::getAnySubscriber($listId);

      $placeholders = array_merge($placeholders, array_keys($list));
    }

    $final = [];
    foreach($placeholders as $k => $v)
    {
      if(in_array($v, ListHelper::inBuiltFields()))
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
