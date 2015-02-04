<?php
namespace Cme\Cli;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;

class QueueMessages extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'command:queue-messages';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Process ranges table and compile and queue email messages';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    $this->_createPIDFile();
    $instanceName = $this->_getInstanceName();
    while(true)
    {
      do
      {
        $result = DB::select(
          sprintf(
            "SELECT * FROM ranges WHERE locked_by='%s' ORDER BY created ASC LIMIT 1",
            $instanceName
          )
        );

        if($result)
        {
          $queueRequest = $result[0];

          //grab the campaign
          $campaign = DB::select(
            sprintf(
              "SELECT * FROM campaigns WHERE id=%d",
              $queueRequest->campaign_id
            )
          );
          $campaign = $campaign[0];

          //process list in chunks
          $listTable = 'list_' . $queueRequest->list_id;
          //TODO check that table exists before attempting to query it

          $lastId       = 0;
          $placeHolders = null;
          do
          {
            Log::debug("Fetching Subscribers");
            $subscribers = DB::select(
              sprintf(
                "SELECT * FROM %s WHERE id > %d
                 AND id BETWEEN %d AND %d
                 AND bounced=0 AND unsubscribed=0
                 LIMIT 1000",
                $listTable,
                $lastId,
                $queueRequest->start,
                $queueRequest->end
              )
            );

            foreach($subscribers as $subscriber)
            {
              if($placeHolders == null)
              {
                Log::debug("Building placeholders");
                $columns = array_keys((array)$subscriber);
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
              }

              //parse and compile message (replacing placeholders if any)
              Log::debug("Parsing and compiling messages");
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
                'to'            => $subscriber->email,
                'html_content'  => $html,
                'text_content'  => $text,
                'subscriber_id' => $subscriber->id,
                'list_id'       => $queueRequest->list_id,
                'brand_id'      => $campaign->brand_id,
                'campaign_id'   => $campaign->id,
                'send_time'     => $campaign->send_time,
                'send_priority' => $campaign->send_priority
              ];
              Log::debug("Queuing message for " . $subscriber->email);
              DB::table('message_queue')->insert($message);

              //add to campaign events table
              $event = [
                'campaign_id'   => $campaign->id,
                'list_id'       => $queueRequest->list_id,
                'subscriber_id' => $subscriber->id,
                'event_type'    => 'queued',
                'time'          => time()
              ];
              DB::table('campaign_events')->insert($event);

              $lastId = $subscriber->id;
            }
          }
          while($subscribers);

          DB::table('ranges')
            ->where(
              [
              'list_id'     => $queueRequest->list_id,
              'campaign_id' => $queueRequest->campaign_id,
              'start'       => $queueRequest->start
              ]
            )
            ->delete();
        }
        else
        {
          //lock a row
          $lockedARow = DB::update(
            "UPDATE ranges SET locked_by=?
            WHERE locked_by IS NULL ORDER BY created ASC LIMIT 1",
            [$instanceName]
          );
          if(!$lockedARow)
          {
            $this->info("sleeping for a bit");
            sleep(5);
          }
        }
      }
      while($result);
    }
  }

  private function _getUnsubscribeUrl($url, $campaignId, $listId, $subscriberId)
  {
    $domain = Config::get('app.domain');
    $url    = "http://" . $domain . "/track/unsubscribe/" . $campaignId
      . "_" . $listId . "_" . $subscriberId . "/" . base64_encode($url);

    return $url;
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('inst', InputArgument::REQUIRED, 'Instance Name'),
    );
  }

  private function _getInstanceName()
  {
    $inst = $this->argument('inst');
    return gethostname() . '-' . $inst;
  }
}