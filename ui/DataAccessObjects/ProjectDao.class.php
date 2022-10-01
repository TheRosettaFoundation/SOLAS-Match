<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;
use \SolasMatch\UI\RouteHandlers as Route;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/lib/CacheHelper.class.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__.'/../../api/lib/PDOWrapper.class.php';


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

    public function getProjectTasksArray($project_id)
    {
        $result = LibAPI\PDOWrapper::call('getTask', 'null, ' . LibAPI\PDOWrapper::cleanseNull($project_id) . ', null, null, null, null, null, null, null, null, null, null, null, null');
        if (empty($result)) return [];
        return $result;
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
error_log("call projectInsertAndUpdate($args): Success");//(**)
            $project = Common\Lib\ModelFactory::buildModel('Project', $result[0]);
        }
else error_log("call projectInsertAndUpdate($args): Fail");//(**)
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

    public function archiveProject($projectId, $userId)
    {
        $memsource_project = $this->get_memsource_project($projectId);

        $request = "{$this->siteApi}v0/projects/archiveProject/$projectId/user/$userId";
        $ret = $this->client->call(null, $request, Common\Enums\HttpMethodEnum::PUT);
        if ($ret && $memsource_project) {
            $memsource_project_uid = $memsource_project['memsource_project_uid'];
            $memsourceApiToken = Common\Lib\Settings::get('memsource.memsource_api_token');
            $ch = curl_init("https://cloud.memsource.com/web/api2/v1/projects/$memsource_project_uid");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $memsourceApiToken"]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['archived' => true]));
            $result = curl_exec($ch);
            curl_close($ch);
            error_log($result);
        }
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
        $request = "{$this->siteApi}v0/io/download/project/$projectId";
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
        $request = "{$this->siteApi}v0/io/download/projectImage/$projectId";
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
        $selections = $this->get_selections();
        $language_options = [];
        foreach ($selections as $selection) {
            if ($selection['enabled']) $language_options[$selection['language_code'] . '-' . $selection['country_code']] = $selection['selection'];
        }
        asort($language_options);
        return $language_options;
    }

    public function convert_selection_to_language_country($selection)
    {
        $trommons_language_code = substr($selection, 0, strpos($selection, '-'));
        $trommons_country_code  = substr($selection, strpos($selection, '-') + 1);
        return [$trommons_language_code, $trommons_country_code];
    }

    public function convert_memsource_to_language_country($memsource)
    {
        $selections = $this->get_selections();
        foreach ($selections as $selection) {
            if ($selection['memsource'] === $memsource) return [$selection['language_code'], $selection['country_code']];
        }

        $pos = strpos($memsource, '_');
        if ($pos !== false) $memsource = substr($memsource, 0, $pos);
        return [$memsource, '--'];
    }

    public function convert_language_country_to_memsource($kp_language, $kp_country)
    {
        $selections = $this->get_selections();
        foreach ($selections as $selection) {
            if ($selection['language_code'] === $kp_language AND $selection['country_code'] === $kp_country) return $selection['memsource'];
        }

        error_log("Failed: convert_language_country_to_memsource($kp_language, $kp_country)");
        return 0;
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

    public function set_memsource_project($project_id, $memsource_project_id, $memsource_project_uid, $created_by_uid, $owner_uid, $workflowLevels)
    {
        LibAPI\PDOWrapper::call('set_memsource_project',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($memsource_project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($memsource_project_uid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($created_by_uid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($owner_uid) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[0]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[1]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[2]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[3]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[4]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[5]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[6]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[7]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[8]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[9]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[10]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[11]));
    }

    public function update_memsource_project($project_id, $workflowLevels)
    {
        LibAPI\PDOWrapper::call('update_memsource_project',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[0]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[1]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[2]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[3]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[4]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[5]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[6]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[7]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[8]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[9]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[10]) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($workflowLevels[11]));
    }

    public function update_memsource_project_owner($project_id, $owner_uid)
    {
        LibAPI\PDOWrapper::call('update_memsource_project_owner',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($owner_uid));
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

    public function set_memsource_self_service_project($memsource_project_id, $split)
    {
        LibAPI\PDOWrapper::call('set_memsource_self_service_project', LibAPI\PDOWrapper::cleanse($memsource_project_id) . ',' . LibAPI\PDOWrapper::cleanse($split));
    }

    public function get_memsource_self_service_project($memsource_project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_memsource_self_service_project', LibAPI\PDOWrapper::cleanse($memsource_project_id));

        if (empty($result)) return 0;

        return $result[0];
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
        $result = LibAPI\PDOWrapper::call('get_queue_copy_task_original_files', '');
        if (empty($result)) return [];
        return $result;
    }

    public static function dequeue_copy_task_original_file($task_id)
    {
        LibAPI\PDOWrapper::call('dequeue_copy_task_original_file', LibAPI\PDOWrapper::cleanse($task_id));
    }

    public function queue_asana_project($project_id)
    {
        error_log("queue_asana_project($project_id)");
        LibAPI\PDOWrapper::call('queue_asana_project', LibAPI\PDOWrapper::cleanse($project_id));
    }

    public function get_queue_asana_projects()
    {
        $result = LibAPI\PDOWrapper::call('get_queue_asana_projects', '');
        if (empty($result)) return [];
        return $result;
    }

    public static function dequeue_asana_project($project_id)
    {
        LibAPI\PDOWrapper::call('dequeue_asana_project', LibAPI\PDOWrapper::cleanse($project_id));
    }

    public function set_asana_task($project_id, $language_code_source, $language_code_target, $asana_task_id)
    {
        LibAPI\PDOWrapper::call('set_asana_task',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_source) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($language_code_target) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($asana_task_id));
    }

    public function get_asana_tasks($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_asana_tasks', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return [];
        $results = [];
        foreach ($result as $r) {
            $results[$r['language_code_target']] = $r;
        }
        return $results;
    }

    public function get_user_id_from_memsource_user($memsource_user_uid)
    {
        $result = LibAPI\PDOWrapper::call('get_user_id_from_memsource_user', LibAPI\PDOWrapper::cleanseWrapStr($memsource_user_uid));

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

    public function get_users_who_claimed($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_users_who_claimed', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return [];

        $users = [];
        foreach ($result as $row) {
            $users[$row['task_id']] = $row;
        }
        return $users;
    }

    public function sync_split_jobs($memsource_project, $split_uids_filter = false, $parent_tasks_filter = false, $words_default = 0)
    {
error_log('split_uids_filter:' . print_r($split_uids_filter, true));//(**)
error_log('parent_tasks_filter:' . print_r($parent_tasks_filter, true));//(**)
        $userDao = new UserDao();
        $taskDao = new TaskDao();
        $project_route_handler = new Route\ProjectRouteHandler();
        $project_id            = $memsource_project['project_id'];
        $memsource_project_uid = $memsource_project['memsource_project_uid'];

        $jobs = $userDao->memsource_list_jobs($memsource_project_uid, $project_id);
        if (empty($jobs)) return 0;

        $memsource_project = $this->get_memsource_project($project_id); // Workflow could have been updated

        foreach ($jobs as $uid => $job) {
            if ($split_uids_filter && !in_array($uid, $split_uids_filter)) continue;
            $memsource_task = $this->get_memsource_task_by_memsource_uid($uid);
            $full_job = $userDao->memsource_get_job($memsource_project_uid, $uid);
            if ($full_job) {
                if (empty($memsource_task)) {
                    if (!$error = $this->create_task($memsource_project, $full_job, $words_default)) {
                        error_log("Created task for job $uid {$full_job['innerId']} in project $project_id");
                        $memsource_task = $this->get_memsource_task_by_memsource_uid($uid);
                        $this->update_task_from_job($memsource_project, $full_job, $memsource_task);
                    } elseif ($error != '-') {
                        return $error;
                    }
                } else {
                    $this->update_task_from_job($memsource_project, $full_job, $memsource_task);
                }
            } else error_log("Could not find job $uid in project $project_id (or is top level)");
        }

        $project_tasks = $this->get_tasks_for_project($project_id);
        foreach ($project_tasks as $uid => $project_task) {
                if (empty($jobs[$uid])) {
                    if ($parent_tasks_filter && !in_array($project_task['id'], $parent_tasks_filter)) continue;
                    $this->adjust_for_deleted_task($memsource_project, $project_task);
                    $this->delete_task_directly($project_task['id']);
                    error_log("Deleted task {$project_task['id']} for job $uid {$project_task['internalId']} in project $project_id");
                }
        }

        $project_tasks = $this->get_tasks_for_project($project_id);
        foreach ($project_tasks as $memsource_task) {
            if ($memsource_task['task-status_id'] == Common\Enums\TaskStatusEnum::IN_PROGRESS || $memsource_task['task-status_id'] == Common\Enums\TaskStatusEnum::COMPLETE) {
                // If Sync has happened after this task was claimed, perhaps creating new split tasks in other workflow...
                $task_id = $memsource_task['task_id'];
                if ($user_id = $this->getUserClaimedTask($task_id)) {
                    // Add corresponding task(s) to deny list for translator
                    $top_level = $this->get_top_level($memsource_task['internalId']);
                    foreach ($project_tasks as $project_task) {
                        if ($top_level == $this->get_top_level($project_task['internalId'])) {
                            if ($memsource_task['workflowLevel'] != $project_task['workflowLevel']) { // Not same workflowLevel
                                if ( $memsource_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION ||
                                    ($memsource_task['task-type_id'] == Common\Enums\TaskTypeEnum::PROOFREADING && $project_task['task-type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION)) {
//(**)Need to add additional code to deny if user translated ANY file (not just current)
//(**)Will there be index on QA/Proofread?
                                    if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                                        error_log("Adding $user_id to Deny List for {$project_task['id']} {$project_task['internalId']} (maybe new split tasks in other workflow)");
                                        $taskDao->addUserToTaskBlacklist($user_id, $project_task['id']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->make_tasks_claimable($project_id);
        return 0;
    }

    private function create_task($memsource_project, $job, $words_default)
    {
        $taskDao = new TaskDao();
        $task = new Common\Protobufs\Models\Task();

        if (empty($job['filename'])) {
            error_log("No filename in new jobPart {$job['uid']}");
            return '-';
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
        $task->setTaskStatus(Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);

        $taskTargetLocale = new Common\Protobufs\Models\Locale();
        list($target_language, $target_country) = $this->convert_memsource_to_language_country($job['targetLang']);
        $taskTargetLocale->setLanguageCode($target_language);
        $taskTargetLocale->setCountryCode($target_country);
        $task->setTargetLocale($taskTargetLocale);

        if (empty($job['workflowLevel'])) {
            error_log("Sync Can't find workflowLevel in new job {$job['uid']} for: {$job['filename']}, assuming Translation");
            $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
        } elseif ($job['workflowLevel'] > 12) {
            error_log("Sync Don't support workflowLevel > 12: {$job['workflowLevel']} in new job {$job['uid']} for: {$job['fileName']}");
            return '-';
        } else {
            $workflow_levels = [$memsource_project['workflow_level_1'], $memsource_project['workflow_level_2'], $memsource_project['workflow_level_3'], $memsource_project['workflow_level_4'], $memsource_project['workflow_level_5'], $memsource_project['workflow_level_6'], $memsource_project['workflow_level_7'], $memsource_project['workflow_level_8'], $memsource_project['workflow_level_9'], $memsource_project['workflow_level_10'], $memsource_project['workflow_level_11'], $memsource_project['workflow_level_12']];
            $taskType = $workflow_levels[$job['workflowLevel'] - 1];
            error_log("Sync taskType: $taskType, workflowLevel: {$job['workflowLevel']}");
            if (!empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$taskType])) $taskType = Common\Enums\TaskTypeEnum::$task_type_to_enum[$taskType];
            elseif ($taskType == '' && $job['workflowLevel'] == 1) {
                $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
                $workflow_levels = ['Translation'];
            } else {
                error_log("Sync Can't find expected taskType ($taskType) in new job {$job['uid']} for: {$job['filename']}");
                return '-';
            }
        }
        $task->setTaskType($taskType);

error_log("words_default: $words_default");//(**)
if (empty($job['wordsCount']) || $job['wordsCount'] == -1) error_log('BAD job[wordsCount]');//(**)
        if ($words_default && (empty($job['wordsCount']) || $job['wordsCount'] == -1)) $job['wordsCount'] = $words_default;
        if (!empty($job['wordsCount'])) {
            if ($job['wordsCount'] == -1) {
                error_log("Sync Memsource not ready (wordsCount: -1) in new job {$job['innerId']}/{$job['uid']} for: {$job['filename']}");
                return "Memsource not ready for job ID: {$job['innerId']}, wait a bit and click Sync Memsource again";
            }
            $task->setWordCount($job['wordsCount']);
            $this->queue_asana_project($project_id);
            if ($this->first_workflow($taskType, $memsource_project)) {
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

        if (!empty($job['dateDue'])) $task->setDeadline(substr($job['dateDue'], 0, 10) . ' ' . substr($job['dateDue'], 11, 8));
        else                         $task->setDeadline($project->getDeadline());

        $task->setPublished(1);

        $task_id = $taskDao->createTaskDirectly($task);
        if (!$task_id) {
            error_log("Failed to add task for new job {$job['uid']} for: {$job['filename']}");
            return '-';
        }
        error_log("Added Task: $task_id for new job {$job['uid']} for: {$job['filename']}");

        $success = $this->set_memsource_task($task_id, 0, $job['uid'], '',
            empty($job['innerId'])       ? 0 : $job['innerId'],
            empty($job['workflowLevel']) ? 0 : $job['workflowLevel'],
            empty($job['beginIndex'])    ? 0 : $job['beginIndex'],
            empty($job['endIndex'])      ? 0 : $job['endIndex'],
            0);
error_log("set_memsource_task($task_id, 0, {$job['uid']}...), success: $success");//(**)
        if (!$success) { // May be because of button double click
            $this->delete_task_directly($task_id);
            error_log("Sync delete_task_directly($task_id) because of set_memsource_task fail");
            return '-';
        }

        $forward_order = [];
        $reverse_order = [];
        foreach ($workflow_levels as $i => $workflow_level) {
            if (!empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level])) {
                $forward_order[Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level]] = empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i + 1]]) ? 0 : Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i + 1]];
                $reverse_order[Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level]] = empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i - 1]]) ? 0 : Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i - 1]];
            }
        }
//(**)Old comment: Translation task should already have been created
        $innerId = empty($job['innerId']) ? 0 : $job['innerId'];
        $top_level = $this->get_top_level($innerId);
        $project_tasks = $this->get_tasks_for_project($project_id);
        foreach ($project_tasks as $project_task) {
            if ($top_level == $this->get_top_level($project_task['internalId'])) {
                //(**) Matches on same file & same language, for QA or Proofreading may need to be wider
                if ($forward_order[$taskType]) {
                     if ($forward_order[$taskType] == $project_task['task-type_id'])
                         $this->set_taskclaims_required_to_make_claimable($task_id, $project_task['task_id'], $project_id);
                }
                if ($reverse_order[$taskType]) {
                     if ($reverse_order[$taskType] == $project_task['task-type_id'])
                         $this->set_taskclaims_required_to_make_claimable($project_task['task_id'], $task_id, $project_id);
                }
            }
        }

        if($this->is_task_claimable($task_id)) $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::PENDING_CLAIM);

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
        return 0;
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
        if ($this->first_workflow($taskType, $memsource_project)) {
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

    public function first_workflow($taskType, $memsource_project)
    {
        if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION ||
            empty($memsource_project['workflow_level_1']) ||
            Common\Enums\TaskTypeEnum::$task_type_to_enum[$memsource_project['workflow_level_1']] == $taskType) return true;
        return false;
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
            if (!empty($job['providers'][0]['uid']) && count($job['providers']) == 1) {
                $user_id = $this->get_user_id_from_memsource_user($job['providers'][0]['uid']);
                if (!$user_id) {
                    error_log("Can't find user_id for {$job['providers'][0]['uid']} in Sync status: ACCEPTED");
                    return;
                }

                if (!$taskDao->taskIsClaimed($task_id)) {
                    $taskDao->claimTaskAndDeny($task_id, $user_id, $memsource_task);
                    error_log("Sync ACCEPTED in memsource task_id: $task_id, user_id: $user_id, memsource job: {$job['uid']}, user: {$job['providers'][0]['uid']}");
                } else { // Probably being set by admin in Memsource from COMPLETED_BY_LINGUIST back to ASSIGNED
                  if ($taskDao->getTaskStatus($task_id) == Common\Enums\TaskStatusEnum::COMPLETE) {
                    $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::IN_PROGRESS);
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
            error_log("Sync COMPLETED task_id: $task_id, memsource: {$job['uid']}");
          }
        }
        if ($status == 'DECLINED' || $status == 'NEW') { // Unclaimed ('DECLINED_BY_LINGUIST' in Hook)
            if ($taskDao->taskIsClaimed($task_id)) {
                $user_id = $this->getUserClaimedTask($task_id);
                if ($user_id) {
                    $taskDao->unclaimTask($task_id, $user_id);
                    $taskDao->sendOrgFeedbackDeclined($task_id, $user_id, $memsource_project);
                }
                error_log("Sync DECLINED task_id: $task_id, user_id: $user_id, memsource job: {$job['uid']}");
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
        if ($task->getTaskType() != Common\Enums\TaskTypeEnum::TRANSLATION && $memsource_task) {
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

    public function identify_claimed_but_not_yet_in_progress($project_id)
    {
        $memsource_tasks = $this->get_tasks_for_project($project_id);
        $project_tasks = $memsource_tasks;
        $translations_not_all_complete = [];
        foreach ($memsource_tasks as $memsource_task) {
            if ($memsource_task['task-status_id'] == Common\Enums\TaskStatusEnum::IN_PROGRESS) { // This status can sometimes display as CLAIMED if not all translations are complete
                $translations_not_all_complete[$memsource_task['id']] = 0;
                if ($memsource_task['task-type_id'] != Common\Enums\TaskTypeEnum::TRANSLATION) {
                    $top_level = $this->get_top_level($memsource_task['internalId']);
                    foreach ($project_tasks as $project_task) {
                        if ($top_level == $this->get_top_level($project_task['internalId'])) {
                            if ($memsource_task['workflowLevel'] > $project_task['workflowLevel']) { // Dependent on
                                if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                                    if ($project_task['task-status_id'] != Common\Enums\TaskStatusEnum::COMPLETE) {
                                        $translations_not_all_complete[$memsource_task['id']] = 1;
                                        error_log("identify_claimed_but_not_yet_in_progress($project_id), translations_not_all_complete {$memsource_task['task_id']}: {$project_task['id']} {$project_task['internalId']}");//(**)
                                    }
                                }
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

    public function set_dateDue_in_memsource_when_new($memsource_project_uid, $memsource_task_uid, $deadline)
    {
        $ch = curl_init(Common\Lib\Settings::get('memsource.api_url_v1') . "projects/$memsource_project_uid/jobs/$memsource_task_uid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $authorization = 'Authorization: Bearer ' . Common\Lib\Settings::get('memsource.memsource_api_token');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['dateDue' => substr($deadline, 0, 10) . 'T' . substr($deadline, 11, 8) . 'Z', 'status' => 'NEW']));
        curl_exec($ch);
        curl_close($ch);
        error_log("set_dateDue_in_memsource_when_new($memsource_project_uid, $memsource_task_uid, $deadline)");
    }

    public function delete_not_accepted_user()
    {
        LibAPI\PDOWrapper::call('delete_not_accepted_user', '');
    }

    public function get_selections()
    {
        $selections = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::SELECTIONS,
            2592000,
            function ($args) {
                return LibAPI\PDOWrapper::call('get_selections', '');
            },
            []
        );
        return $selections;
    }

    public static function get_task_type_details()
    {
        $get_task_type_details = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::TASK_TYPE_DETAILS,
            2592000,
            function ($args) {
                return LibAPI\PDOWrapper::call('get_task_type_details', '');
            },
            []
        );
        return $get_task_type_details;
    }

    public function get_TEMP_TWB_to_CLEAR_email()
    {
        return LibAPI\PDOWrapper::call('get_TEMP_TWB_to_CLEAR_email', '');
    }

    public function set_taskclaims_required_to_make_claimable($task_id, $claimable_task_id, $project_id)
    {
        LibAPI\PDOWrapper::call('set_taskclaims_required_to_make_claimable', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($claimable_task_id) . ',' . LibAPI\PDOWrapper::cleanse($project_id));
    }

    public function is_task_claimable($claimable_task_id)
    {
        $result = LibAPI\PDOWrapper::call('is_task_claimable', LibAPI\PDOWrapper::cleanse($claimable_task_id));
        $result[0]['result'];
    }

    public function make_tasks_claimable($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_tasks_to_be_made_claimable', LibAPI\PDOWrapper::cleanse($project_id));
        LibAPI\PDOWrapper::call('make_tasks_claimable', LibAPI\PDOWrapper::cleanse($project_id));

        if (empty($result)) return;
        foreach ($result as $row) {
            LibAPI\PDOWrapper::call('update_tasks_status', LibAPI\PDOWrapper::cleanse($row['id']) . ',2,NULL');
        }
    }

    public function get_possible_completes()
    {
        $result = LibAPI\PDOWrapper::call('get_possible_completes', '');
        if (empty($result)) return [];
        return $result;
    }

    public function update_tasks_status_conditionally($task_id, $status_id)
    {
        $result = LibAPI\PDOWrapper::call('get_tasks_status', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return;

        if (result[0]['status_id'] != $status_id) LibAPI\PDOWrapper::call('update_tasks_status_plain', LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanse($status_id));
    }
}
