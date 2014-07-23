<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Lowercase
*/
class Lowercase
{

    public static function buildSanitizer() {
        return function($in) {
            return strtolower($in);
        };
    }

}

