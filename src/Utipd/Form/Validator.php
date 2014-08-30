<?php

namespace Utipd\Form;

use Exception;
use Respect\Validation\Validatable;
use Symfony\Component\HttpFoundation\Request;
use Utipd\Form\Exception\FormException;
use Utipd\Form\Sanitizer;

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

    public function validateRequest(Request $request) {
        return $this->validateSubmittedData($request->request->all());
    }

    public function validateSubmittedData($submitted_data) {
        $validated_data = [];

        $errors = [];
        foreach($this->validation_spec as $_n => $spec) {
            $value = isset($submitted_data[$spec['name']]) ? $submitted_data[$spec['name']] : null;

            if (isset($spec['validation'])) {
                $should_validate = true;
                if (isset($spec['validationFilter'])) {
                    $should_validate = call_user_func($spec['validationFilter'], $submitted_data);
                }

                if ($should_validate) {

                    $is_valid = false;
                    if ($spec['validation'] instanceof Validatable) {
                        $is_valid = $spec['validation']->validate($value);
                    } else if (is_callable($spec['validation'])) {
                        $is_valid = call_user_func($spec['validation'], $value);
                    }

                    if (!$is_valid) {
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
            throw new FormException($errors);
        }

        // no errors
        return $validated_data;
    }

    public function sanitizeRequestVars(Request $request) {
        return $this->sanitizeSubmittedData($request->request->all());
    }

    public function sanitizeSubmittedData($submitted_data) {
        $sanitized_data = [];

        foreach($this->validation_spec as $_n => $spec) {
            $value = isset($submitted_data[$spec['name']]) ? $submitted_data[$spec['name']] : null;

            if (isset($spec['sanitizer'])) {
                if ($spec['sanitizer'] instanceof Sanitizer) {
                    $value = $spec['sanitizer']->sanitize($value);
                } else if (is_callable($spec['sanitizer'])) {
                    $value = call_user_func($spec['sanitizer'], $value, $submitted_data);
                }
            }

            $sanitized_data[$spec['name']] = $value;
        }

        return $sanitized_data;
    } 

    public function getDefaultValues() {
        $defaults = [];

        foreach($this->validation_spec as $_n => $spec) {
            if (isset($spec['default'])) {
                $default = $spec['default'];
                $defaults[$spec['name']] = $default;
            }
        }

        return $defaults;
    }

}
