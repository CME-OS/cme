<?php
namespace Cme\Cli;

use Aws\Sqs\SqsClient;
use Illuminate\Support\Facades\Config;

class ProcessBouncedMessages extends CmeDbCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:process-bounces';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Process Bounced Messages by consuming SQS queue';

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
    $sqsClient = SqsClient::factory(
      array(
        'credentials' => array(
          'key'    => Config::get('cme.aws_key'),
          'secret' => Config::get('cme.aws_secret'),
        ),
        'region'      => Config::get('cme.aws_region')
      )
    );

    $queueUrl = Config::get('cme.sqs_bounce_queue');

    $result = $sqsClient->receiveMessage(
      [
        'QueueUrl'            => $queueUrl,
        'MaxNumberOfMessages' => 1
      ]
    );

    echo $result->count() . PHP_EOL;
    if($result->count() > 1)
    {
      foreach($result->getPath('Messages/*/Body') as $messageBody)
      {
        // Do something with the message
        $messageObj     = json_decode($messageBody);
        $messageDetails = json_decode($messageObj->Message);
        print_r($messageDetails);
        //$messageObj->Type //Notification
        $this->info($messageObj->MessageId);

        //$messageDetails->notificationType e.g Bounce
        //$messageDetails->bounce->bounceSubType e.g General
        //$messageDetails->bounce->bounceType e.g Permanent
        //$messageDetails->bounce->bouncedRecipients // array of bounces and reasons
        //[[status, action, diagnosticCode, emailAddress]]
        //$messageDetails->mail->destination //this is an array on email addresses
      }

      foreach($result->getPath('Messages/*/ReceiptHandle') as $ReceiptHandle)
      {
        $sqsClient->deleteMessage(
          ['QueueUrl' => $queueUrl, 'ReceiptHandle' => $ReceiptHandle]
        );
      }
    }
    else
    {
      $this->info("no result returned");
    }
  }
}
