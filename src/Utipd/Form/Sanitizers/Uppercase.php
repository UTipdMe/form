<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Uppercase
*/
class Uppercase
{

    public static function buildSanitizer() {
        return function($in) {
            return strtoupper($in);
        };
    }

}

