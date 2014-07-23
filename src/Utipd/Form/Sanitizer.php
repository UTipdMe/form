<?php

namespace Utipd\Form;

use Exception;

/*
* Sanitizer
*/
class Sanitizer
{

    protected $sanitizer_chain = [];

    public static function __callStatic($rule, $arguments) {
        $sanitizer = new static();
        return $sanitizer->__call($rule, $arguments);
    }

    public function __call($rule, $arguments) {
        $class = '\\Utipd\\Form\\Sanitizers\\'.ucfirst($rule);
        $sanitizer_func = call_user_func_array(array($class, 'buildSanitizer'), $arguments);
        $this->addSanitizer($sanitizer_func);
        return $this;
    }


    public function sanitize($value) {
        $out = $value;
        foreach ($this->sanitizer_chain as $sanitizer_func) {
            $out = call_user_func($sanitizer_func, $out);
        }
        return $out;
    }

    public function addSanitizer($sanitizer_func) {
        $this->sanitizer_chain[] = $sanitizer_func;
    }


}
