<?php

namespace Utipd\Form\Sanitizers;

use Exception;

/*
* Percentage
*/
class Percentage
{

    public static function buildSanitizer() {
        return function($in) {
            $in_val = round(floatval(rtrim($in, '%')), 5);
            $out = rtrim(rtrim(sprintf('%1f', $in_val),'0'), '.');
            return $out.'%';
        };
    }

}

