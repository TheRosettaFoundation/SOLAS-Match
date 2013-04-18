<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";

class LanguageDao
{
    private $client;
    private $siteApi;
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getLanguage($id)
    {
        $request = "{$this->siteApi}v0/languages/$id";
        return $this->client->call("Language", $request);
    }
    
    public function getLanguages()
    {
        $request = "{$this->siteApi}v0/languages";
        $languages = null;
        if(apc_exists("languages")){ 
            $languages=apc_fetch("languages");
        }else{
            $languages=$this->client->call(array("Language"), $request);
            apc_add("languages", $languages);
        }
        return $languages;
    }
    
    public function getLanguageByCode($code)
    {
        $request = "{$this->siteApi}v0/languages/getByCode/$code";
        return $this->client->call("Language", $request);
    }
}
