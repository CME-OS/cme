<?php
namespace Cme\Cli;

use Aws\Ec2\Ec2Client;
use Illuminate\Support\Facades\Config;

class InstallCommander extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:install-commander';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'This will create EC2 instance and deploy commander';

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
        . " \n\nYou can get this from your AWS console online"
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
    $this->_installCommander();
  }

  private function _installCommander()
  {
    $dryRun    = false;
    $keyName   = 'cme-commander';
    $ec2Client = Ec2Client::factory($this->_awsCredentials);
    if($ec2Client)
    {
      //Create Keys
      /*$result = $ec2Client->createKeyPair(
        array(
          'DryRun'  => $dryRun,
          // KeyName is required
          'KeyName' => $keyName,
        )
      );

      //maybe write this to a file
      $privateKey = $result->get('KeyMaterial').PHP_EOL;
      file_put_contents($keyName.'.pem', $privateKey);
      */

      //Create Security Group to allow on SSH connection
      /*$result = $ec2Client->createSecurityGroup(array(
          'DryRun' => $dryRun,
          'GroupName' => 'CME Security Group',
          'Description' => 'SSH Only',
        ));

      $groupId = $result->get('GroupId');*/


      //Create Instance
      $result = $ec2Client->runInstances(
        array(
          'DryRun'                            => $dryRun,
          'ImageId'                           => 'ami-f6b11181',
          'MinCount'                          => 1,
          'MaxCount'                          => 1,
          'KeyName'                           => $keyName,
          /*'UserData'                          => 'string',*/
          'InstanceType'                      => 't1.micro',
          'BlockDeviceMappings'               => array(
            array(
              'VirtualName' => 'ephemeral0',
              'DeviceName'  => '/dev/sda2',
              'Ebs'         => array(
                /*'SnapshotId'          => 'ebs-cme-1',*/
                'VolumeSize'          => 20,
                'DeleteOnTermination' => true,
                'VolumeType'          => 'gp2',
                /*'Iops'                => 60,*/
                'Encrypted'           => false,
              ),
              /*'NoDevice'    => 'string',*/
            ),
          ),
          'Monitoring'                        => array(
            'Enabled' => false,
          ),
          /*'SubnetId'                          => 'string',*/
          'DisableApiTermination'             => false, //remove since default is false?
          'InstanceInitiatedShutdownBehavior' => 'stop', //remove since default is what we want?
          'ClientToken'                       => 'CME-INSTALLER-1',
          /*'AdditionalInfo'                    => 'string',*/
          /*'IamInstanceProfile'                => array(
            'Arn'  => 'string',
            'Name' => 'CME-COMM-TEST',
          ),*/
          'EbsOptimized'                      => false,
        )
      );

      $instances = $result->get('Instances');
      $instanceId = $instances[0]['InstanceId'];

      //Tag the instance
      $result = $ec2Client->createTags(array(
          'Resources' => array($instanceId),
          'Tags' => array(
            array(
              'Key' => 'Name',
              'Value' => 'CME Commander - Created by Script**'
            )
          )
        ));
    }
    else
    {
      $this->error('Installer could not talk to EC2');
    }




  }




}
