<?php

namespace Utipd\Form;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use InvalidArgumentException;

/*
* Validator
*/
class Validator
{
    protected $validation_spec = null;

    public function __construct($validation_spec)
    {
        $this->validation_spec = $validation_spec;
    }

    public function validateRequestVars(Request $request) {
        return $this->validateSubmittedData($request->request->all());
    }

    public function validateSubmittedData($submitted_data) {
        $validated_data = array();

        $errors = array();
        foreach($this->validation_spec as $_n => $spec) {
            $value = $submitted_data[$spec['name']];

            if (isset($spec['validation'])) {
                $should_validate = true;
                if (isset($spec['validationFilter'])) {
                    $should_validate = call_user_func($spec['validationFilter'], $submitted_data);
                }

                if ($should_validate) {
                    if (!$spec['validation']->validate($value)) {
                        $errors[] = isset($spec['error']) ? $spec['error'] : "There was a problem with the field ".(isset($spec['label']) ? $spec['label'] : $spec['name'])."";
                        $value = null;
                    } else {
                        if (isset($spec['postValidation'])) {
                            call_user_func($spec['postValidation'], $value, $submitted_data);
                        }
                    }

                }


            }

            if ($value !== null) {
                $validated_data[$spec['name']] = $value;
            }
        }

        if ($errors) {
            throw new InvalidArgumentException(implode("\n", $errors));
        }

        // no errors
        return $validated_data;
    }

    public function sanitizeRequestVars(Request $request) {
        return $this->sanitizeSubmittedData($request->request->all());
    }

    public function sanitizeSubmittedData($submitted_data) {
        $sanitized_data = array();

        foreach($this->validation_spec as $_n => $spec) {
            $value = $submitted_data[$spec['name']];

            if (isset($spec['sanitizer'])) {
                $value = $spec['sanitizer']->sanitize($value);
            }

            $sanitized_data[$spec['name']] = $value;
        }

        return $sanitized_data;
    } 

}
