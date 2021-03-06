<?php
namespace App\Cme\Cli;

use App\Cme\Lib\Cli\LongRunningScript;
use CmeKernel\Core\CmeKernel;
use CmeKernel\Helpers\CampaignHelper;
use CmeKernel\Helpers\FilterHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class QueueMessages extends LongRunningScript
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
  public function handle()
  {
    $this->_init();
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
          $campaign = CmeKernel::Campaign()->get($lockedCampaign);

          //get the brand
          $brand = CmeKernel::Brand()->get($campaign->brandId);

          //process list in chunks
          $listTable = 'list_' . $queueRequest->list_id;
          //TODO check that table exists before attempting to query it

          $lastId       = 0;
          $placeHolders = null;
          do
          {
            $filterSql = FilterHelper::buildSql($campaign->filters);
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
                    'brand_id' => $campaign->brandId
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
                  (array)$subscriber
                );

                list($fromName, $fromEmail) = explode(' <', $campaign->from);
                $fromEmail = trim($fromEmail, '<>');

                $label = $this->option('label-sender');
                if($label)
                {
                  $messageId = implode(
                    '.',
                    [$campaign->id, $campaign->list_id, $subscriber->id]
                  );
                  //add label to fromEmail so we can track bounces
                  $fromEmail = CampaignHelper::labelSender(
                    $fromEmail,
                    $messageId
                  );
                }

                //write to message queue
                $message = [
                  'subject'       => $campaign->subject,
                  'from_name'     => $fromName,
                  'from_email'    => $fromEmail,
                  'to'            => $subscriber->email,
                  'html_content'  => $message->html,
                  'text_content'  => $message->text,
                  'subscriber_id' => $subscriber->id,
                  'list_id'       => $queueRequest->list_id,
                  'brand_id'      => $campaign->brandId,
                  'campaign_id'   => $campaign->id,
                  'send_time'     => $campaign->sendTime,
                  'send_priority' => $campaign->sendPriority
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
      $this->_cronBailOut();
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

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    $options   = parent::getOptions();
    $options[] =
      [
        'label-sender',
        'l',
        InputOption::VALUE_OPTIONAL,
        'Whether sender should be labelled or not. '
        . 'Not all mail server support labelling'
      ];

    return $options;
  }

  private function _getInstanceName()
  {
    $inst = $this->argument('inst');
    return gethostname() . '-' . $inst;
  }
}
