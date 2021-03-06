<?php
namespace App\Cme\Cli;

use Aws\Ec2\Ec2Client;
use App\Cme\Lib\Cli\CmeCommand;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;

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
  private $_groupId;
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
  public function handle()
  {
    $this->_validateAws();
    $this->_ec2Client = Ec2Client::factory($this->_awsCredentials);
    $this->_installCommander();
  }

  private function _installCommander()
  {
    $this->_setupEc2();
    $status = $this->_ec2Status;
    echo "Checking if EC2 server is ready for deployment" . PHP_EOL;
    while($status != 16)
    {
      echo "...\r";
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
    if(file_exists($this->_keyName . '.pem') && file_exists(
        'commander.config.php'
      )
    )
    {
      $cmeCommanderSrc = "cme-commander";
      $runCmds         = [
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} sudo apt-get update",
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} sudo apt-get install -y p7zip",
        "scp -i {$this->_keyName}.pem $cmeCommanderSrc.7z ubuntu@{$this->_ec2PublicDns}:$cmeCommanderSrc.7z",
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} sudo rm -Rf $cmeCommanderSrc",
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} p7zip -d $cmeCommanderSrc.7z",
        "scp -i {$this->_keyName}.pem commander.config.php ubuntu@{$this->_ec2PublicDns}:/home/ubuntu/cme-commander/commander.config.php",
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} chmod +x cme-commander/install.sh",
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} chmod +x cme-commander/purge.sh",
        "ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns} sudo cme-commander/install.sh",
      ];

      foreach($runCmds as $cmd)
      {
        $output = [];
        $return = null;

        $tries    = 0;
        $maxTries = 5;
        do
        {
          $this->info("Running ($tries): " . $cmd);
          exec($cmd, $output, $return);
          if($return == 0)
          {
            echo implode("\n", $output) . PHP_EOL;
          }
          $tries++;
          sleep(1);
        }
        while($return != 0 && $tries < $maxTries);
      }
      $this->info("CME Commander is deployed and ready!");
      $this->info(
        "To login RUN: ssh -i {$this->_keyName}.pem ubuntu@{$this->_ec2PublicDns}"
      );
    }
    else
    {
      $this->error("CME Commander cannot be deployed without a private key");
      $this->error(
        "Make sure you have both {$this->_keyName}.pem and commander.config.php files"
      );
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
    $return   = 0;
    if(isset($statuses[0]))
    {
      $return = $statuses[0]['InstanceState']['Code'];
    }

    return $return;
  }

  private function _setupEc2()
  {
    $dryRun = false;
    if($this->_ec2Client)
    {
      //Create Keys
      if(!file_exists($this->_keyName . '.pem'))
      {
        $this->info("Creating public/private key pairs");
        $result = $this->_ec2Client->createKeyPair(
          array(
            'DryRun'  => $dryRun,
            'KeyName' => $this->_keyName,
          )
        );

        //maybe write this to a file
        $privateKey = $result->get('KeyMaterial') . PHP_EOL;
        file_put_contents($this->_keyName . '.pem', $privateKey);
        //make private key file read only
        chmod($this->_keyName . '.pem', 400);
      }

      //Create Security Group to allow on SSH connection
      try
      {
        $this->info("Creating security group to allow only SSH connections");
        $result         = $this->_ec2Client->createSecurityGroup(
          array(
            'DryRun'      => $dryRun,
            'GroupName'   => 'CME Security Group',
            'Description' => 'SSH Only',
          )
        );
        $this->_groupId = $result->get('GroupId');

        try
        {
          $this->_ec2Client->authorizeSecurityGroupIngress(
            array(
              'GroupId'       => $this->_groupId,
              'IpPermissions' => array(
                array(
                  'IpProtocol' => 'tcp',
                  'FromPort'   => 22,
                  'ToPort'     => 22,
                  'IpRanges'   => array(
                    array(
                      'CidrIp' => '0.0.0.0/0',
                    )
                  ),
                ),

              )
            )
          );
        }
        catch(\Exception $e)
        {
          $this->info("CME: " . $e->getMessage());
        }
      }
      catch(\Exception $e)
      {
        $result         = $this->_ec2Client->describeSecurityGroups(
          array(
            'DryRun'     => $dryRun,
            'GroupNames' => array('CME Security Group'),
          )
        );
        $securityGroups = $result->get('SecurityGroups');
        $this->_groupId = $securityGroups[0]['GroupId'];
        $this->info("CME: " . $e->getMessage());
      }

      $this->info("Launching an instance to host Commander");
      $this->info("Waiting for instance to start up");
      do
      {
        //Create Instance
        $result    = $this->_ec2Client->runInstances(
          array(
            'DryRun'                            => $dryRun,
            'ImageId'                           => 'ami-f6b11181',
            'MinCount'                          => 1,
            'MaxCount'                          => 1,
            'KeyName'                           => $this->_keyName,
            'SecurityGroupIds'                  => array($this->_groupId),
            'InstanceType'                      => 't1.micro',
            'Monitoring'                        => array(
              'Enabled' => false,
            ),
            'DisableApiTermination'             => false,
            'InstanceInitiatedShutdownBehavior' => 'stop',
            'ClientToken'                       => 'CME-INSTALLER-' . $this->option(
                'instance-id'
              ),
            'EbsOptimized'                      => false,
          )
        );
        $instances = $result->get('Instances');
        echo "...\r";
        sleep(1);
      }
      while($instances[0]["PublicDnsName"] == "");

      $this->_instanceId   = $instances[0]['InstanceId'];
      $this->_ec2PublicDns = $instances[0]["PublicDnsName"];
      $this->info("Public DNS: " . $this->_ec2PublicDns);
      $this->_ec2PublicIp = $instances[0]['PublicIpAddress'];
      $this->_ec2Status   = $instances[0]['State']['Code'];

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

  protected function getOptions()
  {
    return [
      [
        'instance-id',
        'i',
        InputOption::VALUE_REQUIRED,
        'Instance Id used to build a Client Token'
      ]
    ];
  }
}
