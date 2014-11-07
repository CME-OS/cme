<?php
/**
 * @author luke.rodham
 */

namespace MyTechJob\Validation;

use Illuminate\Validation\Factory as Validator;
use MyTechJob\Exceptions\FormValidationException;

class BaseValidation
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    private $validation;

    /**
     * @var Validator
     */
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $formData
     *
     * @throws \MyTechJob\Exceptions\FormValidationException
     * @return $this
     */
    public function validate(array $formData)
    {
        $this->validation = $this->validator->make(
            $formData,
            $this->getValidationRules(),
            $this->getCustomMessages()
        );

        if ($this->validation->fails()) {
            throw new FormValidationException('Validation Failed', $this->getValidationErrors());
        }

        return true;
    }

    /**
     * Get the validation rules array.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->rules;
    }

    /**
     * Get the custom messages array
     *
     * @return array
     */
    private function getCustomMessages()
    {
        return isset($this->customMessages) ? $this->customMessages : [];
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function getValidationErrors()
    {
        return $this->validation->errors();
    }
} 
