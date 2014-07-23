<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Capitalize
*/
class Capitalize
{

    public static function buildSanitizer() {
        return function($in) {
            return strtoupper($in);
        };
    }

}

