<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Trim
*/
class Trim
{

    public static function buildSanitizer() {
        return function($in) {
            return trim($in);
        };
    }

}
