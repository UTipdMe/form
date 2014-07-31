<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Int
*/
class Int
{

    public static function buildSanitizer() {

        return function($in) {
            return intval(preg_replace('![^0-9.-]!', '', $in));
        };

    }

}

