<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Float
*/
class Float
{

    public static function buildSanitizer() {

        return function($in) {
            return floatval($in);
        };

    }

}

