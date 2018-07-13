<?php
namespace App\Cme\Web\Controllers;

use CmeData\CampaignData;
use CmeData\MessageQueueData;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Core\CmeMessage;
use CmeKernel\Enums\CampaignPriority;
use CmeKernel\Exceptions\InvalidDataException;
use CmeKernel\Helpers\CampaignHelper;
use CmeKernel\Helpers\ListHelper;
use CmeKernel\Helpers\ListsSchemaHelper;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class CampaignsController extends BaseController
{
  public function index()
  {
    $data['campaigns']    = CmeKernel::Campaign()->all();
    $data['labelClasses'] = [
      'Pending' => 'label-default',
      'Queuing' => 'label-info',
      'Queued'  => 'label-info',
      'Sending' => 'label-primary',
      'Sent'    => 'label-success',
      'Paused'  => 'label-warning',
      'Aborted' => 'label-danger'
    ];

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
          CmeKernel::Campaign()->get($campaignId) : new CampaignData();
        try
        {
          if(Request::isMethod('post'))
          {
            $campaign->name    = Input::get('name');
            $campaign->subject = Input::get('subject');
            $campaign->from    = Input::get('from');
            $campaign->listId  = Input::get('list_id');
            $campaign->brandId = Input::get('brand_id');
            $campaign->type    = Input::get('type');
            if(Input::get('filters'))
            {
              $campaign->filters = Input::get('filters');
            }
            $campaign->id = CmeKernel::Campaign()->create($campaign);
            Session::put('newCampaignId', $campaign->id);
          }

          $stepView = $this->_step2($campaign->id);
        }
        catch(\Exception $e)
        {
          return Redirect::to('/campaigns/new')->with(
            'formData',
            [
              'input'  => Input::all(),
              'errors' => $campaign->getValidationErrors()
            ]
          );
        }
        break;
      case 3:
        $campaign = CmeKernel::Campaign()->get($campaignId);
        try
        {
          if($campaign)
          {
            $campaign->htmlContent = Input::get('html_content');
            CmeKernel::Campaign()->update($campaign);
            $stepView = $this->_step3($campaign->id);
          }
          else
          {
            $stepView = $this->_step1($campaign->id);
          }
        }
        catch(InvalidDataException $e)
        {
          return Redirect::to('/campaigns/new/2')->with(
            'formData',
            [
              'input'  => Input::all(),
              'errors' => $campaign->getValidationErrors()
            ]
          );
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
      $data['campaign'] = new CampaignData();
    }
    else
    {
      $campaign           = CmeKernel::Campaign()->get($campaignId);
      $data['campaign']   = $campaign;
      $data['filterData'] = $this->_getSegmentOptions($campaign->listId);
    }
    $data['brands'] = CmeKernel::Brand()->all();
    $data['lists']  = CmeKernel::EmailList()->all();

    $data = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );

    return View::make('campaigns.step1', $data);
  }

  private function _step2($campaignId)
  {
    $campaign             = CmeKernel::Campaign()->get($campaignId);
    $data['campaign']     = $campaign;
    $data['templates']    = CmeKernel::Template()->getKeyedListFor('name');
    $data['placeholders'] = $this->_getPlaceHolders($campaign->listId);

    $data = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );

    return View::make('campaigns.step2', $data);
  }

  private function _step3($campaignId)
  {
    $campaign              = CmeKernel::Campaign()->get($campaignId);
    $campaign->sendTime    = ((int)$campaign->sendTime) ? date(
      'Y-m-d H:i:s',
      $campaign->sendTime
    ) : '';
    $data['campaign']      = $campaign;
    $data['smtpProviders'] = CmeKernel::SmtpProvider()->all();

    $data = array_merge(
      $data,
      Session::get('formData', ['input' => null, 'errors' => null])
    );

    return View::make('campaigns.step3', $data);
  }

  public function add()
  {
    $campaign                 = CmeKernel::Campaign()->get(Input::get('id'));
    $campaign->sendPriority   = Input::get('send_priority');
    $campaign->sendTime       = strtotime(Input::get('send_time'));
    $campaign->smtpProviderId = Input::get('smtp_provider_id');

    try
    {
      CmeKernel::Campaign()->update($campaign);
      Session::forget('newCampaignId');
      return Redirect::to('/campaigns/preview/' . $campaign->id);
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/campaigns/new/3')->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $campaign->getValidationErrors()
        ]
      );
    }
  }

  public function edit($id)
  {
    $campaign = CmeKernel::Campaign()->get($id);
    if($campaign)
    {
      $campaign->sendTime    = date('Y-m-d H:i:s', $campaign->sendTime);
      $data['campaign']      = $campaign;
      $data['brands']        = CmeKernel::Brand()->all();
      $data['lists']         = CmeKernel::EmailList()->all();
      $data['smtpProviders'] = CmeKernel::SmtpProvider()->all();
      $data['placeholders']  = $this->_getPlaceHolders($campaign->listId);
      $data['filterData']    = $this->_getSegmentOptions($campaign->listId);

      $data = array_merge(
        $data,
        Session::get('formData', ['input' => null, 'errors' => null])
      );
      return View::make('campaigns.edit', $data);
    }

    return Redirect::route('campaigns');
  }

  public function preview($id)
  {
    $campaign               = CmeKernel::Campaign()->get($id);
    $campaign->brand        = CmeKernel::Brand()->get($campaign->brandId);
    $campaign->list         = CmeKernel::EmailList()->get($campaign->listId);
    $campaign->smtpProvider = null;
    if($campaign->smtpProviderId)
    {
      $campaign->smtpProvider = CmeKernel::SmtpProvider()->get(
        $campaign->smtpProviderId
      );
    }

    $data['campaign']   = $campaign;
    $data['sentEmails'] = CmeKernel::CampaignEvent()->getSentMessages(
      $campaign->id
    );
    return View::make('campaigns.preview', $data);
  }

  public function content($id)
  {
    $campaign = CmeKernel::Campaign()->get($id);
    if($campaign)
    {
      echo $campaign->htmlContent;
    }
    else
    {
      //show 404 page
      echo "";
    }
  }

  public function update()
  {
    $data = CampaignData::hydrate(Input::all());
    try
    {
      $data->sendTime = strtotime($data->sendTime);
      CmeKernel::Campaign()->update($data);
      return Redirect::to('/campaigns/preview/' . $data->id);
    }
    catch(InvalidDataException $e)
    {
      return Redirect::to('/campaigns/edit/' . $data->id)->with(
        'formData',
        [
          'input'  => Input::all(),
          'errors' => $data->getValidationErrors()
        ]
      );
    }
  }

  public function copy($id)
  {
    CmeKernel::Campaign()->copy($id);
    return Redirect::to('/campaigns');
  }

  public function delete()
  {
    CmeKernel::Campaign()->delete(Route::input('id'));
    return Redirect::to('/campaigns');
  }

  public function send()
  {
    $id = Input::get('id');
    CmeKernel::Campaign()->queue($id);
    return Redirect::to("/campaigns/preview/" . $id);
  }

  public function pause()
  {
    $id = Input::get('id');
    CmeKernel::Campaign()->pause($id);
    return Redirect::to("/campaigns/preview/" . $id);
  }

  public function resume()
  {
    $id = Input::get('id');
    CmeKernel::Campaign()->resume($id);
    return Redirect::to("/campaigns/preview/" . $id);
  }


  public function abort()
  {
    $id = Input::get('id');
    CmeKernel::Campaign()->abort($id);
    return Redirect::to("/campaigns/preview/" . $id);
  }

  public function test()
  {
    $email      = Input::get('test_email');
    $campaignId = Input::get('id');
    $campaign   = CmeKernel::Campaign()->get($campaignId);
    $brand      = CmeKernel::Brand()->get($campaign->brandId);

    $subscriber = ListHelper::getRandomSubscriber($campaign->listId);
    if($subscriber)
    {
      $message = CampaignHelper::compileMessage($campaign, $brand, $subscriber);
      list($fromName, $fromEmail) = explode(' <', $campaign->from);

      $fromEmail = trim($fromEmail, '<>');

      //add label to fromEmail so we can track bounces
      /*$messageId = implode(
        '.',
        [$campaign->id, $campaign->list_id, $subscriber->id]
      );
      $fromEmail = CampaignHelper::labelSender($fromEmail, $messageId);*/

      //write to message queue
      $messageData               = new MessageQueueData();
      $messageData->subject      = $campaign->subject;
      $messageData->fromName     = $fromName;
      $messageData->fromEmail    = $fromEmail;
      $messageData->to           = $email;
      $messageData->htmlContent  = $message->html;
      $messageData->textContent  = $message->text;
      $messageData->subscriberId = 0;
      $messageData->listId       = $campaign->listId;
      $messageData->brandId      = $campaign->brandId;
      $messageData->campaignId   = $campaign->id;
      $messageData->sendTime     = strtotime('-365 days');
      $messageData->sendPriority = CampaignPriority::HIGH;
      (new CmeMessage())->create($messageData);

      $campaign->tested = 1;
      CmeKernel::Campaign()->update($campaign);

      return Redirect::to("/campaigns/preview/" . $campaignId);
    }
    else
    {
      throw new \Exception("Could not get a random subscriber");
    }
  }

  public function getPlaceHolders()
  {
    $listId       = Input::get('listId');
    $placeholders = $this->_getPlaceHolders($listId);
    $final        = [];
    foreach($placeholders as $k => $v)
    {
      $final[] = ['value' => "[$v]", 'text' => "[$v]", 'label' => "[$v]"];
    }

    return Response::json($final);
  }

  private function _getPlaceHolders($listId)
  {
    $placeholders = CmeKernel::Brand()->getColumns();
    if($listId)
    {
      $listColumns = CmeKernel::EmailList()->getColumns($listId);

      $placeholders = array_merge($placeholders, $listColumns);
    }

    return array_diff($placeholders, ListHelper::inBuiltFields());
  }

  public function getDefaultSender()
  {
    $brand  = CmeKernel::Brand()->get(Input::get('brandId'));
    $sender = $brand->brandSenderName . ' <' . $brand->brandSenderEmail . '>';

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

  public function getTemplate()
  {
    $templateId           = Input::get('templateId');
    $template             = CmeKernel::Template()->get($templateId);
    $response['template'] = "";
    if($template)
    {
      $response['template'] = $template->content;
    }
    return Response::json($response);
  }
}
