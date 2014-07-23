<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Boolean
*/
class Boolean
{

    public static function buildSanitizer() {
        return function($in) {
            $in_val = strtolower(trim($in));
            switch ($in_val) {
                case '1':
                case 'y':
                case 'yes':
                case 't':
                case 'true':
                    return true;
                    break;
            }
            return false;
        };

    }

}

