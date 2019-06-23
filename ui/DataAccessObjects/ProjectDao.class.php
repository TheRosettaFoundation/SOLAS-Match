<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
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
}
