<?php
/**
 * @author  User
 */

namespace App\Cme\Lib\SmtpProviders;

use App\Cme\Lib\Campaign\MessageId;

abstract class AbstractSmtpProvider
{
  protected $_host;
  protected $_username;
  protected $_password;
  protected $_port;

  /**
   * @var $_mailer
   */
  protected $_mailer;

  protected function sendEmail()
  {
    //default to sending email using PHPMailer
  }

  /**
   * @return MessageId[]
   */
  protected function processBounce()
  {
    return [
      new MessageId()
    ];
  }

}
