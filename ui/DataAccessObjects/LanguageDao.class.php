<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/lib/CacheHelper.class.php";
require_once __DIR__."/../../Common/Enums/TimeToLiveEnum.class.php";
require_once __DIR__."/BaseDao.php";

class LanguageDao extends BaseDao
{
    
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getLanguage($id)
    {
        $request = "{$this->siteApi}v0/languages/$id";
        return $this->client->call("\SolasMatch\Common\Protobufs\Models\Language", $request);
    }
    
    public function getLanguages()
    {
        $languages = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::LANGUAGES,
            Common\Enums\TimeToLiveEnum::MONTH,
            function ($args) {
                $request = "{$args[1]}v0/languages";
                return $args[0]->call(array("\SolasMatch\Common\Protobufs\Models\Language"), $request);
            },
            array($this->client, $this->siteApi)
        );
        return $languages;
    }

    public function getActiveLanguages()
    {
        $languages = null;
        $request = "{$this->siteApi}v0/languages/getActiveLanguages";
        $languages = $this->client->call(array('\SolasMatch\Common\Protobufs\Models\Language'), $request);
        return $languages;
    }
    
    public function getActiveSourceLanguages()
    {
        $languages = null;
        $request = "{$this->siteApi}v0/languages/getActiveSourceLanguages";
        $languages = $this->client->call(array('\SolasMatch\Common\Protobufs\Models\Language'), $request);
        return $languages;
    }
     
    public function getActiveTargetLanguages()
    {
        $languages = null;
        $request = "{$this->siteApi}v0/languages/getActiveTargetLanguages";
        $languages = $this->client->call(array('\SolasMatch\Common\Protobufs\Models\Language'), $request);
        return $languages;
    }
    
    public function getLanguageByCode($code)
    {
        $request = "{$this->siteApi}v0/languages/getByCode/$code";
        return $this->client->call("\SolasMatch\Common\Protobufs\Models\Language", $request);
    }
}
