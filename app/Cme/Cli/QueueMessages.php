<?php
namespace Cme\Cli;

use Cme\Helpers\CampaignHelper;
use Cme\Helpers\FilterHelper;
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
  protected $name = 'cme:queue-messages';

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
    $instanceName   = $this->_getInstanceName();
    $lockedCampaign = null;
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
          $queueRequest   = $result[0];
          $lockedCampaign = $queueRequest->campaign_id;

          //grab the campaign
          $campaign = head(
            DB::select(
              sprintf(
                "SELECT * FROM campaigns WHERE id=%d",
                $lockedCampaign
              )
            )
          );

          //get the brand
          $brand = head(
            DB::select(
              sprintf(
                "SELECT * FROM brands WHERE id=%d",
                $campaign->brand_id
              )
            )
          );

          //process list in chunks
          $listTable = 'list_' . $queueRequest->list_id;
          //TODO check that table exists before attempting to query it

          $lastId       = 0;
          $placeHolders = null;
          do
          {

            $filters   = json_decode($campaign->filters);
            $filterSql = FilterHelper::buildSql($filters);
            if($filterSql != "")
            {
              $filterSql = ' AND ' . $filterSql;
            }

            Log::debug("Fetching Subscribers");
            $subscribers = DB::select(
              sprintf(
                "SELECT * FROM %s WHERE id > %d
                 AND id BETWEEN %d AND %d
                 $filterSql
                 LIMIT 1000",
                $listTable,
                $lastId,
                $queueRequest->start,
                $queueRequest->end
              )
            );

            foreach($subscribers as $subscriber)
            {
              //check if user is unsubscribed or bounced
              $unsubscribed = DB::table('unsubscribes')
                ->where(
                  [
                    'email'    => $subscriber->email,
                    'brand_id' => $campaign->brand_id
                  ]
                )
                ->first();

              $bounced = DB::table('bounces')
                ->where('email', '=', $subscriber->email)
                ->first();

              if(!$unsubscribed && !$bounced)
              {
                $message = CampaignHelper::compileMessage(
                  $campaign,
                  $brand,
                  $subscriber
                );

                list($fromName, $fromEmail) = explode(' <', $campaign->from);

                //write to message queue
                $message = [
                  'subject'       => $campaign->subject,
                  'from_name'     => $fromName,
                  'from_email'    => trim($fromEmail, '<>'),
                  'to'            => $subscriber->email,
                  'html_content'  => $message->html,
                  'text_content'  => $message->text,
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
              }

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
          //stick to a campaign until we are done queuing it
          $campaignCondition = "";
          if($lockedCampaign !== null)
          {
            $campaignCondition = "AND campaign_id = " . $lockedCampaign;
          }

          //lock a row
          $lockedARow = DB::update(
            "UPDATE ranges SET locked_by=?
            WHERE locked_by IS NULL $campaignCondition ORDER BY created ASC LIMIT 1",
            [$instanceName]
          );
          if(!$lockedARow)
          {
            $this->info("sleeping for a bit");

            if($lockedCampaign !== null)
            {
              $stillQueuing = DB::select(
                "SELECT * FROM ranges WHERE locked_by IS NOT NULL $campaignCondition LIMIT 1"
              );
              if(!$stillQueuing)
              {
                DB::table('campaigns')
                  ->where(['id' => $lockedCampaign])
                  ->update(
                    ['status' => 'Queued']
                  );
              }

              $lockedCampaign = null;
            }

            sleep(5);
          }
        }
      }
      while($result);
    }
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
