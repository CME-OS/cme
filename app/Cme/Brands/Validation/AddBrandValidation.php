<?php
namespace Cme\Brands\Validation;

use Cme\Base\Validation\BaseValidation;

class AddBrandValidation extends BaseValidation
{
  /**
   * todo: update the sender email to check for uniqueness.
   * this will be implemented when we use models/mappers
   */
  protected $rules = [
    'brand_name'            => 'required',
    'brand_sender_name'     => 'required',
    'brand_sender_email'    => 'required|email',
    'brand_domain_name'     => 'required',
    'brand_unsubscribe_url' => 'required'
  ];
}
