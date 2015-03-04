<?php
namespace Cme\Cli;

use Aws\Ec2\Ec2Client;
use Cme\Lib\Cli\CmeCommand;
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
   * @var Ec2Client $_ec2Client ;
   */
  private $_ec2Client;
  private $_instanceId;
  private $_ec2PublicDns;
  private $_ec2PublicIp;
  private $_ec2Status;
  private $_keyName = 'cme-commander';

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
    $this->_ec2Client = Ec2Client::factory($this->_awsCredentials);
    $this->_installCommander();
  }

  private function _installCommander()
  {
    $this->_setupEc2();
    $status = $this->_ec2Status;
    while($status != 16)
    {
      echo "Checking if EC2 server is ready for deployment..." . PHP_EOL;
      $status = $this->_checkEc2Status();
      sleep(1);
    }
    echo "EC2 is ready to host Commander!" . PHP_EOL;
    if($status == 16)
    {
      $this->_deployCommander();
    }
  }

  private function _deployCommander()
  {
    if(file_exists($this->_keyName . '.pem'))
    {
      $cmeCommanderSrc = "cme-commander.7z";
      $runCmds         = [
        "ssh -i cme-commander.pem ubuntu@{$this->_ec2PublicIp} sudo apt-get update",
        "ssh -i cme-commander.pem ubuntu@{$this->_ec2PublicIp} sudo apt-get install p7zip",
        "scp -i cme-commander.pem $cmeCommanderSrc ubuntu@{$this->_ec2PublicIp}:$cmeCommanderSrc",
        "ssh -i cme-commander.pem ubuntu@{$this->_ec2PublicIp} p7zip -d $cmeCommanderSrc",
        "ssh -i cme-commander.pem ubuntu@{$this->_ec2PublicIp} chmod +x cme-commander/install.sh",
        "ssh -i cme-commander.pem ubuntu@{$this->_ec2PublicIp} chmod +x cme-commander/purge.sh",
        "ssh -i cme-commander.pem ubuntu@{$this->_ec2PublicIp} sudo cme-commander/install.sh",
      ];

      foreach($runCmds as $cmd)
      {
        $output = [];
        $return = null;
        echo "Running: " . $cmd . PHP_EOL;
        exec($cmd, $output, $return);
        if($return == 0)
        {
          echo implode("\n", $output) . PHP_EOL;
        }
      }
      echo "CME Commander is deployed and ready!" . PHP_EOL;
    }
    else
    {
      echo "CME Commander cannot be deployed without a private key" . PHP_EOL;
    }
  }

  private function _checkEc2Status()
  {
    $result   = $this->_ec2Client->describeInstanceStatus(
      [
        'DryRun'      => false,
        'InstanceIds' => array($this->_instanceId),
      ]
    );
    $statuses = $result->get('InstanceStatuses');
    return $statuses[0]['InstanceState']['Code'];
  }

  private function _setupEc2()
  {
    $dryRun = false;
    if($this->_ec2Client)
    {
      //Create Keys
      if(!file_exists($this->_keyName . '.pem'))
      {
        $result = $this->_ec2Client->createKeyPair(
          array(
            'DryRun'  => $dryRun,
            'KeyName' => $this->_keyName,
          )
        );

        //maybe write this to a file
        $privateKey = $result->get('KeyMaterial') . PHP_EOL;
        file_put_contents($this->_keyName . '.pem', $privateKey);
      }

      //Create Security Group to allow on SSH connection
      $result  = $this->_ec2Client->createSecurityGroup(
        array(
          'DryRun'      => $dryRun,
          'GroupName'   => 'CME Security Group',
          'Description' => 'SSH Only',
        )
      );
      $groupId = $result->get('GroupId');

      $response = $this->_ec2Client->authorizeSecurityGroupIngress(
        array(
          'GroupId'       => $groupId,
          'IpPermissions' => array(
            array(
              'IpProtocol' => 'tcp',
              'FromPort'   => '22',
              'ToPort'     => '22',
            )
          )
        )
      );

      //Create Instance
      $result = $this->_ec2Client->runInstances(
        array(
          'DryRun'                            => $dryRun,
          'ImageId'                           => 'ami-f6b11181',
          'MinCount'                          => 1,
          'MaxCount'                          => 1,
          'KeyName'                           => $this->_keyName,
          'InstanceType'                      => 't1.micro',
          'Monitoring'                        => array(
            'Enabled' => false,
          ),
          'DisableApiTermination'             => false,
          'InstanceInitiatedShutdownBehavior' => 'stop',
          'ClientToken'                       => 'CME-INSTALLER-3',
          'EbsOptimized'                      => false,
        )
      );

      $instances           = $result->get('Instances');
      $this->_instanceId   = $instances[0]['InstanceId'];
      $this->_ec2PublicDns = $instances[0]["PublicDnsName"];
      $this->_ec2PublicIp  = $instances[0]['PublicIpAddress'];
      $this->_ec2Status    = $instances[0]['State']['Code'];

      //Tag the instance
      $this->_ec2Client->createTags(
        array(
          'Resources' => array($this->_instanceId),
          'Tags'      => array(
            array(
              'Key'   => 'Name',
              'Value' => 'CME Commander - Created by Script'
            )
          )
        )
      );
    }
    else
    {
      $this->error('Installer could not talk to EC2');
    }
  }
}
