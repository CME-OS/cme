<?php


namespace Cme\Base\Exceptions;


class FormValidationException extends \Exception
{
  /**
   * @var mixed
   */
  private $errors;

  /**
   * @param string $message
   * @param mixed $errors
   */
  public function __construct($message, $errors)
  {
    $this->errors = $errors;

    parent::__construct($message);
  }

  /**
   * @return mixed
   */
  public function getErrors()
  {
    return $this->errors;
  }
}
