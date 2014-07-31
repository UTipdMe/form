<?php

namespace Utipd\Form\Exception;

use Exception;

/*
* FormException
*/
class FormException extends Exception
{

    public function __construct($display_error, $code=0, $internal_error_message=null) {
        $this->setDisplayErrors(is_array($display_error) ? $display_error : [$display_error]);

        $message = $internal_error_message;
        if ($internal_error_message === null) { $message = $this->getDisplayErrorsAsString(); }
        parent::__construct($message, $code);
    }

    public function setDisplayErrors($display_errors) {
        $this->display_errors = $display_errors;
    }

    public function getDisplayErrorsAsArray() {
        if (!$this->display_errors) { return []; }

        $errors = $this->display_errors;
        if (!is_array($errors)) { $errors = [$errors]; }
        return $errors;
    }

    public function getDisplayErrorsAsHTML($class_name='error') {
        $errors = $this->getDisplayErrorsAsArray();
        if (!$errors) { return ''; }
        return '<div class="'.$class_name.'">'.implode('</div>'."\n".'<div class="'.$class_name.'">', $errors).'</div>';
    }

    public function getDisplayErrorsAsString() {
        $errors = $this->getDisplayErrorsAsArray();
        if (!$errors) { return ''; }
        return implode("\n", $errors);
    }

}
