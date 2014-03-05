<?php

namespace SolasMatch\Common\Lib;

class CacheHelper
{
    const STATISTICS            = "Statistics";
    const LANGUAGES             = "Languages";
    const COUNTRIES             = "Countries";
    const TOP_TAGS              = "TopTags";
    const GET_USER              = "GetUser";
    const SITE_LANGUAGE         = "SiteLanguage";
    const LOADED_LANGUAGES      = "LoadedLanguages";
    
    public static function getCached($key, $ttl, $function, $args = null)
    {
        $ret= null;
        if (!apc_exists($key)) {
            try {
                $ret = is_null($args) ? call_user_func($function) : call_user_func($function, $args);
                apc_add($key, $ret, $ttl);
            } catch (Exception $e) {
                error_log("Failed to add to cache ".$e->getMessage());
                $ret = null;
            }
        } else {
            $ret= apc_fetch($key);
        }
        return $ret;
    }
    
    public static function unCache($key)
    {
        if (apc_exists($key)) {
            apc_delete($key);
        }
    }
}
