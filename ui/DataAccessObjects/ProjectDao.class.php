<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;
use \SolasMatch\UI\RouteHandlers as Route;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__.'/../../api/lib/PDOWrapper.class.php';
require_once __DIR__ . '/../../Common/from_neon_to_trommons_pair.php';


class ProjectDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getProject($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$id";
        if (!is_null($id)) {
            $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\Project", $request);
            if ($tags = $this->getProjectTags($id)) {
                foreach ($tags as $tag) {
                    $ret->appendTag($tag);
                }
            }
        }

        return $ret;
    }

    public function getProjectTasks($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/tasks";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Task"), $request);
        return $ret;
    }

    public function getProjectReviews($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/reviews";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\TaskReview"), $request);
        return $ret;
    }

    public function getProjectGraph($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/buildGraph/$projectId";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\WorkflowGraph", $request);
        return $ret;
    }

    public function getProjectTags($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/tags";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\Tag"), $request);
        return $ret;
    }

    public function createProject($project)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Project",
            $request,
            Common\Enums\HttpMethodEnum::POST,
            $project
        );
        return $ret;
    }

    public function createProjectDirectly($project)
    {
        $sourceLocale = $project->getSourceLocale();
        $args = LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getId()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getTitle()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getDescription()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getImpact()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getDeadline()). ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getOrganisationId()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getReference()). ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getWordCount()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()). ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()). ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getImageUploaded()). ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getImageApproved());
        $result = LibAPI\PDOWrapper::call('projectInsertAndUpdate', $args);
        $project = null;
        if ($result) {
            $project = Common\Lib\ModelFactory::buildModel('Project', $result[0]);
        }
        return $project;
    }

    public function deleteProject($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function updateProject($project)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/{$project->getId()}";
        $ret = $this->client->call(
            "\SolasMatch\Common\Protobufs\Models\Project",
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            $project
        );
        return $ret;
    }

    public function updateProjectDirectly($project)
    {
        $sourceLocale = $project->getSourceLocale();
        $args = LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getTitle()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getDescription()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getImpact()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getDeadline()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getOrganisationId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getReference()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getWordCount()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getCreatedTime()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($sourceLocale->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getImageUploaded()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getImageApproved());
        LibAPI\PDOWrapper::call('projectInsertAndUpdate', $args);
    }

    public function add_to_project_word_count($project_id, $word_count)
    {
        LibAPI\PDOWrapper::call('add_to_project_word_count', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($word_count));
    }

    public function delete_from_project_word_count($project_id, $word_count)
    {
        LibAPI\PDOWrapper::call('delete_from_project_word_count', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($word_count));
    }

    public function saveProjectFile($project, $userId, $filename, $fileData)
    {
        $filename = urlencode($filename);
        $request = "{$this->siteApi}v0/io/upload/project/{$project->getId()}/file/{$filename}/{$userId}";
        $response = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            null,
            null,
            $fileData
        );
        return $response;
    }

    public function saveProjectImageFile($project, $userId, $filename, $fileData)
    {
        $filename = urlencode($filename);
        $request = "{$this->siteApi}v0/io/upload/project/{$project->getId()}/image/{$filename}/{$userId}";
        $response = $this->client->call(
            null,
            $request,
            Common\Enums\HttpMethodEnum::PUT,
            null,
            null,
            $fileData
        );
        return $response;
    }

    public function calculateProjectDeadlines($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$projectId/calculateDeadlines";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::POST);
        return $ret;
    }

    public function archiveProject($projectId, $userId)
    {
        $request = "{$this->siteApi}v0/projects/archiveProject/$projectId/user/$userId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function getArchivedProject($id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/archivedProjects/$id";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\ArchivedProject"), $request);

        if (!is_null($id) && is_array($ret)) {
            $ret = $ret[0];
        }
        return $ret;
    }

    public function getArchivedProjects()
    {
        $ret = null;
        $request = "{$this->siteApi}v0/archivedProjects";
        $ret = $this->client->call(array("\SolasMatch\Common\Protobufs\Models\ArchivedProject"), $request);
        return $ret;
    }

    public function getProjectFile($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/file";
        $response = $this->client->call(null, $request);
        return $response;
    }

    public function getProjectFileInfo($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/info";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\ProjectFile", $request);
        return $ret;
    }

    public function deleteProjectTags($project_id)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/deleteTags";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::DELETE);
        return $ret;
    }

    public function updateProjectWordCount($project_id, $newWordCount)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/updateWordCount/$newWordCount";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function setProjectImageStatus($project_id, $imageStatus)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/projects/$project_id/setImageApprovalStatus/$imageStatus";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        return $ret;
    }

    public function downloadProjectFile($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/io/download/project/$projectId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::GET);

        switch ($this->client->getResponseCode()) {
            default:
                return $ret;
            case Common\Enums\HttpStatusEnum::NOT_FOUND:
                throw new Common\Exceptions\SolasMatchException("No file!");
        }
    }

    public function downloadProjectImageFile($projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/io/download/projectImage/$projectId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::GET);

        switch ($this->client->getResponseCode()) {
            default:
                return $ret;
            case Common\Enums\HttpStatusEnum::NOT_FOUND:
                throw new Common\Exceptions\SolasMatchException("No file!");
        }
    }

    public function discourse_parameterize($project)
    {
        $a = $project->getTitle();
        $project_id = $project->getId();
//dev server        if ($project_id > 9277) $a .= " $project_id"; //(**)[dev server ID] Backwards compatible
        if ($project_id > 26399) $a .= " $project_id"; //(**)[KP ID] Backwards compatible

        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');

        $a = str_replace(array('\r\n', '\n', '\r', '\t'), '-', $a);

        $a = str_replace("'", '', $a);

        $a = mb_ereg_replace('[\x{0100}-\x{0105}]', 'a', $a);
        $a = mb_ereg_replace('[\x{0106}-\x{010D}]', 'c', $a);
        $a = mb_ereg_replace('[\x{010E}-\x{0111}]', 'd', $a);
        $a = mb_ereg_replace('[\x{0112}-\x{011B}]', 'e', $a);
        $a = mb_ereg_replace('[\x{011C}-\x{0123}]', 'g', $a);
        $a = mb_ereg_replace('[\x{0124}-\x{0127}]', 'h', $a);
        $a = mb_ereg_replace('[\x{0128}-\x{0131}]', 'i', $a);
        $a = mb_ereg_replace('[\x{0132}-\x{0133}]', 'ij', $a);
        $a = mb_ereg_replace('[\x{0134}-\x{0135}]', 'j', $a);
        $a = mb_ereg_replace('[\x{0136}-\x{0138}]', 'k', $a);
        $a = mb_ereg_replace('[\x{0139}-\x{0142}]', 'l', $a);
        $a = mb_ereg_replace('[\x{0143}-\x{014B}]', 'n', $a);
        $a = mb_ereg_replace('[\x{014C}-\x{0151}]', 'o', $a);
        $a = mb_ereg_replace('[\x{0152}-\x{0153}]', 'oe', $a);
        $a = mb_ereg_replace('[\x{0154}-\x{0159}]', 'r', $a);
        $a = mb_ereg_replace('[\x{015A}-\x{0161}]', 's', $a);
        $a = mb_ereg_replace('[\x{0162}-\x{0167}]', 't', $a);
        $a = mb_ereg_replace('[\x{0168}-\x{0173}]', 'u', $a);
        $a = mb_ereg_replace('[\x{0174}-\x{0175}]', 'w', $a);
        $a = mb_ereg_replace('[\x{0176}-\x{0178}]', 'y', $a);
        $a = mb_ereg_replace('[\x{0179}-\x{017E}]', 'z', $a);

        $a = mb_ereg_replace('[^\x{0030}-\x{00FF}]', '-', $a);
        $a = mb_ereg_replace('[\x{007B}-\x{00BF}]', '-', $a);

        $a = iconv('UTF-8', 'ISO-8859-1', $a);

$replace = array(
':' => '-',
';' => '-',
'<' => '-',
'=' => '-',
'>' => '-',
'?' => '-',
'@' => '-',
'[' => '-',
'\\' => '-',
']' => '-',
'^' => '-',
'_' => '-',
'`' => '-',
"\xC0" => 'a',
"\xC1" => 'a',
"\xC2" => 'a',
"\xC3" => 'a',
"\xC4" => 'a',
"\xC5" => 'a',
"\xC6" => 'ae',
"\xC7" => 'c',
"\xC8" => 'e',
"\xC9" => 'e',
"\xCA" => 'e',
"\xCB" => 'e',
"\xCC" => 'i',
"\xCD" => 'i',
"\xCE" => 'i',
"\xCF" => 'i',
"\xD0" => 'd',
"\xD1" => 'n',
"\xD2" => 'o',
"\xD3" => 'o',
"\xD4" => 'o',
"\xD5" => 'o',
"\xD6" => 'o',
"\xD7" => 'x',
"\xD8" => 'o',
"\xD9" => 'u',
"\xDA" => 'u',
"\xDB" => 'u',
"\xDC" => 'u',
"\xDD" => 'y',
"\xDE" => 'th',
"\xDF" => 'ss',
"\xE0" => 'a',
"\xE1" => 'a',
"\xE2" => 'a',
"\xE3" => 'a',
"\xE4" => 'a',
"\xE5" => 'a',
"\xE6" => 'ae',
"\xE7" => 'c',
"\xE8" => 'e',
"\xE9" => 'e',
"\xEA" => 'e',
"\xEB" => 'e',
"\xEC" => 'i',
"\xED" => 'i',
"\xEE" => 'i',
"\xEF" => 'i',
"\xF0" => 'd',
"\xF1" => 'n',
"\xF2" => 'o',
"\xF3" => 'o',
"\xF4" => 'o',
"\xF5" => 'o',
"\xF6" => 'o',
"\xF7" => '-',
"\xF8" => 'o',
"\xF9" => 'u',
"\xFA" => 'u',
"\xFB" => 'u',
"\xFC" => 'u',
"\xFD" => 'y',
"\xFE" => 'th',
"\xFF" => 'y',
);
        $a = str_replace(array_keys($replace), $replace, $a);

        $a = trim($a, '-');
        $a = preg_replace('/-+/', '-', $a);
        $a = strtolower($a);

        $topic_id = $this->get_discourse_id($project_id);
        if (!empty($topic_id)) $a .= "/$topic_id";

        return $a;
    }

    public function set_discourse_id($project_id, $topic_id)
    {
        LibAPI\PDOWrapper::call('set_discourse_id', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($topic_id));
    }

    public function get_discourse_id($project_id)
    {
        $topic_id = 0;
        $result = LibAPI\PDOWrapper::call('get_discourse_id', LibAPI\PDOWrapper::cleanse($project_id));
        if (!empty($result)) {
            $topic_id = $result[0]['topic_id'];
        }
        return $topic_id;
    }

    public function getOrgProjects($org_id, $months)
    {
        $result = LibAPI\PDOWrapper::call('getOrgProjects', LibAPI\PDOWrapper::cleanse($org_id) . ',' . LibAPI\PDOWrapper::cleanse($months));
        return $result;
    }

    public function get_project_id_for_latest_org_image($org_id)
    {
        $result = LibAPI\PDOWrapper::call('get_project_id_for_latest_org_image', LibAPI\PDOWrapper::cleanse($org_id));
        if (!empty($result)) return $result[0]['id'];
        return 0;
    }

    public function set_uploaded_approved($project_id)
    {
        LibAPI\PDOWrapper::call('set_uploaded_approved', LibAPI\PDOWrapper::cleanse($project_id));
    }

    public function generate_language_selection($create_memsource = 0)
    {
        global $from_neon_to_trommons_pair, $language_options_changes;

        $language_options = [];
        foreach ($from_neon_to_trommons_pair as $language => $trommons_pair) {
            $language_options[$trommons_pair[0] . '-' . $trommons_pair[1]] = $language;
        }

        foreach ($language_options_changes as $key => $language) {
            $language_options[$key] = $language;
        }

        asort($language_options);
        return $language_options;
    }

    public function convert_selection_to_language_country($selection)
    {
        $language_code = str_replace('#', '', $selection); // Alternative language name uses # in code
        $trommons_language_code = substr($language_code, 0, strpos($language_code, '-'));
        $trommons_country_code  = substr($language_code, strpos($language_code, '-') + 1);
        return [$trommons_language_code, $trommons_country_code];
    }

    public function convert_memsource_to_language_country($memsource)
    {
$memsource_change_language_to_kp = [
'as' => 'asm',
'ilt' => 'ilo',
'kz' => 'ky',
'rn' => 'run',
'tir' => 'ti',
'mfi' => 'mf0',
'nb' => 'no',

'ku' => 'kmr',
];

$memsource_change_country_to_kp = [
'001' => '--',
'mod' => '--',
'419' => '49',
'latn' => '90',
'latn_az' => '90',
'latn_bg' => '90',
'latn_ba' => '90',
'latn_gr' => '90',
'latn_ir' => '90',
'latn_am' => '90',
'latn_in' => '90',
'latn_ru' => '90',
'latn_rs' => '90',
'latn_ua' => '90',
'latn_uz' => '90',
'latn_ng' => '90',

'cyrl_rs' => '91',
'cyrl' => '91',
'cyrl_az' => '91',
'cyrl_ba' => '91',
'cyrl_tj' => '91',
'cyrl_uz' => '91',

'arab' => 'pk', // Because sd_arab is the only active 'arab'

'cn' => '92',
'hans' => '92',
'hans_cn' => '92',
'tw' => '93',
'hant' => '93',
'hant_tw' => '93',

'arab_iq' => '94',
];
        $trommons_language_code = $memsource;
        $trommons_country_code  = '';
        $pos = strpos($memsource, '_');
        if ($pos != false) {
            $trommons_language_code = substr($memsource, 0, $pos);
            $trommons_country_code  = substr($memsource, $pos + 1);
            if (!empty($memsource_change_country_to_kp[$trommons_country_code])) $trommons_country_code = $memsource_change_country_to_kp[$trommons_country_code];
            $trommons_country_code = strtoupper($trommons_country_code);
        } else {
            $trommons_country_code = '--';
        }
        if (!empty($memsource_change_language_to_kp[$trommons_language_code])) $trommons_language_code = $memsource_change_language_to_kp[$trommons_language_code];

        if ($trommons_language_code === 'sw' && $trommons_country_code === 'CD') $trommons_language_code = 'swc';
        if ($trommons_country_code === 'LATN_ME') { $trommons_language_code = 'cnr'; $trommons_country_code = '90';}
        if ($trommons_country_code === 'CYRL_ME') { $trommons_language_code = 'cnr'; $trommons_country_code = '91';}

        return [$trommons_language_code, $trommons_country_code];
    }

    public function convert_language_country_to_memsource($kp_language, $kp_country)
    {
        $kp_country = strtolower($kp_country);
        $kp_change_language_to_memsource = [
            'asm' => 'as',
            'run' => 'rn',
            'swc' => 'sw',
            'mf0' => 'mfi',
            'no' => 'nb',
        ];
        if (!empty($kp_change_language_to_memsource[$kp_language])) $kp_language = $kp_change_language_to_memsource[$kp_language];
        $kp_change_country_to_memsource = [
            '49' => '419',  // Latin America
            '90' => 'latn', // Latin Script
            '91' => 'cyrl', // Cyrillic Script
            '92' => 'cn',   // Simplified Script
            '93' => 'tw',   // Traditional Script
            '94' => 'arab_iq', // Bahdini Variant
        ];
        if (!empty($kp_change_country_to_memsource[$kp_country])) $kp_country = $kp_change_country_to_memsource[$kp_country];

        $memsource_valid = ['aa','af_za','sq','am_et','ar_sa','pga','apc','hy_am','as','ay','az_cyrl','az_latn','eu','be_by','bem','bn_bd','bn_in','bik','bi','bs_cyrl','bs_latn','bg','bwr','my_mm','ca','ceb','ckb','ku_arab_iq','shu_td','shu_latn_ng','cbk','ce_ru','ny','zh_cn','zh_tw','ctg_bd','ckl','hr','cs','da','prs_af','dv_mv','din_ss','nl','dyu','tw','bin_ng','en_gb','en_us','et','fa_ir','fj','fil_ph','fi','fr_ca','fr_cd','fr_fr','ff','gl','mfi_ng','lg','ka_ge','de','glw','el','gn','gu_in','guz_ke','ht','ha','he','hi_in','hmn','hu','is','ig_ng','ilo_ph','hil','id_id','ga','it','ja','quc','kea_cv','kln_ke','kam_ke','hig','kn_in','kr','pam','kar','kk_kz','km','ki','rw','rn_bi','kg','kok','ko_kr','kri','ky_kg','hia','lo','lv','ln','ln_cd','lt','lua','luo_ke','mk_mk','mdh','mg_mg','ms_my','ml_in','mt_mt','mi_nz','mrw','mr_in','mrt','lol','mn_mn','nnb','ne_np','ngc','kmr','nd','nso','nb','nn','nus','om_et','pag','ps','ps_af','pis','pl','pt_br','pt_mz','pt_pt','pa_in','qu','rhg','rhg_latn','ro','ru_ru','sm','sg_cf','seh','sr_cyrl_me','sr_cyrl_rs','sr_latn_me','sr_latn_rs','shr','sn','sd','sd_arab','si_lk','sk','sl','so_et','so_so','nr','st','es_co','es_419','es_mx','es_es','sw','sw_cd','sv','syl','tl','tg_cyrl_tj','ta_in','ta_lk','tt','tsg_ph','te','th_th','bo','ti','tpi','to','ts','tn','tr','tk','uk_ua','ur_pk','uz_cyrl_uz','vi_vn','war','cy_gb','wo_sn','xh','yo','zu_za','tig','lu',];

        if ($kp_country != '--') $memsource_pair = $kp_language . '_' . $kp_country;
        else                     $memsource_pair = $kp_language;

        if ($memsource_pair === 'sd_pk') $memsource_pair = 'sd_arab';
        if ($memsource_pair === 'shu_latn') $memsource_pair = 'shu_latn_ng';
        if ($memsource_pair === 'uz_cyrl') $memsource_pair = 'uz_cyrl_uz';
        if ($memsource_pair === 'tg_cyrl') $memsource_pair = 'tg_cyrl_tj';
        if ($memsource_pair === 'sr_latn') $memsource_pair = 'sr_latn_rs';
        if ($memsource_pair === 'sr_cyrl') $memsource_pair = 'sr_cyrl_rs';
        if ($memsource_pair === 'cnr_latn') $memsource_pair = 'sr_latn_me';
        if ($memsource_pair === 'cnr_cyrl') $memsource_pair = 'sr_cyrl_me';
        if ($memsource_pair === 'mfi_cm') $memsource_pair = 'mfi_ng';//(**)
        if ($memsource_pair === 'kmr_arab_iq') $memsource_pair = 'ku_arab_iq';

        if (in_array($memsource_pair, $memsource_valid)) return $memsource_pair;
        if (in_array($kp_language,    $memsource_valid)) return $kp_language;

        error_log("Failed: convert_language_country_to_memsource($kp_language, $kp_country)");
        return 0;
    }

    public function copy_project_file($project_to_copy_id, $project_id, $user_id_owner)
    {
        $result = LibAPI\PDOWrapper::call('getProjectFile', "$project_to_copy_id, null, null, null, null");
        $filename = $result[0]['filename'];
        $mime     = $result[0]['mime'];
        $args = LibAPI\PDOWrapper::cleanseNull($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseNull($user_id_owner) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($filename) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($filename) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($mime);
        LibAPI\PDOWrapper::call('addProjectFile', $args);

        $destination = Common\Lib\Settings::get("files.upload_path") . "proj-$project_id/";
        mkdir($destination, 0755);
        file_put_contents($destination . $filename, "files/proj-$project_to_copy_id/$filename"); // Point to existing project file

        return [$filename, $mime];
    }

    public function addProjectTask(
        $project_to_copy_id,
        $filename,
        $mime,
        $project,
        $language_code_target,
        $country_code_target,
        $task_type,
        $task_id_prereq,
        $user_id_owner,
        $taskDao)
    {
        if ($task_type == Common\Enums\TaskTypeEnum::TRANSLATION) {
            $published = 0;
            $deadline = gmdate('Y-m-d H:i:s', strtotime('10 days'));
        } else {
            $published = 1;
            $deadline = gmdate('Y-m-d H:i:s', strtotime('24 days'));
        }

        $args = 'null ,' .
            LibAPI\PDOWrapper::cleanseNull($project->getId()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getTitle()) . ',' .
            LibAPI\PDOWrapper::cleanseNull($project->getWordCount()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getSourceLocale()->getLanguageCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($language_code_target) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr('') . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($project->getSourceLocale()->getCountryCode()) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($country_code_target) . ',' .
            LibAPI\PDOWrapper::cleanseNullOrWrapStr($deadline) . ',' .
            LibAPI\PDOWrapper::cleanseNull($task_type) . ',' .
            LibAPI\PDOWrapper::cleanseNull(Common\Enums\TaskStatusEnum::PENDING_CLAIM) . ',' .
            LibAPI\PDOWrapper::cleanseNull($published);
        $result = LibAPI\PDOWrapper::call('taskInsertAndUpdate', $args);
        if (!empty($result)) {
            $task_id = $result[0]['id'];

            if ($task_type == Common\Enums\TaskTypeEnum::PROOFREADING) {
                $taskDao->updateRequiredTaskQualificationLevel($task_id, 3); // Reviser Needs to be Senior
            } else {
                $taskDao->updateRequiredTaskQualificationLevel($task_id, 1);
            }

            $args = LibAPI\PDOWrapper::cleanseNull($task_id) . ',' .
                LibAPI\PDOWrapper::cleanseWrapStr($filename) . ',' .
                LibAPI\PDOWrapper::cleanseWrapStr($mime) . ',' .
                LibAPI\PDOWrapper::cleanseNull($user_id_owner) . ',' .
                'NULL';
            LibAPI\PDOWrapper::call('recordFileUpload', $args);

            $project_id = $project->getId();
            $uploadFolder = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-0";
            mkdir($uploadFolder, 0755, true);

            file_put_contents($uploadFolder . "/$filename", "files/proj-$project_to_copy_id/$filename"); // Point to existing project file

            if ($task_id_prereq) LibAPI\PDOWrapper::call('addTaskPreReq', LibAPI\PDOWrapper::cleanseNull($task_id) . ',' . LibAPI\PDOWrapper::cleanseNull($task_id_prereq));

            return $task_id;
        } else {
            return 0;
        }
    }

    public function insert_testing_center_project($user_id, $project_id, $translation_task_id, $proofreading_task_id, $project_to_copy_id, $language_code_source, $language_code_target)
    {
        LibAPI\PDOWrapper::call('insert_testing_center_project',
            LibAPI\PDOWrapper::cleanse($user_id) . ',' .
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($translation_task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($proofreading_task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($project_to_copy_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_target)
        );
    }

    public function get_testing_center_projects($user_id, &$testing_center_projects_by_code)
    {
        $results = LibAPI\PDOWrapper::call('get_testing_center_projects', LibAPI\PDOWrapper::cleanse($user_id));
        $testing_center_projects = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                $testing_center_projects[$result['project_to_copy_id']] = $result;
                $testing_center_projects_by_code[$result['language_code_source'] . '-' . $result['language_code_target']] = $result;
            }
        }
        return $testing_center_projects;
    }

    public function save_task_file($user_id, $project_id, $task_id, $filename, $file)
    {
        $userDao = new UserDao();
        $mime = $userDao->detectMimeType($file, $filename);

        $args = LibAPI\PDOWrapper::cleanseNull($task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($filename) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($mime) . ',' .
            LibAPI\PDOWrapper::cleanseNull($user_id) . ',' .
            'NULL';
        $result = LibAPI\PDOWrapper::call('recordFileUpload', $args);
        $version = $result[0]['version'];

        $uploadFolder = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-$version";
        if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0755, true);

        $min_id = $this->get_first_project_task($project_id);
        if ($min_id) {
            $previous_path = "files/proj-$project_id/task-$min_id/v-0/$filename";
            $previous_file = '';
            if (file_exists(Common\Lib\Settings::get('files.upload_path') . $previous_path)) {
                $previous_file = file_get_contents(Common\Lib\Settings::get('files.upload_path') . $previous_path);
            }
            if ($previous_file && $previous_file === $file) {                 // If a previously stored file is identical
                file_put_contents("$uploadFolder/$filename", $previous_path); // Point to files folder for previous file
                return;
            }
        }

        $filesFolder = "files/proj-$project_id/task-$task_id/v-$version";
        $filesFolderFull = Common\Lib\Settings::get('files.upload_path') . $filesFolder;
        if (!is_dir($filesFolderFull)) mkdir($filesFolderFull, 0755, true);

        file_put_contents($filesFolderFull . "/$filename", $file); // Save the file in files folder
        file_put_contents("$uploadFolder/$filename", "$filesFolder/$filename"); // Point to files folder
    }

    public function set_memsource_client($org_id, $memsource_client_id, $memsource_client_uid)
    {
        LibAPI\PDOWrapper::call('set_memsource_client', LibAPI\PDOWrapper::cleanse($org_id) . ',' . LibAPI\PDOWrapper::cleanse($memsource_client_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($memsource_client_uid));
    }

    public function get_memsource_client($org_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_client', LibAPI\PDOWrapper::cleanse($org_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function get_memsource_client_by_memsource_id($memsource_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_client_by_memsource_id', LibAPI\PDOWrapper::cleanse($memsource_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function set_memsource_project($project_id, $memsource_project_id, $memsource_project_uid, $created_by_id, $owner_id, $workflowLevels)
    {
        LibAPI\PDOWrapper::call('set_memsource_project',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($memsource_project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($memsource_project_uid) . ',' .
            LibAPI\PDOWrapper::cleanse($created_by_id) . ',' .
            LibAPI\PDOWrapper::cleanse($owner_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[0]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[1]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[2]));
    }

    public function update_memsource_project($project_id, $workflowLevels)
    {
        LibAPI\PDOWrapper::call('update_memsource_project',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[0]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[1]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[2]));
    }

    public function record_memsource_project_languages($project_id, $source_language_pair, $target_languages)
    {
        LibAPI\PDOWrapper::call('record_memsource_project_languages',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($source_language_pair) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($target_languages));
    }

    public function get_memsource_project_languages($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_project_languages', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return 0;
        return $result[0];
    }

    public function set_memsource_self_service_project($memsource_project_id)
    {
        LibAPI\PDOWrapper::call('set_memsource_self_service_project', LibAPI\PDOWrapper::cleanse($memsource_project_id));
    }

    public function get_memsource_self_service_project($memsource_project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_self_service_project', LibAPI\PDOWrapper::cleanse($memsource_project_id));

        if (empty($result)) return 0;

        return 1;
    }

    public function get_memsource_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_project', LibAPI\PDOWrapper::cleanse($project_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function get_memsource_project_by_memsource_id($memsource_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_project_by_memsource_id', LibAPI\PDOWrapper::cleanse($memsource_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function get_memsource_project_by_memsource_uid($memsource_uid)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_project_by_memsource_uid', LibAPI\PDOWrapper::cleanseWrapStr($memsource_uid));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function set_memsource_task($task_id, $memsource_task_id, $memsource_task_uid, $task, $internalId, $workflowLevel, $beginIndex, $endIndex, $prerequisite)
    {
        $result = LibAPI\PDOWrapper::call('set_memsource_task',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($memsource_task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($memsource_task_uid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($internalId) . ',' .
            LibAPI\PDOWrapper::cleanse($workflowLevel) . ',' .
            LibAPI\PDOWrapper::cleanse($beginIndex) . ',' .
            LibAPI\PDOWrapper::cleanse($endIndex) . ',' .
            LibAPI\PDOWrapper::cleanse($prerequisite));
        return $result[0]['result'];
    }

    public function update_memsource_task($task_id, $memsource_task_id, $task, $internalId, $beginIndex, $endIndex)
    {
        LibAPI\PDOWrapper::call('update_memsource_task',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($memsource_task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($internalId) . ',' .
            LibAPI\PDOWrapper::cleanse($beginIndex) . ',' .
            LibAPI\PDOWrapper::cleanse($endIndex));
    }

    public function get_memsource_task($task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_task', LibAPI\PDOWrapper::cleanse($task_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function get_memsource_task_by_memsource_id($memsource_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_task_by_memsource_id', LibAPI\PDOWrapper::cleanse($memsource_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function get_memsource_task_by_memsource_uid($memsource_uid)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_task_by_memsource_uid', LibAPI\PDOWrapper::cleanseWrapStr($memsource_uid));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function is_job_uid_already_processed($memsource_task_uid)
    {
        $result = LibAPI\PDOWrapper::call('is_job_uid_already_processed', LibAPI\PDOWrapper::cleanseWrapStr($memsource_task_uid));
        return $result[0]['result'];
    }

    public function get_memsource_tasks_for_project_language_type($project_id, $task, $type_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_tasks_for_project_language_type',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function get_memsource_tasks_for_project_internal_id_type($project_id, $internalId, $type_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_tasks_for_project_internal_id_type',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($internalId) . ',' .
            LibAPI\PDOWrapper::cleanse($type_id));

        if (empty($result)) return 0;

        return $result[0];
    }

    public function queue_copy_task_original_file($project_id, $task_id, $memsource_task_uid, $filename)
    {
        LibAPI\PDOWrapper::call('queue_copy_task_original_file',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($memsource_task_uid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($filename));
    }

    public function get_queue_copy_task_original_files()
    {
        return LibAPI\PDOWrapper::call('get_queue_copy_task_original_files', '');
    }

    public static function dequeue_copy_task_original_file($task_id)
    {
        LibAPI\PDOWrapper::call('dequeue_copy_task_original_file', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function get_user_id_from_memsource_user($memsource_user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_user_id_from_memsource_user', LibAPI\PDOWrapper::cleanse($memsource_user_id));

        if (empty($result)) return 0;

        return $result[0]['user_id'];
    }

    public function get_first_project_task($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_first_project_task', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result[0]['min_id'])) return 0;

        return $result[0]['min_id'];
    }

    public function update_project_due_date($project_id, $deadline)
    {
        LibAPI\PDOWrapper::call('update_project_due_date', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($deadline));
    }

    public function update_project_description($project_id, $description)
    {
        LibAPI\PDOWrapper::call('update_project_description', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($description));
    }

    public function update_project_organisation($project_id, $org_id)
    {
        LibAPI\PDOWrapper::call('update_project_organisation', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($org_id));
    }

    public function update_task_due_date($task_id, $deadline)
    {
        LibAPI\PDOWrapper::call('update_task_due_date', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($deadline));
    }

    public function get_user($user_id)
    {
        return LibAPI\PDOWrapper::call('get_user', LibAPI\PDOWrapper::cleanse($user_id));
    }

    public function getUserClaimedTask($task_id)
    {
        $result = LibAPI\PDOWrapper::call('getUserClaimedTask', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return 0;

        return $result[0]['id'];
    }

    public function sync_split_jobs($memsource_project)
    {
        $userDao = new UserDao();
        $taskDao = new TaskDao();
        $project_route_handler = new Route\ProjectRouteHandler();
        $project_id            = $memsource_project['project_id'];
        $memsource_project_uid = $memsource_project['memsource_project_uid'];

        $jobs = $userDao->memsource_list_jobs($memsource_project_uid, $project_id);
        if (empty($jobs)) return;

        $memsource_project = $this->get_memsource_project($project_id); // Workflow could have been updated

        foreach ($jobs as $uid => $job) {
            $memsource_task = $this->get_memsource_task_by_memsource_uid($uid);
            $full_job = $userDao->memsource_get_job($memsource_project_uid, $uid);
            if ($full_job) {
                if (empty($memsource_task)) {
                    if ($this->create_task($memsource_project, $full_job)) {
                        error_log("Created task for job $uid {$full_job['innerId']} in project $project_id");
                    }
                } else {
                    $this->update_task_from_job($memsource_project, $full_job, $memsource_task);
                }
            } else error_log("Could not find job $uid in project $project_id (or is top level)");
        }

        $project_tasks = $this->get_tasks_for_project($project_id);
        foreach ($project_tasks as $uid => $project_task) {
                if (empty($jobs[$uid])) {
                    $this->adjust_for_deleted_task($memsource_project, $project_task);
                    $this->delete_task_directly($project_task['id']);
                    error_log("Deleted task {$project_task['id']} for job $uid {$project_task['internalId']} in project $project_id");
                } elseif (($prerequisite = $project_task['prerequisite']) && $project_task['task-status_id'] == Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES) {
                    $prerequisite_uid = 0;
                    foreach ($project_tasks as $u => $pt) {
                        if ($pt['id'] == $prerequisite) $prerequisite_uid = $u;
                    }
                    if (empty($jobs[$prerequisite_uid])) { // Has been (or will be) deleted
                        $taskDao->setTaskStatus($project_task['id'], Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                    }
                }
        }
    }

    private function create_task($memsource_project, $job)
    {
        $taskDao = new TaskDao();
        $task = new Common\Protobufs\Models\Task();

        if (empty($job['filename'])) {
            error_log("No filename in new jobPart {$job['uid']}");
            return 0;
        }
//error_log('Sync create_task job: ' . print_r($job, true));

        $project_id = $memsource_project['project_id'];
        $task->setProjectId($project_id);
        $task->setTitle(mb_substr("{$job['innerId']} {$job['filename']}", 0, 128));

        $project = $this->getProject($project_id);
        $projectSourceLocale = $project->getSourceLocale();
        $taskSourceLocale = new Common\Protobufs\Models\Locale();
        $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
        $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
        $task->setSourceLocale($taskSourceLocale);
        $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);

        $taskTargetLocale = new Common\Protobufs\Models\Locale();
        list($target_language, $target_country) = $this->convert_memsource_to_language_country($job['targetLang']);
        $taskTargetLocale->setLanguageCode($target_language);
        $taskTargetLocale->setCountryCode($target_country);
        $task->setTargetLocale($taskTargetLocale);

        if (empty($job['workflowLevel'])) {
            error_log("Sync Can't find workflowLevel in new job {$job['uid']} for: {$job['filename']}, assuming Translation");
            $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
        } elseif ($job['workflowLevel'] > 3) {
            error_log("Sync Don't support workflowLevel > 3: {$job['workflowLevel']} in new job {$job['uid']} for: {$job['fileName']}");
            return 0;
        } else {
            $taskType = [$memsource_project['workflow_level_1'], $memsource_project['workflow_level_2'], $memsource_project['workflow_level_3']][$job['workflowLevel'] - 1];
            error_log("Sync taskType: $taskType, workflowLevel: {$job['workflowLevel']}");
            if     ($taskType == 'Translation') $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
            elseif ($taskType == 'Revision')    $taskType = Common\Enums\TaskTypeEnum::PROOFREADING;
            elseif ($taskType == '' && $job['workflowLevel'] == 1) $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
            else {
                error_log("Sync Can't find expected taskType ($taskType) in new job {$job['uid']} for: {$job['filename']}");
                return 0;
            }
        }
        $task->setTaskType($taskType);

        if (!empty($job['wordsCount'])) {
            $task->setWordCount($job['wordsCount']);
            if ( $taskType == Common\Enums\TaskTypeEnum::TRANSLATION ||
                ($taskType == Common\Enums\TaskTypeEnum::PROOFREADING &&
                 ($memsource_project['workflow_level_1'] === 'Revision' && $memsource_project['workflow_level_2'] !== 'Translation' && $memsource_project['workflow_level_3'] !== 'Translation'))
               ) {
                $project_languages = $this->get_memsource_project_languages($project_id);
error_log("Sync Translation {$target_language}-{$target_country} vs first get_memsource_project_languages($project_id): {$project_languages[0]} + {$job['wordsCount']}");//(**)
                if (!empty($project_languages['kp_target_language_pairs'])) {
                    $project_languages = explode(',', $project_languages['kp_target_language_pairs']);
                    if ("{$target_language}-{$target_country}" === $project_languages[0]) {
error_log("Sync Updating project_wordcount with {$job['wordsCount']}");//(**)
                        $this->add_to_project_word_count($project_id, $job['wordsCount']);
                    }
                }
            }
        } else {
            $task->setWordCount(1);
        }

        $prerequisite = 0;
        $innerId       = empty($job['innerId']) ? 0 : $job['innerId'];
        $workflowLevel = empty($job['workflowLevel']) ? 0 : $job['workflowLevel'];
        if ($taskType == Common\Enums\TaskTypeEnum::PROOFREADING && strpos($innerId, '.') === false) { // Revision & top level
            $project_tasks = $this->get_tasks_for_project($project_id); // Translation task should already have been created
            foreach ($project_tasks as $project_task) {
                if ($innerId == $project_task['internalId']) {
                    if ($workflowLevel > $project_task['workflowLevel']) { // Dependent on
                        $prerequisite = $project_task['task_id'];
                        $task->setTaskStatus(Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                    }
                }
            }
        }

        if (!empty($job['dateDue'])) $task->setDeadline(substr($job['dateDue'], 0, 10) . ' ' . substr($job['dateDue'], 11, 8));
        else                         $task->setDeadline($project->getDeadline());

        $task->setPublished(1);

        $task_id = $taskDao->createTaskDirectly($task);
        if (!$task_id) {
            error_log("Failed to add task for new job {$job['uid']} for: {$job['filename']}");
            return 0;
        }
        error_log("Added Task: $task_id for new job {$job['uid']} for: {$job['filename']}");

        $success = $this->set_memsource_task($task_id, 0, $job['uid'], '',
            empty($job['innerId'])       ? 0 : $job['innerId'],
            empty($job['workflowLevel']) ? 0 : $job['workflowLevel'],
            empty($job['beginIndex'])    ? 0 : $job['beginIndex'],
            empty($job['endIndex'])      ? 0 : $job['endIndex'],
            $prerequisite);
error_log("set_memsource_task($task_id, 0, {$job['uid']}...), success: $success");//(**)
        if (!$success) { // May be because of button double click
            $this->delete_task_directly($task_id);
            error_log("Sync delete_task_directly($task_id) because of set_memsource_task fail");
            return 0;
        }

        $project_id = $project->getId();

        $project_restrictions = $taskDao->get_project_restrictions($project_id);
        if ($project_restrictions && (
                ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION  && $project_restrictions['restrict_translate_tasks'])
                    ||
                ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $project_restrictions['restrict_revise_tasks']))) {
            $taskDao->setRestrictedTask($task_id);
        }

        $uploadFolder = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-0";
        mkdir($uploadFolder, 0755, true);
        $filesFolder = Common\Lib\Settings::get('files.upload_path') . "files/proj-$project_id/task-$task_id/v-0";
        mkdir($filesFolder, 0755, true);

        $filename = $job['filename'];
        file_put_contents("$filesFolder/$filename", ''); // Placeholder
        file_put_contents("$uploadFolder/$filename", "files/proj-$project_id/task-$task_id/v-0/$filename"); // Point to it

        if (mb_strlen($filename) <= 255) $this->queue_copy_task_original_file($project_id, $task_id, $job['uid'], $filename); // cron will copy file from memsource
        return 1;
    }

    private function adjust_for_deleted_task($memsource_project, $project_task)
    {
        // error_log('adjust_for_deleted_task project_task: ' . print_r($project_task, true));
        $taskDao = new TaskDao();
        $project_id = $memsource_project['project_id'];
        $task = $taskDao->getTask($project_task['task_id']);
        $target_language = $task->getTargetLocale()->getLanguageCode();
        $target_country  = $task->getTargetLocale()->getCountryCode();
        $taskType = $project_task['task-type_id'];
        if ( $taskType == Common\Enums\TaskTypeEnum::TRANSLATION ||
            ($taskType == Common\Enums\TaskTypeEnum::PROOFREADING &&
             ($memsource_project['workflow_level_1'] === 'Revision' && $memsource_project['workflow_level_2'] !== 'Translation' && $memsource_project['workflow_level_3'] !== 'Translation'))
           ) {
            $project_languages = $this->get_memsource_project_languages($project_id);
error_log("adjust_for_deleted_task check: {$target_language}-{$target_country} vs first get_memsource_project_languages($project_id): {$project_languages[0]} - {$project_task['word-count']}");//(**)
            if (!empty($project_languages['kp_target_language_pairs'])) {
                $project_languages = explode(',', $project_languages['kp_target_language_pairs']);
                if ("{$target_language}-{$target_country}" === $project_languages[0]) {
error_log("adjust_for_deleted_task updating: {$project_task['word-count']}");//(**)
                    $this->delete_from_project_word_count($project_id, $project_task['word-count']);
                }
            }
        }
    }

    private function update_task_from_job($memsource_project, $job, $memsource_task)
    {
        $taskDao = new TaskDao();
        $task_id = $memsource_task['task_id'];

        $status = $job['status'];
error_log("Sync update_task_from_job() task_id: $task_id, status: $status, job: " . print_r($job, true));//(**)
        $taskDao->set_memsource_status($task_id, $memsource_task['memsource_task_uid'], $status);

        if (!empty($job['dateDue'])) $this->update_task_due_date($task_id, substr($job['dateDue'], 0, 10) . ' ' . substr($job['dateDue'], 11, 8));

        if ($status == 'ACCEPTED') { // In Progress ('ASSIGNED' in Hook)
            if (!empty($job['providers'][0]['id']) && count($job['providers']) == 1) {
                $user_id = $this->get_user_id_from_memsource_user($job['providers'][0]['id']);
                if (!$user_id) {
                    error_log("Can't find user_id for {$job['providers'][0]['id']} in Sync status: ACCEPTED");
                    return;
                }

                if (!$taskDao->taskIsClaimed($task_id)) {
                    $taskDao->claimTask($task_id, $user_id);
                    error_log("Sync ACCEPTED in memsource task_id: $task_id, user_id: $user_id, memsource job: {$job['uid']}, user: {$job['providers'][0]['id']}");
                } else { // Probably being set by admin in Memsource from COMPLETED_BY_LINGUIST back to ASSIGNED
                  if ($taskDao->getTaskStatus($task_id) == Common\Enums\TaskStatusEnum::COMPLETE) {
                    $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::IN_PROGRESS);

                    // See if the current task is the Translation matching a prerequisite for a Revision, if so set Revision back to WAITING_FOR_PREREQUISITES
                    if (strpos($memsource_task['internalId'], '.') === false) { // Not split
                        $dependent_task = $this->get_memsource_tasks_for_project_internal_id_type($memsource_project['project_id'], $memsource_task['internalId'], Common\Enums\TaskTypeEnum::PROOFREADING);
                        if ($dependent_task && $dependent_task['prerequisite'] == $task_id) {
                            if ($dependent_task['task-status_id'] == Common\Enums\TaskStatusEnum::PENDING_CLAIM)
                                $taskDao->setTaskStatus($dependent_task['task_id'], Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                        }
                    }
                    error_log("Sync ACCEPTED task_id: $task_id, memsource: {$job['uid']}, reverting from COMPLETED_BY_LINGUIST");
                  }
                }
            }
        }
        if ($status == 'COMPLETED') { // Complete ('COMPLETED_BY_LINGUIST' in Hook)
            if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 62927); // translators@translatorswithoutborders.org
//(**)dev server                if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 3297);

          if ($taskDao->getTaskStatus($task_id) != Common\Enums\TaskStatusEnum::COMPLETE) {
            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::COMPLETE);
            $taskDao->sendTaskUploadNotifications($task_id, 1);
            $taskDao->set_task_complete_date($task_id);

            if (strpos($memsource_task['internalId'], '.') === false) { // Not split
                $dependent_task = $this->get_memsource_tasks_for_project_internal_id_type($memsource_project['project_id'], $memsource_task['internalId'], Common\Enums\TaskTypeEnum::PROOFREADING);
                if ($dependent_task && $dependent_task['prerequisite'] == $task_id) {
                    if ($dependent_task['task-status_id'] == Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES)
                        $taskDao->setTaskStatus($dependent_task['task_id'], Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                    $user_id = $this->getUserClaimedTask($task_id);
                    if ($user_id) $taskDao->addUserToTaskBlacklist($user_id, $dependent_task['task_id']);
                }
            }
            error_log("Sync COMPLETED task_id: $task_id, memsource: {$job['uid']}");
          }
        }
        if ($status == 'DECLINED' || $status == 'NEW') { // Unclaimed ('DECLINED_BY_LINGUIST' in Hook)
            if ($taskDao->taskIsClaimed($task_id)) {
                $old_status = $taskDao->getTaskStatus($task_id);
                $user_id = $this->getUserClaimedTask($task_id);
                if ($user_id) $taskDao->unclaimTask($task_id, $user_id);
                error_log("Sync DECLINED task_id: $task_id, user_id: $user_id, memsource job: {$job['uid']}");
                if ($old_status == Common\Enums\TaskStatusEnum::COMPLETE) {
                    // See if the current task is the Translation matching a prerequisite for a Revision, if so set Revision back to WAITING_FOR_PREREQUISITES
                    if (strpos($memsource_task['internalId'], '.') === false) { // Not split
                        $dependent_task = $this->get_memsource_tasks_for_project_internal_id_type($memsource_project['project_id'], $memsource_task['internalId'], Common\Enums\TaskTypeEnum::PROOFREADING);
                        if ($dependent_task && $dependent_task['prerequisite'] == $task_id) {
                            if ($dependent_task['task-status_id'] == Common\Enums\TaskStatusEnum::PENDING_CLAIM)
                                $taskDao->setTaskStatus($dependent_task['task_id'], Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                        }
                    }
                    error_log("Sync DECLINED task_id: $task_id, memsource: {$job['uid']}, reverting from COMPLETED");
                }
            }
        }
    }

    public function get_top_level($id)
    {
        $pos = strpos($id, '.');
        if ($pos === false) return $id;
        return substr($id, 0, $pos);
    }

    public function get_tasks_for_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_tasks_for_project', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return [];
        $tasks = [];
        foreach ($result as $row) {
            $tasks[$row['memsource_task_uid']] = $row;
        }
        return $tasks;
    }

    public function are_translations_not_all_complete($task, $memsource_task)
    {

        $translations_not_all_complete = 0;
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $memsource_task) {
            $top_level = $this->get_top_level($memsource_task['internalId']);
            $project_tasks = $this->get_tasks_for_project($task->getProjectId());
            foreach ($project_tasks as $project_task) {
                if ($top_level == $this->get_top_level($project_task['internalId'])) {
                    if ($memsource_task['workflowLevel'] > $project_task['workflowLevel']) { // Dependent on
                        if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                            if ($project_task['task-status_id'] != Common\Enums\TaskStatusEnum::COMPLETE) {
                                $translations_not_all_complete = 1;
                                error_log("translations_not_all_complete {$memsource_task['task_id']}: {$project_task['id']} {$project_task['internalId']}");//(**)
                            }
                        }
                    }
                }
            }
        }
        return $translations_not_all_complete;
    }

    public function delete_task_directly($task_id)
    {
        LibAPI\PDOWrapper::call('delete_task_directly', LibAPI\PDOWrapper::cleanse($task_id));
    }
}
