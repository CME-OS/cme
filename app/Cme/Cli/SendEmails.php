<?php
namespace Cme\Cli;

use Cme\Lib\Cli\LongRunningScript;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SendEmails extends LongRunningScript
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:send-emails';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send emails due in message queue';

  /**
   * Used for caching SMTP connections
   *
   * @var array
   */
  private $_smtp = [];
  private $_batchSize = 100;
  private $_queueTable = 'message_queue';

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
    $this->_init();
    $this->_createPIDFile();
    $instanceName     = $this->_getInstanceName();
    $lockedCampaignId = null;

    $batchSize = $this->option('batch-size');
    if($batchSize)
    {
      $this->_batchSize = (int)$batchSize;
    }

    while(true)
    {
      //read jobs from queue
      $messages = DB::select(
        sprintf(
          'SELECT * FROM %s WHERE locked_by="%s"
            AND `status` ="%s" ORDER BY send_priority DESC LIMIT %d',
          $this->_queueTable,
          $instanceName,
          'pending',
          $this->_batchSize
        )
      );

      if($messages)
      {
        //process it
        foreach($messages as $message)
        {
          //trick to convert message from an array to an object
          $message = json_decode(json_encode($message));

          if($lockedCampaignId == null)
          {
            $lockedCampaignId = $message->campaign_id;
          }

          //send email
          $emailSent = $this->sendEmail(
            $lockedCampaignId,
            $message->to,
            $message->from_name,
            $message->from_email,
            $message->subject,
            $message->html_content
          );

          //unlock message and set the appropriate status
          $status = ($emailSent) ? 'Sent' : 'Failed';
          $sql    = sprintf(
            "UPDATE %s SET locked_by=NULL, `status`='%s'
              WHERE id=%d",
            $this->_queueTable,
            $status,
            $message->id
          );
          DB::update($sql);

          //update analytics
          DB::insert(
            sprintf(
              "INSERT INTO campaign_events (campaign_id, list_id, subscriber_id, event_type, time)
                VALUES (%d, %d, %d, '%s', %d)",
              $message->campaign_id,
              $message->list_id,
              $message->subscriber_id,
              strtolower($status),
              time()
            )
          );
        }
      }
      else
      {
        if($lockedCampaignId == null)
        {
          //pick a campaign and stick to it
          $sql = sprintf(
            "UPDATE %s SET locked_by='%s'
               WHERE locked_by IS NULL
               AND send_time < %d
               AND `status`='%s'
               ORDER BY send_priority DESC
               LIMIT %d",
            $this->_queueTable,
            $instanceName,
            time(),
            'Pending',
            1
          );
          DB::update($sql);

          $message = DB::select(
            sprintf(
              'SELECT campaign_id FROM %s WHERE locked_by="%s"
                AND `status` ="%s" ORDER BY send_priority DESC LIMIT 1',
              $this->_queueTable,
              $instanceName,
              'pending'
            )
          );

          if($message)
          {
            $lockedCampaignId = $message[0]->campaign_id;
          }
        }

        $sleep = true;
        if($lockedCampaignId)
        {
          $this->info("Locking some rows for campaign $lockedCampaignId");
          //lock some messages
          $sql = sprintf(
            "UPDATE %s SET locked_by='%s'
            WHERE locked_by IS NULL
            AND campaign_id = %d
            AND send_time < %d
            AND `status`='%s'
            ORDER BY send_priority DESC
            LIMIT %d",
            $this->_queueTable,
            $instanceName,
            $lockedCampaignId,
            time(),
            'Pending',
            $this->_batchSize
          );
          $affectedRows = DB::update($sql);
          $sleep = ($affectedRows == 0);
        }

        //if process could not lock any rows. Lets take a break
        //to avoid overloading the server
        if($sleep)
        {
          if($lockedCampaignId)
          {
            //set status of campaign to sending
            $sql = sprintf(
              "UPDATE campaigns SET `status`='%s'
               WHERE id = %d",
              'Sent',
              $lockedCampaignId
            );
            DB::update($sql);

            $lockedCampaignId = null;
          }

          sleep(5);
          $this->info(@date('Y-m-d H:i:s') . ": Sleeping for a bit");
        }
        else
        {
          //set status of campaign to sending
          $sql = sprintf(
            "UPDATE campaigns SET `status`='%s'
               WHERE id = %d",
            'Sending',
            $lockedCampaignId
          );
          DB::update($sql);
        }
      }
    }
    $this->_cronBailOut();
  }

  /**
   * Grab SMTP details
   *
   * First we grab the smtp_provider_id
   * then we grab the full details of that SMTP provider by
   * querying smtp_providers table
   *
   * @param $campaignId
   *
   * @throws \Exception
   */
  private function _loadSmtpConfig($campaignId)
  {
    if(!isset($this->_smtp[$campaignId]))
    {
      $this->info("Loading SMTP config for campaign ID " . $campaignId);

      $campaignSmtpProvider = DB::select(
        sprintf(
          'SELECT smtp_provider_id as id FROM %s WHERE id="%d"',
          'campaigns',
          $campaignId
        )
      );
      $smtpProviderId       = $campaignSmtpProvider[0]->id;
      if($smtpProviderId)
      {
        $query = sprintf(
          'SELECT * FROM %s WHERE id="%d"',
          'smtp_providers',
          $smtpProviderId
        );
      }
      else
      {
        //get default one smtp provider instead
        $query = sprintf(
          'SELECT * FROM %s WHERE `default`=1 LIMIT 1',
          'smtp_providers'
        );
      }

      $smtpProvider = DB::select($query);
      if($smtpProvider)
      {
        $smtpProvider = $smtpProvider[0];
        //cache it
        $this->_smtp[$campaignId]['host']     = $smtpProvider->host;
        $this->_smtp[$campaignId]['username'] = Crypt::decrypt(
          $smtpProvider->username
        );
        $this->_smtp[$campaignId]['password'] = Crypt::decrypt(
          $smtpProvider->password
        );
        $this->_smtp[$campaignId]['port']     = $smtpProvider->port;
        Log::error("Using " . $smtpProvider->name . " SMTP Provider");
      }
      else
      {
        throw new \Exception(
          "No SMTP Provider set for campaignID " . $campaignId
        );
      }
    }
  }

  private function sendEmail(
    $campaignId, $to, $fromName, $fromEmail, $subject, $body
  )
  {
    if($campaignId)
    {
      $this->_loadSmtpConfig($campaignId);
      $mailer = new \PHPMailer();
      $mailer->isSMTP();
      $mailer->SMTPAuth   = true;
      $mailer->SMTPSecure = 'tls';
      $mailer->Host       = $this->_smtp[$campaignId]['host'];
      $mailer->Username   = $this->_smtp[$campaignId]['username'];
      $mailer->Password   = $this->_smtp[$campaignId]['password'];
      $mailer->Port       = $this->_smtp[$campaignId]['port'];
      $mailer->isHTML(true);
      $mailer->addAddress($to);
      $mailer->From     = $fromEmail;
      $mailer->FromName = $fromName;
      $mailer->Subject  = $subject;
      $mailer->Body     = $body;
      if($mailer->send())
      {
        $this->info("Sending to $to");
        $return = true;
      }
      else
      {
        $this->info($mailer->ErrorInfo);
        Log::error($mailer->ErrorInfo);
        $return = false;
      }

      $mailer->clearAddresses();
      return $return;
    }
    else
    {
      die("I need a campaignId in order to load SMTP settings");
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
        'batch-size',
        'b',
        InputOption::VALUE_OPTIONAL,
        'Number of messages to process at a time'
      ];

    return $options;
  }

  private function _getInstanceName()
  {
    $inst = $this->argument('inst');
    return gethostname() . '-' . $inst;
  }
}
