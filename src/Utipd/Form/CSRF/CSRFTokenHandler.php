<?php

namespace Utipd\Form\CSRF;

use Utipd\Session\SessionHandler;
use Utipd\Util\UserException;
use Symfony\Component\HttpFoundation\Request;
use Exception;

/*
* CSRFTokenHandler
*/
class CSRFTokenHandler
{

    const EXPIRE_TTL                = 14400; // forms can stay open for 4 hours
    const REFRESH_WINDOW_EXPIRE_TTL = 300;   // after 5 minutes, a refresh won't work

    public static function generateAndStoreToken() {
        SessionHandler::initSession();

        $token = self::generateToken();

        $csrfs = $_SESSION['csrfs'];
        if (!$csrfs) { $csrfs = array(); }
        $csrfs[$token] = time() + self::EXPIRE_TTL;
        $_SESSION['csrfs'] = $csrfs;

#        Debug::trace("\$_SESSION['csrfs']=",$_SESSION['csrfs'],__FILE__,__LINE__,$this);

        return $token;
    }

    public static function verifyFromRequest(Request $request) {
        $token = $request->request->get('_tok');
        if (!self::verifyToken($token)) {
            throw new UserException("This form was not valid.  Please try again.", 1);
        }

        self::expireToken($token);
    }

    public static function verifyToken($token) {
        $now = time();
        SessionHandler::initSession();

        $csrfs = $_SESSION['csrfs'];
        if (!$csrfs) { $csrfs = array(); }

#        Debug::trace("stored CSRF time=".($csrfs[$token])." now=".$now,__FILE__,__LINE__,$this);
        return isset($csrfs[$token]) AND ($csrfs[$token] > $now);
    }

    public static function expireToken($token) {
        SessionHandler::initSession();

        $csrfs = $_SESSION['csrfs'];
        if (!$csrfs) { $csrfs = array(); }

        $csrfs[$token] = time() + self::REFRESH_WINDOW_EXPIRE_TTL;

        foreach($csrfs as $old_token => $ttl) {
            if ($ttl <= time()) {
                unset($csrfs[$old_token]);
            }
        }

        $_SESSION['csrfs'] = $csrfs;
    }

    public static function generateToken($length=48){
        $token = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $len = 62;
        for($i=0;$i<$length;$i++){
            $token .= $chars[self::random(0,$len)];
        }
        return $token;
    }


    protected static function random($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

}

