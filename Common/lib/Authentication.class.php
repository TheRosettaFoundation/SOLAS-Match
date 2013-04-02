<?php

class Authentication {
    
    public static function hashPassword($password, $nonce)
    {
        // Thanks to http://stackoverflow.com/questions/401656/secure-hash-and-salt-for-php-passwords/401684#401684
        $site_key = Settings::get('session.site_key');
        return self::hash($password, $nonce, $site_key);
    }

    public static function generateNonce()
    {
        return mt_rand(0, self::maxNonceValue());
    }

    private static function maxNonceValue()
    {
        $mysql_max_int = 4294967295;
        $algo_max = mt_getrandmax();
        return min(array($mysql_max_int, $algo_max));
    }
    
    private static function hash($password, $nonce, $site_key)
    {
        return hash_hmac('sha512', $password . $nonce, $site_key);
    }
}
