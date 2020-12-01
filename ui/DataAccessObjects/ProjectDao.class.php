<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

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

        $topic_id = $this->get_discourse_id($project->getId());
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

    public function generate_language_selection()
    {
        global $from_neon_to_trommons_pair, $from_neon_to_trommons_pair_options_remove, $language_options_changes;
        unset($from_neon_to_trommons_pair["Norwegian Bokm\xE5l"]); // Remove as it is just here to support bad Neon hook

        foreach ($from_neon_to_trommons_pair_options_remove as $remove) {
            unset($from_neon_to_trommons_pair[$remove]);
        }

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
];

$memsource_change_country_to_kp = [
'001' => '--',
'mod' => '--',
'latn' => '900',
'latn_az' => '900',
'latn_bg' => '900',
'latn_ba' => '900',
'latn_gr' => '900',
'latn_ir' => '900',
'latn_am' => '900',
'latn_in' => '900',
'latn_ru' => '900',
'latn_me' => '900',
'latn_rs' => '900',
'latn_ua' => '900',
'latn_uz' => '900',

'cyrl_rs' => 'rs',
'cyrl_me' => 'me',

'cyrl' => '901',
'cyrl_az' => '901',
'cyrl_ba' => '901',
'cyrl_tj' => '901',
'cyrl_uz' => '901',

'arab' => 'pk', // Because sd_arab is the only active 'arab'

'hans' => 'cn',
'hans_cn' => 'cn',
'hant' => 'tw',
'hant_tw' => 'tw',
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
        if (!empty($memsource_change_language_to_kp[$trommons_language_code])) $trommons_language_code = $memsource_change_language_to_kp[$language_code];

        return [$trommons_language_code, $trommons_country_code];
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

    public function set_memsource_task($task_id, $memsource_task_id, $memsource_task_uid, $task, $workflowLevel, $beginIndex, $endIndex)
    {
        LibAPI\PDOWrapper::call('set_memsource_task',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanse($memsource_task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($memsource_task_uid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task) . ',' .
            LibAPI\PDOWrapper::cleanse($workflowLevel) . ',' .
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

    public function update_project_organisation($project_id, $org_id)
    {
        LibAPI\PDOWrapper::call('update_project_organisation', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanse($org_id));
    }

    public function update_task_due_date($task_id, $deadline)
    {
        LibAPI\PDOWrapper::call('update_task_due_date', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($deadline));
    }
}
