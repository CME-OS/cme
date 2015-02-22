<?php
namespace Cme\Cli;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;

class SesTool extends CmeDbCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:ses-tool';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'SES Tool used for setting up CME to process Bounces';

  private $_awsCredentials;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  private function _validateAws()
  {
    $key    = Config::get('cme.aws_key');
    $secret = Config::get('cme.aws_secret');
    $region = Config::get('cme.aws_region');
    if(!$key || !$secret || !$region)
    {
      echo(
        "You need to set the following keys in your .env file\n"
        . " \n - aws_key, \n - aws_secret \n - aws_region"
        ." \n\nYou can get this from your AWS console online"
      );
      die;
    }

    $this->_awsCredentials = array(
      'credentials' => array(
        'key'    => $key,
        'secret' => $secret,
      ),
      'region'      => $region
    );
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    $this->_validateAws();
    $task = $this->argument('task');
    switch($task)
    {
      case 'setup':
        $this->_setupBounceTracking();
        break;
      case 'process-bounces':
        $this->_processBounces();
        break;
      default:
        die('Invalid task. Task allowed are setup|process-bounces');
    }
  }

  private function _setupBounceTracking()
  {
    $snsClient = SnsClient::factory($this->_awsCredentials);
    $sqsClient = SqsClient::factory($this->_awsCredentials);
    $sesClient = SesClient::factory($this->_awsCredentials);

    if($snsClient && $sqsClient && $sesClient)
    {
      //Create AN SNS Topic
      $cmeTopicArn = $snsClient->createTopic(
        [
          'Name' => 'CmeBounces'
        ]
      )->get('TopicArn');

      //get all verified domains
      $domains = $sesClient->listIdentities(
        array(
          'IdentityType' => 'Domain',
          'NextToken'    => '',
          'MaxItems'     => 10,
        )
      )->get('Identities');

      foreach($domains as $identity)
      {
        $this->info("linking $identity to SNS topic - " . $cmeTopicArn);
        //link bounce/complain handling to new SNS topic
        $sesClient->setIdentityNotificationTopic(
          array(
            'Identity'         => $identity,
            'NotificationType' => 'Bounce',
            'SnsTopic'         => $cmeTopicArn,
          )
        );

        sleep(1);

        //link bounce/complain handling to new SNS topic
        $sesClient->setIdentityNotificationTopic(
          array(
            'Identity'         => $identity,
            'NotificationType' => 'Complaint',
            'SnsTopic'         => $cmeTopicArn,
          )
        );

        sleep(1);
      }

      $this->info("Done setting up SNS topics for all verified domains");

      //create SQS Queue
      $sqsQueueUrl = $sqsClient->createQueue(
        [
          'QueueName' => 'cmeQueue'
        ]
      )->get('QueueUrl');

      //get the Queue's ARN
      $attributes  = $sqsClient->getQueueAttributes(
        [
          'QueueUrl'       => $sqsQueueUrl,
          'AttributeNames' => ['QueueArn']
        ]
      )->get('Attributes');
      $sqsQueueArn = $attributes['QueueArn'];

      //Subscribe to SQS queue
      $snsClient->subscribe(
        [
          'TopicArn' => $cmeTopicArn,
          'Protocol' => 'sqs',
          'Endpoint' => $sqsQueueArn
        ]
      );

      //add permission policy so that SNS can push to SQS queue
      $policy = [
        'Version'   => '2008-10-17',
        'Id'        => 'CME-SQS-POLICY',
        'Statement' => [
          [
            'Sid'       => 'Sid' . time(),
            'Effect'    => 'Allow',
            'Principal' => ['AWS' => "*"],
            'Action'    => 'SQS:SendMessage',
            'Resource'  => $sqsQueueArn,
            'Condition' => ["ArnEquals" => ['aws:SourceArn' => $cmeTopicArn]]
          ]
        ]
      ];

      $sqsClient->setQueueAttributes(
        [
          'QueueUrl'   => $sqsQueueUrl,
          'Attributes' => ['Policy' => json_encode($policy)]
        ]
      );
    }
    else
    {
      $this->error('SES Tool could not talk to all SQS, SES, SNS');
    }
  }

  private function _processBounces()
  {
    $sqsClient = SqsClient::factory($this->_awsCredentials);

    $sqsQueueUrl = $sqsClient->createQueue(
      [
        'QueueName' => 'cmeQueue'
      ]
    )->get('QueueUrl');

    $result = $sqsClient->receiveMessage(
      [
        'QueueUrl'            => $sqsQueueUrl,
        'MaxNumberOfMessages' => 10
      ]
    );

    if($result->count() > 1)
    {
      foreach($result->getPath('Messages') as $message)
      {
        $messageObj = json_decode($message['Body']);
        if($messageObj)
        {
          $this->info("Processing Message ID: " . $messageObj->MessageId);
          $messageDetails = json_decode($messageObj->Message);
          if($messageObj->Type == 'Notification')
          {
            if(isset($messageDetails->notificationType)
              && $messageDetails->notificationType == 'Bounce'
            )
            {
              $bounceInfo  = $messageDetails->bounce;
              $sourceEmail = $messageDetails->mail->source;

              $name       = strstr($sourceEmail, '@', true);
              $campaignId = $listId = $subscriberId = null;
              if(strpos($name, '+') !== false)
              {
                list($name, $cmeMessageId) = explode('+', $name, 2);
                if($cmeMessageId)
                {
                  list(
                    $campaignId, $listId, $subscriberId
                    ) = explode('.', $cmeMessageId);
                }
              }

              $bouncedRecipients = $bounceInfo->bouncedRecipients;
              //print_r($bouncedRecipients); maybe store this alongside bounce
              foreach($bouncedRecipients as $recipient)
              {
                $exists = DB::table('bounces')->where(
                  ['email' => $recipient->emailAddress]
                )->get();

                if(!$exists)
                {
                  $insertedBounce = DB::table('bounces')->insert(
                    [
                      'email'       => $recipient->emailAddress,
                      'campaign_id' => $campaignId,
                      'time'        => time()
                    ]
                  );

                  if($campaignId && $listId && $subscriberId)
                  {
                    //add to campaign events table
                    $event = [
                      'campaign_id'   => $campaignId,
                      'list_id'       => $listId,
                      'subscriber_id' => $subscriberId,
                      'event_type'    => 'bounced',
                      'time'          => time()
                    ];
                    DB::table('campaign_events')->insert($event);
                  }

                  $this->info($message['ReceiptHandle']);
                  //remove message
                  if($insertedBounce)
                  {
                    $sqsClient->deleteMessage(
                      [
                        'QueueUrl'      => $sqsQueueUrl,
                        'ReceiptHandle' => $message['ReceiptHandle']
                      ]
                    );
                  }
                }
                else
                {
                  $this->info(
                    $recipient->emailAddress . " already exists in bounce list"
                  );
                }
              }
            }
            else
            {
              $sqsClient->deleteMessage(
                [
                  'QueueUrl'      => $sqsQueueUrl,
                  'ReceiptHandle' => $message['ReceiptHandle']
                ]
              );

              $type = isset($messageDetails->notificationType) ?
                ': ' . $messageDetails->notificationType : '';

              $this->error(
                "Unrecognized notification type" . $type
              );
            }
          }
        }
      }
    }
    else
    {
      $this->info("no result returned");
    }
  }

  protected function getArguments()
  {
    return array(
      array('task', InputArgument::REQUIRED, 'Task'),
    );
  }
}
