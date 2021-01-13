<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/TaskTypeEnum.class.php";
require_once __DIR__."/../../Common/Enums/TaskStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";
require_once __DIR__.'/../../Common/matecat_acceptable_languages.php';

class ProjectRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();

        $app->get(
            "/project/:project_id/view/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "projectView")
        )->via("POST")->name("project-view");

        $app->get(
            "/project/:project_id/alter/",
            array($middleware, "authUserForOrgProject"),
            array($this, "projectAlter")
        )->via("POST")->name("project-alter");

        $app->get(
            "/project/:org_id/create/",
            array($middleware, "authUserForOrg"),
            array($this, "projectCreate")
        )->via("GET", "POST")->name("project-create");

        $app->get(
            "/project/id/:project_id/created/",
            array($middleware, "authUserForOrgProject"),
            array($this, "projectCreated")
        )->name("project-created");

        $app->get(
            "/project/id/:project_id/mark-archived/:sesskey/",
            array($middleware, "authUserForOrgProject"),
            array($this, "archiveProject")
        )->name("archive-project");

        $app->get(
            "/project/:project_id/file/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "downloadProjectFile")
        )->name("download-project-file");

        $app->get(
            "/project/:project_id/image/",
            array($middleware, "authUserForProjectImage"),
            array($this, "downloadProjectImageFile")
        )->name("download-project-image");

        $app->get("/project/:project_id/test/", array($this, "test"));

        $app->get(
            '/project_cron_1_minute/',
            array($this, 'project_cron_1_minute')
        )->name('project_cron_1_minute');

        $app->get(
            '/task_cron_1_minute/',
            array($this, 'task_cron_1_minute')
        )->name('task_cron_1_minute');

        $app->get(
            '/project/:project_id/getwordcount/',
            array($this, 'project_get_wordcount')
        )->name('project_get_wordcount');

        $app->get(
            '/memsource_hook/',
            array($this, 'memsourceHook')
        )->via("POST")->name('memsource_hook');
    }

    public function memsourceHook()
    {
        $app = \Slim\Slim::getInstance();
        if ($app->request->headers->get('X-Memsource-Token') !== Common\Lib\Settings::get('memsource.X-Memsource-Token')) {
            error_log('X-Memsource-Token does not match!');
            die;
        }
        $body = $app->request()->getBody();
        error_log(print_r(json_decode($body, true), true));
        $hook = json_decode($body, true);

        switch ($hook['event']) {
            case 'PROJECT_CREATED':
                $this->create_project($hook);
                break;
            case 'PROJECT_DUE_DATE_CHANGED':
                $this->update_project_due_date($hook);
                break;
            case 'PROJECT_METADATA_UPDATED':
                $this->update_project_client($hook);
                break;
            case 'JOB_CREATED':
                $this->create_task($hook);
                break;
            case 'JOB_STATUS_CHANGED':
                $this->update_task_status($hook);
                break;
            case 'JOB_ASSIGNED':
                $this->job_assigned($hook);
                break;
            case 'JOB_DUE_DATE_CHANGED':
                $this->update_task_due_date($hook);
                break;
        }
        die;
    }

    private function create_project($hook)
    {
        $hook = $hook['project'];
        $project = new Common\Protobufs\Models\Project();
        $projectDao = new DAO\ProjectDao();
if ($projectDao->get_memsource_project_by_memsource_id($hook['id'])) error_log("memsource Project exists {$hook['id']} {$hook['uid']}"); //(**) test code
        if ($projectDao->get_memsource_project_by_memsource_id($hook['id'])) return; // Likely self service project

        $project->setTitle($hook['name']);
        if (!empty($hook['note'])) $project->setDescription($hook['note']);
        else                       $project->setDescription('-');
        $project->setImpact('-');
        if (!empty($hook['dateDue'])) $project->setDeadline(substr($hook['dateDue'], 0, 10) . ' ' . substr($hook['dateDue'], 11, 8));
        else                          $project->setDeadline(gmdate('Y-m-d H:i:s', strtotime('25 days')));
        $project->setWordCount(1);
        list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_memsource_to_language_country($hook['sourceLang']);
        $sourceLocale = new Common\Protobufs\Models\Locale();
        $sourceLocale->setCountryCode($trommons_source_country_code);
        $sourceLocale->setLanguageCode($trommons_source_language_code);
        $project->setSourceLocale($sourceLocale);

        if (empty($hook['client']['id'])) {
            error_log("No client id in new project: {$hook['name']}");
//(**)            return;
$hook['client']['id'] = 0;
        }
        $memsource_client = $projectDao->get_memsource_client_by_memsource_id($hook['client']['id']);
        if (empty($memsource_client)) {
            error_log("No MemsourceOrganisations record for new project: {$hook['name']}, client id: {$hook['client']['id']}");
//(**)            return;
$memsource_client = ['org_id' => 456];//(**) TWB
        }
        $project->setOrganisationId($memsource_client['org_id']);

        if (!empty($hook['dateCreated'])) $project->setCreatedTime(substr($hook['dateCreated'], 0, 10) . ' ' . substr($hook['dateCreated'], 11, 8));
        else                              $project->setCreatedTime(gmdate('Y-m-d H:i:s'));

        $project = $projectDao->createProjectDirectly($project);
        error_log("Created Project: {$hook['name']}");
        if (empty($project)) {
            error_log("Failed to create Project: {$hook['name']}");
            return;
        }

        $project_id = $project->getId();
        $destination = Common\Lib\Settings::get("files.upload_path") . "proj-$project_id/";
        mkdir($destination, 0755);

        $workflowLevels = ['', '', '']; // Will contain 'Translation' or 'Revision' for workflowLevel 1 possibly up to 3
        if (!empty($hook['workflowSteps'])) {
            foreach ($hook['workflowSteps'] as $step) {
                foreach ($workflowLevels as $i => $w) {
                    if ($step['workflowLevel'] == $i + 1) $workflowLevels[$i] = $step['name'];
                }
            }
        }

        $projectDao->set_memsource_project($project_id, $hook['id'], $hook['uid'],
            empty($hook['createdBy']['id']) ? 0 : $hook['createdBy']['id'],
            empty($hook['owner']['id']) ? 0 : $hook['owner']['id'],
            $workflowLevels);

        // Create a topic in the Community forum (Discourse) and a project in Asana
        $target_languages = '';
        if (!empty($hook['targetLangs'])) {
            foreach ($hook['targetLangs'] as $index => $value) {
                list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_memsource_to_language_country($value);
                $hook['targetLangs'][$index] = "{$trommons_source_language_code}-{$trommons_source_country_code}";
            }
            $target_languages = implode(',', $hook['targetLangs']);
        }
        error_log("projectCreate create_discourse_topic($project_id, $target_languages)");
        try {
            $this->create_discourse_topic($project_id, $target_languages, ['created_by_id' => empty($hook['createdBy']['id']) ? 0 : $hook['createdBy']['id']]);
        } catch (\Exception $e) {
            error_log('projectCreate create_discourse_topic Exception: ' . $e->getMessage());
        }
    }

    private function update_project_due_date($hook)
    {
        $hook = $hook['project'];
        $projectDao = new DAO\ProjectDao();

        $memsource_project = $projectDao->get_memsource_project_by_memsource_id($hook['id']);
        if (empty($memsource_project)) {
            error_log("Can't find memsource_project for {$hook['id']} in event PROJECT_DUE_DATE_CHANGED");
            return;
        }
        if (!empty($hook['dateDue'])) $projectDao->update_project_due_date($memsource_project['project_id'], substr($hook['dateDue'], 0, 10) . ' ' . substr($hook['dateDue'], 11, 8));
    }

    private function update_project_client($hook)
    {
        $hook = $hook['project'];
        $projectDao = new DAO\ProjectDao();

        $memsource_project = $projectDao->get_memsource_project_by_memsource_id($hook['id']);
        if (empty($memsource_project)) {
            error_log("Can't find memsource_project for {$hook['id']} in event PROJECT_METADATA_UPDATED");
            return;
        }
        if (empty($hook['client']['id'])) return;

        $memsource_client = $projectDao->get_memsource_client_by_memsource_id($hook['client']['id']);
        if (empty($memsource_client)) {
            error_log("No MemsourceOrganisations record for project: {$hook['name']}, client id: {$hook['client']['id']} in event PROJECT_METADATA_UPDATED");
            return;
        }
        $projectDao->update_project_organisation($memsource_project['project_id'], $memsource_client['org_id']);
    }

    private function create_task($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();
        foreach ($hook as $part) {
            $task = new Common\Protobufs\Models\Task();

            if (empty($part['fileName'])) {
                error_log("No fileName in new jobPart {$part['uid']}");
                continue;
            }
            if (empty($part['project']['id'])) {
                error_log("No project id in new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            }
            $memsource_project = $projectDao->get_memsource_project_by_memsource_id($part['project']['id']);
            if (empty($memsource_project)) {
                error_log("Can't find memsource_project for {$part['project']['id']} in new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            }
            $task->setProjectId($memsource_project['project_id']);
            $task->setTitle($part['fileName']);

            $project = $projectDao->getProject($memsource_project['project_id']);
            $projectSourceLocale = $project->getSourceLocale();
            $taskSourceLocale = new Common\Protobufs\Models\Locale();
            $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
            $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
            $task->setSourceLocale($taskSourceLocale);
            $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);

            $taskTargetLocale = new Common\Protobufs\Models\Locale();
            list($target_language, $target_country) = $projectDao->convert_memsource_to_language_country($part['targetLang']);
            $taskTargetLocale->setLanguageCode($target_language);
            $taskTargetLocale->setCountryCode($target_country);
            $task->setTargetLocale($taskTargetLocale);

            if (empty($part['workflowLevel']) || $part['workflowLevel'] > 3) {
                error_log("Can't find workflowLevel in new jobPart {$part['uid']} for: {$part['fileName']}, assuming Translation");
                $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
            } else {
                $taskType = [$memsource_project['workflow_level_1'], $memsource_project['workflow_level_2'], $memsource_project['workflow_level_3']][$part['workflowLevel'] - 1];
                if     ($taskType == 'Translation' || $taskType == '') $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
                elseif ($taskType == 'Revision')                       $taskType = Common\Enums\TaskTypeEnum::PROOFREADING;
                else {
                    error_log("Can't find expected taskType ($taskType) in new jobPart {$part['uid']} for: {$part['fileName']}");
                    continue;
                }
            }
            $task->setTaskType($taskType);

            if (!empty($part['wordsCount'])) {
                $task->setWordCount($part['wordsCount']);
                $project->setWordCount($part['wordsCount']);
            } else {
                $task->setWordCount(1);
            }

            if (!empty($part['dateDue'])) $task->setDeadline(substr($part['dateDue'], 0, 10) . ' ' . substr($part['dateDue'], 11, 8));
            else                          $task->setDeadline($project->getDeadline());

            $task->setPublished(1);

            $prerequisite = 0;
            if (!empty($part['task']) && $taskType == Common\Enums\TaskTypeEnum::PROOFREADING) {
                $prerequisite_task = $projectDao->get_memsource_tasks_for_project_language_type($memsource_project['project_id'], $part['task'], Common\Enums\TaskTypeEnum::TRANSLATION);
                if ($prerequisite_task) {
                    $prerequisite = $prerequisite_task['task_id'];
                    $task->setTaskStatus(Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                }
            }

            $task_id = $taskDao->createTaskDirectly($task);
            if (!$task_id) {
                error_log("Failed to add task for new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            }
            error_log("Added Task: $task_id for new jobPart {$part['uid']} for: {$part['fileName']}");

            $projectDao->set_memsource_task($task_id, !empty($part['id']) ? $part['id'] : 0, $part['uid'], $part['task'], // note 'task' is for Language pair (independent of workflow step)
                empty($part['internalId'])    ? 0 : $part['internalId'],
                empty($part['workflowLevel']) ? 0 : $part['workflowLevel'],
                empty($part['beginIndex'])    ? 0 : $part['beginIndex'], // Begin Segment number
                empty($part['endIndex'])      ? 0 : $part['endIndex'],
                $prerequisite);

            $projectDao->updateProjectDirectly($project);

            $project_id = $project->getId();
            $uploadFolder = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-0";
            mkdir($uploadFolder, 0755, true);
            $filesFolder = Common\Lib\Settings::get('files.upload_path') . "files/proj-$project_id/task-$task_id/v-0";
            mkdir($filesFolder, 0755, true);

            $filename = $part['fileName'];
            file_put_contents("$filesFolder/$filename", ''); // Placeholder
            file_put_contents("$uploadFolder/$filename", "files/proj-$project_id/task-$task_id/v-0/$filename"); // Point to it

            $projectDao->queue_copy_task_original_file($project_id, $task_id, $part['uid'], $filename); // cron will copy file from memsource
        }
    }

    private function update_task_status($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();
        foreach ($hook as $part) {
            $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($part['uid']);
            if (empty($memsource_task)) {
                error_log("Can't find memsource_task for {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: {$part['status']}");
                continue;
            }
            $task_id = $memsource_task['task_id'];
            $taskDao->set_memsource_status($task_id, $part['uid'], $part['status']);

            if ($part['status'] == 'ASSIGNED') {
                if (!empty($part['assignedTo'][0]['linguist']['id']) && count($part['assignedTo']) == 1) {
                    $user_id = $projectDao->get_user_id_from_memsource_user($part['assignedTo'][0]['linguist']['id']);
                    if (!$user_id) {
                        error_log("Can't find user_id for {$part['assignedTo'][0]['linguist']['id']} in event JOB_STATUS_CHANGED, jobPart status: ASSIGNED");
                        continue;
                    }

                    if (!$taskDao->taskIsClaimed($task_id)) {
                        $taskDao->claimTask($task_id, $user_id);
                        error_log("JOB_STATUS_CHANGED ASSIGNED in memsource task_id: $task_id, user_id: $user_id, memsource job: {$part['uid']}, user: {$part['assignedTo'][0]['linguist']['id']}");
                    }
                }
            }
            if ($part['status'] == 'COMPLETED_BY_LINGUIST') {
//(**)                if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 62927); // translators@translatorswithoutborders.org
                if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 3297);

                $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::COMPLETE);
                $taskDao->sendTaskUploadNotifications($task_id, 1);
                $taskDao->set_task_complete_date($task_id);

                if (strpos($memsource_task['internalId'], '.') === false) { // Not split
                    if (empty($part['project']['id'])) {
                        error_log("No project id in {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: COMPLETED_BY_LINGUIST");
                        continue;
                    }
                    $memsource_project = $projectDao->get_memsource_project_by_memsource_id($part['project']['id']);
                    if (empty($memsource_project)) {
                        error_log("Can't find memsource_project for {$part['project']['id']} in {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: COMPLETED_BY_LINGUIST");
                        continue;
                    }
                    $dependent_task = $projectDao->get_memsource_tasks_for_project_language_type($memsource_project['project_id'], $memsource_task['task'], Common\Enums\TaskTypeEnum::PROOFREADING);
                    if ($dependent_task && $dependent_task['prerequisite'] == $task_id) {
                        $taskDao->setTaskStatus($dependent_task['task_id'], Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                        $user_id = $projectDao->getUserClaimedTask($task_id);
                        if ($user_id) $taskDao->addUserToTaskBlacklist($user_id, $dependent_task['task_id']);
                    }
                }
                error_log("COMPLETED_BY_LINGUIST task_id: $task_id, memsource: {$part['uid']}");
            }
            if ($part['status'] == 'DECLINED_BY_LINGUIST') {
                if (!empty($part['assignedTo'][0]['linguist']['id']) && count($part['assignedTo']) == 1) {
                    $user_id = $projectDao->get_user_id_from_memsource_user($part['assignedTo'][0]['linguist']['id']);
                    if (!$user_id) {
                        error_log("Can't find user_id for {$part['assignedTo'][0]['linguist']['id']} in event JOB_STATUS_CHANGED, jobPart status: DECLINED_BY_LINGUIST");
                        continue;
                    }

                    if ($taskDao->taskIsClaimed($task_id)) {
                        $taskDao->unclaimTask($task_id, $user_id);
                        error_log("JOB_STATUS_CHANGED DECLINED_BY_LINGUIST in memsource task_id: $task_id, user_id: $user_id, memsource job: {$part['uid']}, user: {$part['assignedTo'][0]['linguist']['id']}");
                    }
                }
            }
        }
    }

    private function job_assigned($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();
        foreach ($hook as $part) {
            if (!empty($part['assignedTo'][0]['linguist']['id']) && count($part['assignedTo']) == 1) {
                $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($part['uid']);
                if (empty($memsource_task)) {
                    error_log("Can't find memsource_task for {$part['uid']} in event JOB_ASSIGNED jobPart");
                    continue;
                }
                $task_id = $memsource_task['task_id'];

                $user_id = $projectDao->get_user_id_from_memsource_user($part['assignedTo'][0]['linguist']['id']);
                if (!$user_id) {
                    error_log("Can't find user_id for {$part['assignedTo'][0]['linguist']['id']} in event JOB_ASSIGNED jobPart");
                    continue;
                }

                if (!$taskDao->taskIsClaimed($task_id)) {
                    $taskDao->claimTask($task_id, $user_id);
                    error_log("JOB_ASSIGNED in memsource task_id: $task_id, user_id: $user_id, memsource job: {$part['uid']}, user: {$part['assignedTo'][0]['linguist']['id']}");
                }
            }
        }
    }

    private function update_task_due_date($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        foreach ($hook as $part) {
            $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($part['uid']);
            if (empty($memsource_task)) {
                error_log("Can't find memsource_task for {$part['uid']} in event JOB_DUE_DATE_CHANGED");
                continue;
            }
            if (!empty($part['dateDue'])) $projectDao->update_task_due_date($memsource_task['task_id'], substr($part['dateDue'], 0, 10) . ' ' . substr($part['dateDue'], 11, 8));
        }
    }

    public function test($projectId)
    {
        $app = \Slim\Slim::getInstance();
        $extra_scripts = "";

        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $time1 = $time;

        $projectDao = new DAO\ProjectDao();
        $graph = $projectDao->getProjectGraph($projectId);
        $viewer = new Lib\GraphViewer($graph);
        $body = $viewer->constructView();

        $extra_scripts .= $viewer->generateDataScript();
        $extra_scripts .=
            "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/GraphHelper.js\"></script>";
        $extra_scripts .= "<script>
                $(window).load(runStartup);
                function runStartup()
                {
                    prepareGraph();
                    $( \"#tabs\" ).tabs();
                }
            </script>";

        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $time2 = $time;

        $totaltime = ($time2 - $time1);
        $body .= "<br />Running Time: $totaltime seconds.";
        $app->view()->appendData(array(
                    "body"          => $body,
                    "extra_scripts" => $extra_scripts
        ));
        $app->render("empty.tpl");
    }

    public function projectView($project_id)
    {
        $matecat_api = Common\Lib\Settings::get('matecat.url');
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $project = $projectDao->getProject($project_id);
        if (empty($project)) {
            $app->flash('error', 'That project does not exist!');
            $app->redirect($app->urlFor('home'));
        }

        if ($taskDao->isUserRestrictedFromProject($project_id, $user_id)) {
            $app->flash('error', 'You cannot access this project!');
            $app->redirect($app->urlFor('home'));
        }

        $memsource_project = $projectDao->get_memsource_project($project_id);

        $app->view()->setData("project", $project);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            Common\Lib\UserSession::checkCSRFKey($post, 'projectView');

            $task = null;
            if (isset($post['task_id'])) {
                $task = $taskDao->getTask($post['task_id']);
            } elseif (isset($post['revokeTaskId'])) {
                $task = $taskDao->getTask($post['revokeTaskId']);
            }

            if (isset($post['publishedTask']) && isset($post['task_id'])) {
                if ($post['publishedTask']) {
                    $task->setPublished(true);
                } else {
                    $task->setPublished(false);
                }
                error_log("setPublished");
                $taskDao->updateTask($task);
            }

            if (isset($post['trackProject'])) {
                if ($post['trackProject']) {
                    $userTrackProject = $userDao->trackProject($user_id, $project->getId());
                    if ($userTrackProject) {
                        $app->flashNow("success", Lib\Localisation::getTranslation('project_view_7'));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('project_view_8'));
                    }
                } else {
                    $userUntrackProject = $userDao->untrackProject($user_id, $project->getId());
                    if ($userUntrackProject) {
                        $app->flashNow("success", Lib\Localisation::getTranslation('project_view_9'));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('project_view_10'));
                    }
                }
            } elseif (isset($post['trackTask'])) {
                if ($task && $task->getTitle() != "") {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task {$task->getId()}";
                }

                if (!$post['trackTask']) {
                    $response = $userDao->untrackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('project_view_11'), $task_title)
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('project_view_12'), $task_title)
                        );
                    }
                } else {
                    $response = $userDao->trackTask($user_id, $post['task_id']);
                    if ($response) {
                        $app->flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('project_view_13'), $task_title)
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('project_view_14'), $task_title)
                        );
                    }
                }
            }

            if (isset($post['deleteTask'])) {
                $taskDao->deleteTask($post['task_id']);
                $app->flashNow(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('project_view_15'), $task->getTitle())
                );
            }

            if (isset($post['archiveTask'])) {
                $taskDao->archiveTask($post['task_id'], $user_id);
                $app->flashNow(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('project_view_16'), $task->getTitle())
                );
            }

            if (isset($post['trackOrganisation'])) {
                if ($post['trackOrganisation']) {
                    $userTrackOrganisation = $userDao->trackOrganisation($user_id, $project->getOrganisationId());
                    if ($userTrackOrganisation) {
                        $app->flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_success')
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_error')
                        );
                    }
                } else {
                    $userUntrackOrganisation = $userDao->unTrackOrganisation($user_id, $project->getOrganisationId());
                    if ($userUntrackOrganisation) {
                        $app->flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_success')
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_error')
                        );
                    }
                }
            }

            if (isset($post['imageApprove'])) {
                if (!$post['imageApprove']) {
                    $project->setImageApproved(1);
                    $result = $projectDao->setProjectImageStatus($project_id, 1);
                    if ($result)
                    {
                        $app->flashNow(
                            "success",
                            Lib\Localisation::getTranslation('project_view_image_approve_success')
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            Lib\Localisation::getTranslation('project_view_image_approve_failed')
                        );
                    }
                } else {
                    $project->setImageApproved(0);
                    $result = $projectDao->setProjectImageStatus($project_id, 0);
                    if ($result)
                    {
                        $app->flashNow(
                            "success",
                            Lib\Localisation::getTranslation('project_view_image_disapprove_success')
                        );
                    } else {
                        $app->flashNow(
                            "error",
                            Lib\Localisation::getTranslation('project_view_image_approve_failed')
                        );
                    }
                }
            }

            if (!empty($post['copyChunks']) && empty($memsource_project)) {
                $matecat_language_pairs = $taskDao->getMatecatLanguagePairsForProject($project_id);
                $matecat_language_pairs_populated = false;
                if (!empty($matecat_language_pairs)) {
                    $matecat_language_pairs_populated = true;
                    foreach ($matecat_language_pairs as $matecat_language_pair) {
                        if (empty($matecat_language_pair['matecat_id_job'])) $matecat_language_pairs_populated = false;
                    }
                }
                if ($matecat_language_pairs_populated) {
                    $project_chunks = $taskDao->getTaskChunks($project_id);
                    if (empty($project_chunks)) $project_chunks = array();

                    $task_chunks = array();
                    foreach ($project_chunks as $task_chunk) {
                        $task_chunks[$task_chunk['matecat_id_job']][$task_chunk['chunk_number']][$task_chunk['type_id']] = $task_chunk;
                    }

                    $parent_task_by_matecat_id_job_and_type = array();
                    $job_was_chunked = array();
                    foreach ($matecat_language_pairs as $matecat_language_pair) {
                        $parent_task_by_matecat_id_job_and_type[$matecat_language_pair['matecat_id_job']][$matecat_language_pair['type_id']] = $matecat_language_pair['task_id'];

                        $job_was_chunked[$matecat_language_pair['matecat_id_job']] = true;
                    }

                    foreach ($matecat_language_pairs as $matecat_language_pair) {
                        if (empty($task_chunks[$matecat_language_pair['matecat_id_job']])) {
                            $job_was_chunked[$matecat_language_pair['matecat_id_job']] = false;

                            $task_chunks[$matecat_language_pair['matecat_id_job']][0][Common\Enums\TaskTypeEnum::TRANSLATION ] = array(
                                'task_id' => 0,
                                'project_id' => $project_id,
                                'type_id' => Common\Enums\TaskTypeEnum::TRANSLATION,
                                'matecat_langpair' => $matecat_language_pair['matecat_langpair'],
                                'matecat_id_job' => $matecat_language_pair['matecat_id_job'],
                                'chunk_number' => 0,
                                'matecat_id_chunk_password' => $matecat_language_pair['matecat_id_job_password'],
                                'job_first_segment' => '',
                            );
                            $task_chunks[$matecat_language_pair['matecat_id_job']][0][Common\Enums\TaskTypeEnum::PROOFREADING] = array(
                                'task_id' => 0,
                                'project_id' => $project_id,
                                'type_id' => Common\Enums\TaskTypeEnum::PROOFREADING,
                                'matecat_langpair' => $matecat_language_pair['matecat_langpair'],
                                'matecat_id_job' => $matecat_language_pair['matecat_id_job'],
                                'chunk_number' => 0,
                                'matecat_id_chunk_password' => $matecat_language_pair['matecat_id_job_password'],
                                'job_first_segment' => '',
                            );
                        }
                    }

                    $request_for_project = $taskDao->getWordCountRequestForProject($project_id);
                    if ($request_for_project && !empty($request_for_project['matecat_id_project']) && !empty($request_for_project['matecat_id_project_pass'])) {
                        $re = curl_init("{$matecat_api}api/v2/projects/{$request_for_project['matecat_id_project']}/{$request_for_project['matecat_id_project_pass']}/urls");

                        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'GET');
                        curl_setopt($re, CURLOPT_COOKIESESSION, true);
                        curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($re, CURLOPT_AUTOREFERER, true);

                        $httpHeaders = array(
                            'Expect:'
                        );
                        curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

                        curl_setopt($re, CURLOPT_HEADER, true);
                        curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                        $res = curl_exec($re);

                        $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
                        $header = substr($res, 0, $header_size);
                        $res = substr($res, $header_size);
                        $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                        curl_close($re);

                        if ($responseCode == 200) {
                            $response_data = json_decode($res, true);
                            if (!empty($response_data['urls']['jobs'])) {
                                $chunks = $taskDao->getStatusOfSubChunks($project_id); // It is possible that this should have been used instead of /urls above, but would involve recode/retest
                                $segment_by_job_and_password = [];
                                foreach ($chunks as $chunk) {
                                    $segment_by_job_and_password[$chunk['matecat_id_job'] . '|' . $chunk['matecat_id_chunk_password']] = $chunk['job_first_segment'];
                                }

                                $jobs = $response_data['urls']['jobs'];
                                foreach ($jobs as $job) {
                                    if (!empty($job['chunks']) && !empty($job['id'])) {
                                        $matecat_id_job = $job['id'];

                                        $chunks = $job['chunks'];
                                        $number_of_chunks = count($chunks);

                                        foreach ($chunks as $chunk_number => $chunk) {
                                            if (empty($chunks[$chunk_number]['password']) && !empty($chunks[$chunk_number]['translate_url'])) {
                                                // 20191102 MateCat 2.9.2e no longer has "password" key here, need to extract it from "translate_url"
                                                $chunks[$chunk_number]['password'] = substr($chunks[$chunk_number]['translate_url'], strrpos($chunks[$chunk_number]['translate_url'], '-') + 1);
                                            }
                                        }

                                        $was_chunked = !empty($job_was_chunked[$matecat_id_job]);
                                        $chunked_now = $number_of_chunks > 1;
                                        if     (!$was_chunked && !$chunked_now) $matched = true;
                                        elseif (!$was_chunked &&  $chunked_now) $matched = false;
                                        elseif ( $was_chunked && !$chunked_now) $matched = false;
                                        else { //$was_chunked &&  $chunked_now
                                            $matched = true;
                                            foreach ($chunks as $chunk_number => $chunk) {
                                                if (empty($task_chunks[$matecat_id_job][$chunk_number][Common\Enums\TaskTypeEnum::TRANSLATION]['matecat_id_chunk_password']) ||
                                                    $task_chunks[$matecat_id_job][$chunk_number][Common\Enums\TaskTypeEnum::TRANSLATION]['matecat_id_chunk_password'] != $chunk['password']) {

                                                    $matched = false;
                                                }
                                            }
                                        }

                                        if (!$matched) {
                                            $parent_task_id_translation  = 0;
                                            $parent_task_id_proofreading = 0;
                                            if (!empty($parent_task_by_matecat_id_job_and_type[$matecat_id_job][Common\Enums\TaskTypeEnum::TRANSLATION])) {
                                                $parent_task_id_translation  = $parent_task_by_matecat_id_job_and_type[$matecat_id_job][Common\Enums\TaskTypeEnum::TRANSLATION];
                                            }
                                            if (!empty($parent_task_by_matecat_id_job_and_type[$matecat_id_job][Common\Enums\TaskTypeEnum::PROOFREADING])) {
                                                $parent_task_id_proofreading = $parent_task_by_matecat_id_job_and_type[$matecat_id_job][Common\Enums\TaskTypeEnum::PROOFREADING];
                                            }
                                            if (!empty($parent_task_id_translation ) || !empty($parent_task_id_proofreading)) {
                                                if (empty($parent_task_id_translation )) $parent_task_id_translation  = $parent_task_id_proofreading;
                                                if (empty($parent_task_id_proofreading)) $parent_task_id_proofreading = $parent_task_id_translation;

                                                $parent_translation_task  = $taskDao->getTask($parent_task_id_translation);
                                                $parent_proofreading_task = $taskDao->getTask($parent_task_id_proofreading);

                                                if ($parent_task_id_translation === $parent_task_id_proofreading) {
                                                    // Need to calculate Translation Deadline, 3 days earlier if it will fit
                                                    $deadline = strtotime($parent_proofreading_task->getDeadline());
                                                    $deadline_less_3_days = $deadline - 3*24*60*60;
                                                    if ($deadline_less_3_days < time())    $deadline_less_3_days = time() + 2*24*60*60;
                                                    if ($deadline_less_3_days > $deadline) $deadline_less_3_days = $deadline;
                                                    error_log("Parent task_id: $parent_task_id_translation, deadline will be inherited: " . date("Y-m-d H:i:s", $deadline_less_3_days));
                                                    $parent_translation_task->setDeadline(date("Y-m-d H:i:s", $deadline_less_3_days));
                                                }

                                                if ($was_chunked) {
                                                    foreach ($task_chunks[$matecat_id_job] as $chunk_item) {
                                                        $taskDao->deleteTask($chunk_item[Common\Enums\TaskTypeEnum::TRANSLATION ]['task_id']);
                                                        $taskDao->deleteTask($chunk_item[Common\Enums\TaskTypeEnum::PROOFREADING]['task_id']);
                                                    }
                                                    // $taskDao->removeTaskChunks($matecat_id_job); WILL BE DONE BY DELETE CASCADE
                                                }

                                                if ($chunked_now) {
                                                    foreach ($chunks as $chunk_number => $chunk) {
                                                        $job_first_segment = '';
                                                        if (!empty($segment_by_job_and_password[$matecat_id_job . '|' . $chunk['password']])) $job_first_segment = $segment_by_job_and_password[$matecat_id_job . '|' . $chunk['password']];

                                                        // Ideally Tasks should be created after the TaskChunks as there could, in theory, be an immediate attempt to claim the task linked to the chunk
                                                        // However we are not doing that here
                                                        $task_id = $this->addChunkTask(
                                                            $taskDao,
                                                            $project_id,
                                                            $parent_translation_task,
                                                            Common\Enums\TaskTypeEnum::TRANSLATION,
                                                            $chunk_number,
                                                            $number_of_chunks);
                                                        $taskDao->insertTaskChunks(
                                                            $task_id,
                                                            $project_id,
                                                            Common\Enums\TaskTypeEnum::TRANSLATION,
                                                            $task_chunks[$matecat_id_job][0][Common\Enums\TaskTypeEnum::TRANSLATION ]['matecat_langpair'],
                                                            $matecat_id_job,
                                                            $chunk_number,
                                                            $chunk['password'],
                                                            $job_first_segment);
                                                        $task_id = $this->addChunkTask(
                                                            $taskDao,
                                                            $project_id,
                                                            $parent_proofreading_task,
                                                            Common\Enums\TaskTypeEnum::PROOFREADING,
                                                            $chunk_number,
                                                            $number_of_chunks);
                                                        $taskDao->insertTaskChunks(
                                                            $task_id,
                                                            $project_id,
                                                            Common\Enums\TaskTypeEnum::PROOFREADING,
                                                            $task_chunks[$matecat_id_job][0][Common\Enums\TaskTypeEnum::PROOFREADING]['matecat_langpair'],
                                                            $matecat_id_job,
                                                            $chunk_number,
                                                            $chunk['password'],
                                                            $job_first_segment);
                                                    }
                                                }
                                            } else {
                                                $app->flashNow('error', "Could not find parent translation or revising task for chunks (Job id: $matecat_id_job)");
                                            }
                                        }
                                    } else {
                                        $app->flashNow('error', 'No chunks or id found for job');
                                    }
                                }
                            } else {
                                $app->flashNow('error', 'No jobs found');
                            }
                        } else {
                            $app->flashNow('error', "Could not get data from Kató TM, Response Code: $responseCode");
                        }
                    } else {
                        $app->flashNow('error', 'Could not get matecat_id_project (WordCountRequestForProjects)');
                    }
                } else {
                    $app->flashNow('error', 'No MateCat project (MatecatLanguagePairs) found for this project in Kató Platform');
                }
            }
            if (!empty($post['copyChunks']) && !empty($memsource_project)) {
                $projectDao->sync_split_jobs($memsource_project);
            }
        }

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $project_tags = $projectDao->getProjectTags($project_id);
        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($user_id, $project->getOrganisationId());

        $isSiteAdmin = $adminDao->isSiteAdmin($user_id);
        $isAdmin = $adminDao->isOrgAdmin($project->getOrganisationId(), $user_id) || $isSiteAdmin;

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        //$allow_downloads = array();
        if ($isOrgMember || $isAdmin) {
            $userSubscribedToProject = $userDao->isSubscribedToProject($user_id, $project_id);
            $taskMetaData = array();
            $project_tasks = $projectDao->getProjectTasks($project_id);
            $taskLanguageMap = array();
            if ($project_tasks) {
                foreach ($project_tasks as $task) {
                    $targetLocale = $task->getTargetLocale();
                    $taskTargetLanguage = $targetLocale->getLanguageCode();
                    $taskTargetCountry = $targetLocale->getCountryCode();
                    $taskLanguageMap["$taskTargetLanguage,$taskTargetCountry"][] = $task;
                    $task_id = $task->getId();
                    $metaData = array();
                    $response = $userDao->isSubscribedToTask($user_id, $task_id);
                    if ($response == 1) {
                        $metaData['tracking'] = true;
                    } else {
                        $metaData['tracking'] = false;
                    }
                    $taskMetaData[$task_id] = $metaData;
                    //$allow_downloads[$task_id] = $taskDao->get_allow_download($task);
                }
            }

            $graph = $projectDao->getProjectGraph($project_id);
            $viewer = new Lib\GraphViewer($graph);
            $graphView = $viewer->constructView();

            $extra_scripts = "";
            $extra_scripts .= $viewer->generateDataScript();
            $extra_scripts .= file_get_contents(__DIR__."/../js/GraphHelper.js");
            $extra_scripts .= file_get_contents(__DIR__."/../js/project-view.js");
            $extra_scripts .= file_get_contents(__DIR__."/../js/TaskView1.js");
            // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
            $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

            $app->view()->appendData(array(
                    "org" => $org,
                    "graph" => $graphView,
                    "extra_scripts" => $extra_scripts,
                    "projectTasks" => $project_tasks,
                    "taskMetaData" => $taskMetaData,
                    "userSubscribedToProject" => $userSubscribedToProject,
                    "project_tags" => $project_tags,
                    "taskLanguageMap" => $taskLanguageMap
            ));
        } else {
            $project_tasks = $taskDao->getVolunteerProjectTasks($project_id, $user_id);
            $volunteerTaskLanguageMap = array();
            foreach ($project_tasks as $task) {
                $volunteerTaskLanguageMap[$task['target_language_code'] . ',' . $task['target_country_code']][] = $task;
            }

            $extra_scripts = file_get_contents(__DIR__."/../js/TaskView1.js");
            // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
            $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

            $app->view()->appendData(array(
                "extra_scripts" => $extra_scripts,
                "org" => $org,
                'volunteerTaskLanguageMap' => $volunteerTaskLanguageMap,
                "project_tags" => $project_tags
            ));
        }

        $preventImageCacheToken = time(); //see http://stackoverflow.com/questions/126772/how-to-force-a-web-browser-not-to-cache-images

        $creator = $taskDao->get_creator($project_id, $memsource_project);
        $pm = $creator['email'];
        if (strpos($pm, '@translatorswithoutborders.org') === false) $pm = 'projects@translatorswithoutborders.org';

        $app->view()->appendData(array(
                'sesskey'       => $sesskey,
                "isOrgMember"   => $isOrgMember,
                "isAdmin"       => $isAdmin,
                "isSiteAdmin"   => $isSiteAdmin,
                'taskTypeColours' => $taskTypeColours,
                "imgCacheToken" => $preventImageCacheToken,
                'discourse_slug' => $projectDao->discourse_parameterize($project),
                'memsource_project'   => $memsource_project,
                'matecat_analyze_url' => $taskDao->get_matecat_analyze_url($project_id, $memsource_project),
                'pm' => $pm,
                'userSubscribedToOrganisation' => $userSubscribedToOrganisation
        ));
                //'allow_downloads'     => $allow_downloads,
        $app->render("project/project.view.tpl");
    }

    private function addChunkTask(
        $taskDao,
        $project_id,
        $parent_task,
        $type_id,
        $chunk_number,
        $number_of_chunks)
    {
        $task = new Common\Protobufs\Models\Task();
        $task->setProjectId($project_id);
        $task->setTitle("Chunk $chunk_number of " . $parent_task->getTitle());
        $task->setTaskType($type_id);
        $task->setSourceLocale($parent_task->getSourceLocale());
        $task->setTargetLocale($parent_task->getTargetLocale());
        $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);

        $word_count = $parent_task->getWordCount() / $number_of_chunks;
        if ($word_count < 1) $word_count = 1;
        $task->setWordCount($word_count);

        $task->setDeadline($parent_task->getDeadline());
        //$task->setPublished($parent_task->getPublished());
        $task->setPublished(true);

        $newTask = $taskDao->createTask($task);
        $task_id = $newTask->getId();
        error_log("addChunkTask $chunk_number: $task_id, deadline: " . $task->getDeadline());

        $taskDao->updateRequiredTaskQualificationLevel($task_id, $taskDao->getRequiredTaskQualificationLevel($parent_task->getId()));

        $project_restrictions = $taskDao->get_project_restrictions($project_id);
        if ($project_restrictions && (
                ($newTask->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION  && $project_restrictions['restrict_translate_tasks'])
                    ||
                ($newTask->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $project_restrictions['restrict_revise_tasks']))) {
            $taskDao->setRestrictedTask($task_id);
        }

        // Trigger afterTaskCreate should update UserTrackedTasks based on UserTrackedProjects

        return $task_id;
    }

    public function projectAlter($project_id)
    {
        $matecat_api = Common\Lib\Settings::get('matecat.url');
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = $this->random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $project = $projectDao->getProject($project_id);

        $memsource_project = $projectDao->get_memsource_project($project_id);

        if ($post = $app->request()->post()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey
                    || empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['project_deadline'])
                    || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])) {
                // Note the deadline date validation above is only partial (these checks have been done more rigorously on client size, if that is to be trusted)
                $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_to_create_project'), htmlspecialchars($post['project_title'], ENT_COMPAT, 'UTF-8')));
            } else {
                $sourceLocale = new Common\Protobufs\Models\Locale();

                $project->setTitle($post['project_title']);
                $project->setDescription($post['project_description']);
                $project->setDeadline($post['project_deadline']);
                $project->setImpact($post['project_impact']);
                $project->setReference($post['project_reference']);
                // Done by DAOupdateProjectWordCount(), which only saves it conditionally...
                // $project->setWordCount($post['wordCountInput']);

                $project->clearTag();
                if (!empty($post['tagList'])) {
                    $tagLabels = explode(' ', $post['tagList']);
                    foreach ($tagLabels as $tagLabel) {
                        $tagLabel = trim($tagLabel);
                        if (!empty($tagLabel)) {
                            $tag = new Common\Protobufs\Models\Tag();
                            $tag->setLabel($tagLabel);
                            $project->addTag($tag);
                        }
                    }
                }

                try {
                    error_log('projectAlter: ' . $project->getId());
                    $project = $projectDao->updateProject($project);
                } catch (\Exception $e) {
                    $project = null;
                }
                if (empty($project) || $project->getId() <= 0) {
                    $app->flashNow('error', Lib\Localisation::getTranslation('project_create_title_conflict'));
                } else {
                    if (false) { // Code copied from Project Create
                    } else {
                        if (false) { // Code copied from Project Create
                        } else {
                            $image_failed = false;
                            if (!empty($_FILES['projectImageFile']['name'])) {
                                $projectImageFileName = $_FILES['projectImageFile']['name'];
                                $extensionStartIndex = strrpos($projectImageFileName, '.');
                                // Check that file has an extension
                                if ($extensionStartIndex > 0) {
                                    $extension = substr($projectImageFileName, $extensionStartIndex + 1);
                                    $extension = strtolower($extension);
                                    $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . $extension;

                                    // Check that the file extension is valid for an image
                                    if (!in_array($extension, explode(",", Common\Lib\Settings::get('projectImages.supported_formats')))) {
                                        $image_failed = true;
                                    }
                                } else {
                                    // File has no extension
                                    $image_failed = true;
                                }

                                if ($image_failed || !empty($_FILES['projectImageFile']['error']) || empty($_FILES['projectImageFile']['tmp_name'])
                                        ||(($data = file_get_contents($_FILES['projectImageFile']['tmp_name'])) === false)) {
                                    $image_failed = true;
                                } else {
                                    $imageMaxWidth  = Common\Lib\Settings::get('projectImages.max_width');
                                    $imageMaxHeight = Common\Lib\Settings::get('projectImages.max_height');
                                    list($width, $height) = getimagesize($_FILES['projectImageFile']['tmp_name']);

                                    if (empty($width) || empty($height) || (($width <= $imageMaxWidth) && ($height <= $imageMaxHeight))) {
                                        try {
                                            $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                            $success = true;
                                        } catch (\Exception $e) {
                                            $success = false;
                                        }
                                    } else { // Resize the image
                                        $ratio = min($imageMaxWidth / $width, $imageMaxHeight / $height);
                                        $newWidth  = floor($width * $ratio);
                                        $newHeight = floor($height * $ratio);

                                        $img = '';
                                        if ($extension == 'gif') {
                                            $img = imagecreatefromgif($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } elseif ($extension == 'png') {
                                            $img = imagecreatefrompng($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } else {
                                            $img = imagecreatefromjpeg($_FILES['projectImageFile']['tmp_name']);
                                        }

                                        $tci = imagecreatetruecolor($newWidth, $newHeight);
                                        if (!empty($img) && $tci !== false) {
                                            if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                                                imagejpeg($tci, $_FILES['projectImageFile']['tmp_name'], 100); // Overwrite
                                                // If we did not get this far, give up and use the un-resized image
                                            }
                                        }

                                        $data = file_get_contents($_FILES['projectImageFile']['tmp_name']);
                                        if ($data !== false) {
                                            try {
                                                $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                                $success = true;
                                            } catch (\Exception $e) {
                                                $success = false;
                                            }
                                        } else {
                                            $success = false;
                                        }
                                    }
                                    if (!$success) {
                                        $image_failed = true;
                                    }
                                }
                            }
                            if ($image_failed) {
                                $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_image'), htmlspecialchars($_FILES['projectImageFile']['name'], ENT_COMPAT, 'UTF-8')));
                            } else {
                                // Continue here whether there is, or is not, an image file uploaded as long as there was not an explicit failure

                                if (!empty($post['analyse_url'])) {
                                    $request_for_project = $taskDao->getWordCountRequestForProject($project_id);
                                    if ($request_for_project && empty($request_for_project['matecat_id_project']) && empty($request_for_project['matecat_id_project_pass']) && $request_for_project['state'] == 3) {
                                        $found = preg_match('|^http[s]?' . substr($matecat_api, strpos($matecat_api, ':')) . 'analyze/proj-([0-9]+)/([0-9]+)-([0-9a-z]+)$|', $post['analyse_url'], $matches);
                                        if ($found && $matches[1] == $project_id) {
                                            $matecat_id_project = $matches[2];
                                            $matecat_id_project_pass = $matches[3];
                                            $taskDao->updateWordCountRequestForProjects($project_id, $matecat_id_project, $matecat_id_project_pass, 0, 1);
                                            $app->flash('success', 'Matecat Project ID/Password updated!');
                                        } else {
                                            $app->flash('error', 'URL did not match project and expected pattern!');
                                        }
                                    }
                                }
                                try {
                                     $app->redirect($app->urlFor('project-view', array('project_id' => $project->getId())));
                                } catch (\Exception $e) { // redirect throws \Slim\Exception\Stop
                                }
                            }
                        }
                    }
                }
            }
        }

        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();
        $countryDao = new DAO\CountryDao();
        $countries = $countryDao->getCountries();

        $month_list = array(
            1 => Lib\Localisation::getTranslation('common_january'),
            2 => Lib\Localisation::getTranslation('common_february'),
            3 => Lib\Localisation::getTranslation('common_march'),
            4 => Lib\Localisation::getTranslation('common_april'),
            5 => Lib\Localisation::getTranslation('common_may'),
            6 => Lib\Localisation::getTranslation('common_june'),
            7 => Lib\Localisation::getTranslation('common_july'),
            8 => Lib\Localisation::getTranslation('common_august'),
            9 => Lib\Localisation::getTranslation('common_september'),
            10 => Lib\Localisation::getTranslation('common_october'),
            11 => Lib\Localisation::getTranslation('common_november'),
            12 => Lib\Localisation::getTranslation('common_december'),
        );
        $year_list = array();
        $yeari = (int)date('Y');
        for ($i = 0; $i < 10; $i++) {
            $year_list[$yeari] = $yeari;
            $yeari++;
        }
        $hour_list = array();
        for ($i = 0; $i < 24; $i++) {
            $hour_list[$i] = $i;
        }
        $minute_list = array();
        $minutei = (int)date('Y');
        for ($i = 0; $i < 60; $i++) {
            $minute_list[$i] = $i;
        }

        $project = $projectDao->getProject($project_id);
        $deadline = $project->getDeadline();
        $selected_year   = (int)substr($deadline,  0, 4);
        $selected_month  = (int)substr($deadline,  5, 2);
        $selected_day    = (int)substr($deadline,  8, 2);
        $selected_hour   = (int)substr($deadline, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
        $selected_minute = (int)substr($deadline, 14, 2);
        $deadline_timestamp = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

        $sourceLocale = $project->getSourceLocale();
        $sourceCountrySelectCode  = $sourceLocale->getCountryCode();
        $sourceLanguageSelectCode = $sourceLocale->getLanguageCode();

        $project_tags_list = '';
        try {
            $project_tags = $projectDao->getProjectTags($project_id);
            if (!empty($project_tags)) {
                $separator = '';
                foreach ($project_tags as $project_tag) {
                    $project_tags_list .= $separator . $project_tag->getLabel();
                    $separator = ' ';
                }
            }
        } catch (\Exception $e) {
        }

        $adminDao = new DAO\AdminDao();
        $userIsAdmin = $adminDao->isSiteAdmin($user_id);
        // For some reason the existing Dart code excludes this case...
        //$userIsAdmin = $adminDao->isOrgAdmin($project->getOrganisationId(), $user_id) || $userIsAdmin;
        $enter_analyse_url = 0;
        if ($userIsAdmin) {
            $userIsAdmin = 1; // Just to be sure what will appear in the template and then the JavaScript

            $request_for_project = $taskDao->getWordCountRequestForProject($project_id);
            if ($request_for_project && empty($request_for_project['matecat_id_project']) && empty($request_for_project['matecat_id_project_pass']) && $request_for_project['state'] == 3) {
                $enter_analyse_url = 1;
            }
        } else {
            $userIsAdmin = 0;
        }

        $extraScripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/ProjectAlter1.js\"></script>";

        $app->view()->appendData(array(
            "siteLocation"          => Common\Lib\Settings::get('site.location'),
            "siteAPI"               => Common\Lib\Settings::get('site.api'),
            "maxFileSize"           => Lib\TemplateHelper::maxFileSizeBytes(),
            "imageMaxFileSize"      => Common\Lib\Settings::get('projectImages.max_image_size'),
            "supportedImageFormats" => Common\Lib\Settings::get('projectImages.supported_formats'),
            "project"        => $project,
            "project_tags"   => $project_tags_list,
            "project_id"     => $project_id,
            "org_id"         => $project->getOrganisationId(),
            "user_id"        => $user_id,
            "extra_scripts"  => $extraScripts,
            'deadline_timestamp' => $deadline_timestamp,
            'selected_day'   => $selected_day,
            'month_list'     => $month_list,
            'selected_month' => $selected_month,
            'year_list'      => $year_list,
            'selected_year'  => $selected_year,
            'hour_list'      => $hour_list,
            'selected_hour'  => $selected_hour,
            'minute_list'    => $minute_list,
            'selected_minute'=> $selected_minute,
            'languages'      => $languages,
            'countries'      => $countries,
            'sourceLanguageSelectCode' => $sourceLanguageSelectCode,
            'sourceCountrySelectCode'  => $sourceCountrySelectCode,
            'userIsAdmin'    => $userIsAdmin,
            'enter_analyse_url' => $enter_analyse_url,
            'memsource_project' => $memsource_project,
            'sesskey'        => $sesskey,
        ));

        $app->render("project/project.alter.tpl");
    }

    public function projectCreate($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $subscriptionDao = new DAO\SubscriptionDao();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = $this->random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $create_memsource = 1;//(**)If this org is a memsource one

        if ($post = $app->request()->post()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey
                    || empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['sourceLanguageSelect']) || empty($post['project_deadline'])
                    || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])
                    ) {
                // Note the deadline date validation above is only partial (these checks have been done more rigorously on client size, if that is to be trusted)
                $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_to_create_project'), htmlspecialchars($post['project_title'], ENT_COMPAT, 'UTF-8')));
            } else {
                $sourceLocale = new Common\Protobufs\Models\Locale();
                $project = new Common\Protobufs\Models\Project();

                $project->setTitle($post['project_title']);
                $project->setDescription($post['project_description']);
                $project->setDeadline($post['project_deadline']);
                $project->setImpact($post['project_impact']);
                $project->setReference($post['project_reference']);
                $project->setWordCount(1); // Code in taskInsertAndUpdate() does not support 0, so use 1 as placeholder

                list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_selection_to_language_country($post['sourceLanguageSelect']);
                $sourceLocale->setCountryCode($trommons_source_country_code);
                $sourceLocale->setLanguageCode($trommons_source_language_code);
                $project->setSourceLocale($sourceLocale);

                $project->setOrganisationId($org_id);
                $project->setCreatedTime(gmdate('Y-m-d H:i:s'));

                $project->clearTag();
                if (!empty($post['tagList'])) {
                    $tagLabels = explode(' ', $post['tagList']);
                    foreach ($tagLabels as $tagLabel) {
                        $tagLabel = trim($tagLabel);
                        if (!empty($tagLabel)) {
                            $tag = new Common\Protobufs\Models\Tag();
                            $tag->setLabel($tagLabel);
                            $project->addTag($tag);
                        }
                    }
                }

                try {
                    $project = $projectDao->createProject($project);
                    error_log('Created Project: ' . $post['project_title']);
                } catch (\Exception $e) {
                    $project = null;
                }
                if (empty($project) || $project->getId() <= 0) {
                    $app->flashNow('error', Lib\Localisation::getTranslation('project_create_title_conflict'));
                } else {
                    if (empty($_FILES['projectFile']['name']) || !empty($_FILES['projectFile']['error']) || empty($_FILES['projectFile']['tmp_name'])
                            || (($data = file_get_contents($_FILES['projectFile']['tmp_name'])) === false)) {
                        $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                        error_log('Project Upload Error: ' . $post['project_title']);
                        try {
                            $projectDao->deleteProject($project->getId());
                        } catch (\Exception $e) {
                        }
                    } else {
                        $projectFileName = $_FILES['projectFile']['name'];
                        $extensionStartIndex = strrpos($projectFileName, '.');
                        // Check that file has an extension
                        if ($extensionStartIndex > 0) {
                             $extension = substr($projectFileName, $extensionStartIndex + 1);
                             $extension = strtolower($extension);
                             $projectFileName = substr($projectFileName, 0, $extensionStartIndex + 1) . $extension;
                        }
                        try {
                            $projectDao->saveProjectFile($project, $user_id, $projectFileName, $data);
                            error_log("Project File Saved($user_id): " . $post['project_title']);
                            $success = true;
                            if ($create_memsource) {
                                $memsource_project = $userDao->create_memsource_project($post, $project, $data);
                                if (!$memsource_project) $success = false;
                            } else $memsource_project = 0;
                        } catch (\Exception $e) {
                            error_log("Project File Save Error($user_id): " . $post['project_title']);
                            $success = false;
                        }
                        if (!$success) {
                            $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('common_error_file_stopped_by_extension')));
                            try {
                                $projectDao->deleteProject($project->getId());
                            } catch (\Exception $e) {
                            }
                        } else {
                            $image_failed = false;
                            if (!empty($_FILES['projectImageFile']['name'])) {
                                $projectImageFileName = $_FILES['projectImageFile']['name'];
                                $extensionStartIndex = strrpos($projectImageFileName, '.');
                                // Check that file has an extension
                                if ($extensionStartIndex > 0) {
                                    $extension = substr($projectImageFileName, $extensionStartIndex + 1);
                                    $extension = strtolower($extension);
                                    $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . $extension;

                                    // Check that the file extension is valid for an image
                                    if (!in_array($extension, explode(",", Common\Lib\Settings::get('projectImages.supported_formats')))) {
                                        $image_failed = true;
                                    }
                                } else {
                                    // File has no extension
                                    $image_failed = true;
                                }

                                if ($image_failed || !empty($_FILES['projectImageFile']['error']) || empty($_FILES['projectImageFile']['tmp_name'])
                                        ||(($data = file_get_contents($_FILES['projectImageFile']['tmp_name'])) === false)) {
                                    $image_failed = true;
                                } else {
                                    $imageMaxWidth  = Common\Lib\Settings::get('projectImages.max_width');
                                    $imageMaxHeight = Common\Lib\Settings::get('projectImages.max_height');
                                    list($width, $height) = getimagesize($_FILES['projectImageFile']['tmp_name']);

                                    if (empty($width) || empty($height) || (($width <= $imageMaxWidth) && ($height <= $imageMaxHeight))) {
                                        try {
                                            $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                            $success = true;
                                        } catch (\Exception $e) {
                                            $success = false;
                                        }
                                    } else { // Resize the image
                                        $ratio = min($imageMaxWidth / $width, $imageMaxHeight / $height);
                                        $newWidth  = floor($width * $ratio);
                                        $newHeight = floor($height * $ratio);

                                        $img = '';
                                        if ($extension == 'gif') {
                                            $img = imagecreatefromgif($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } elseif ($extension == 'png') {
                                            $img = imagecreatefrompng($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } else {
                                            $img = imagecreatefromjpeg($_FILES['projectImageFile']['tmp_name']);
                                        }

                                        $tci = imagecreatetruecolor($newWidth, $newHeight);
                                        if (!empty($img) && $tci !== false) {
                                            if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                                                imagejpeg($tci, $_FILES['projectImageFile']['tmp_name'], 100); // Overwrite
                                                // If we did not get this far, give up and use the un-resized image
                                            }
                                        }

                                        $data = file_get_contents($_FILES['projectImageFile']['tmp_name']);
                                        if ($data !== false) {
                                            try {
                                                $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                                $success = true;
                                            } catch (\Exception $e) {
                                                $success = false;
                                            }
                                        } else {
                                            $success = false;
                                        }
                                    }
                                    if (!$success) {
                                        $image_failed = true;
                                    }
                                }
                            }
                            if ($image_failed) {
                                $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_image'), htmlspecialchars($_FILES['projectImageFile']['name'], ENT_COMPAT, 'UTF-8')));
                                try {
                                    $projectDao->deleteProject($project->getId());
                                } catch (\Exception $e) {
                                }
                            } else {
                                // Continue here whether there is, or is not, an image file uploaded as long as there was not an explicit failure

                                // Add Tasks for the new Project
                                $targetCount = 0;
                                $creatingTasksSuccess = true;
                                $createdTasks = array();
                                $matecat_translation_task_ids         = array();
                                $matecat_translation_target_languages = array();
                                $matecat_translation_target_countrys  = array();
                                $matecat_proofreading_task_ids        = array();
                                $matecat_proofreading_target_languages= array();
                                $matecat_proofreading_target_countrys = array();
                                while (!empty($post["target_language_$targetCount"])) {
                                    list($trommons_language_code, $trommons_country_code) = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
                                        if ($create_memsource || !empty($post["translation_$targetCount"])) {
                                            $translation_Task_Id = $this->addProjectTask(
                                                $project,
                                                $memsource_project,
                                                $trommons_language_code,
                                                $trommons_country_code,
                                                Common\Enums\TaskTypeEnum::TRANSLATION,
                                                0,
                                                $createdTasks,
                                                $user_id,
                                                $projectDao,
                                                $taskDao,
                                                $userDao,
                                                $app,
                                                $post);
                                            if (!$translation_Task_Id) {
                                                $creatingTasksSuccess = false;
                                                break;
                                            }
                                            $matecat_translation_task_ids[]         = $translation_Task_Id;
                                            $matecat_translation_target_languages[] = $trommons_language_code;
                                            $matecat_translation_target_countrys[]  = $trommons_country_code;

                                            if ($create_memsource || !empty($post["proofreading_$targetCount"])) {
                                                $id = $this->addProjectTask(
                                                    $project,
                                                    $memsource_project,
                                                    $trommons_language_code,
                                                    $trommons_country_code,
                                                    Common\Enums\TaskTypeEnum::PROOFREADING,
                                                    $translation_Task_Id,
                                                    $createdTasks,
                                                    $user_id,
                                                    $projectDao,
                                                    $taskDao,
                                                    $userDao,
                                                    $app,
                                                    $post);
                                                if (!$id) {
                                                    $creatingTasksSuccess = false;
                                                    break;
                                                }
                                                $matecat_proofreading_task_ids[]         = $id;
                                                $matecat_proofreading_target_languages[] = $trommons_language_code;
                                                $matecat_proofreading_target_countrys[]  = $trommons_country_code;
                                            }
                                        } elseif (!$create_memsource && empty($post["translation_$targetCount"]) && !empty($post["proofreading_$targetCount"])) {
                                            // Only a proofreading task to be created
                                            $id = $this->addProjectTask(
                                                $project,
                                                $memsource_project,
                                                $trommons_language_code,
                                                $trommons_country_code,
                                                Common\Enums\TaskTypeEnum::PROOFREADING,
                                                0,
                                                $createdTasks,
                                                $user_id,
                                                $projectDao,
                                                $taskDao,
                                                $userDao,
                                                $app,
                                                $post);
                                            if (!$id) {
                                                $creatingTasksSuccess = false;
                                                break;
                                            }
                                            $matecat_proofreading_task_ids[]         = $id;
                                            $matecat_proofreading_target_languages[] = $trommons_language_code;
                                            $matecat_proofreading_target_countrys[]  = $trommons_country_code;
                                        }
                                    $targetCount++;
                                }

                                if (!$creatingTasksSuccess) {
                                    foreach ($createdTasks as $taskIdToDelete) {
                                        if ($taskIdToDelete) {
                                            try {
                                                $taskDao->deleteTask($taskIdToDelete);
                                            } catch (\Exception $e) {
                                            }
                                        }
                                    }
                                    $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                                    try {
                                        $projectDao->deleteProject($project->getId());
                                    } catch (\Exception $e) {
                                    }
                                } else {
                                    try {
                                      if (!$memsource_project) {
                                        error_log('projectCreate calculateProjectDeadlines: ' . $project->getId());
                                        $projectDao->calculateProjectDeadlines($project->getId());
                                        $source_language = $trommons_source_language_code . '-' . $trommons_source_country_code;
                                      }
                                        $target_languages = '';
                                        $targetCount = 0;
                                        if (!empty($post["target_language_$targetCount"])) {
                                            list($trommons_language_code, $trommons_country_code) = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
                                            $target_languages = $trommons_language_code . '-' . $trommons_country_code;
                                        }
                                        $targetCount++;
                                        while (!empty($post["target_language_$targetCount"])) {
                                            list($trommons_language_code, $trommons_country_code) = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
                                            $target_languages .= ',' . $trommons_language_code . '-' . $trommons_country_code;
                                            $targetCount++;
                                        }

                                      if (!$memsource_project) {
                                        // $taskDao->insertWordCountRequestForProjects($project->getId(), $source_language, $target_languages, $post['wordCountInput']);
                                        $taskDao->insertWordCountRequestForProjects($project->getId(), $source_language, $target_languages, 0);

                                        $source_language = $this->valid_language_for_matecat($source_language);
                                        if (!empty($source_language) && !empty($matecat_translation_task_ids)) {
                                            $target_list = array();
                                            foreach ($matecat_translation_task_ids as $i => $matecat_translation_task_id) {
                                                $target_language = $this->valid_language_for_matecat($matecat_translation_target_languages[$i] . '-' . $matecat_translation_target_countrys[$i]);
                                                if (!empty($target_language) && ($target_language != $source_language) && !in_array($target_language, $target_list)) {
                                                    $target_list[] = $target_language;
                                                    $taskDao->insertMatecatLanguagePairs($matecat_translation_task_id, $project->getId(), Common\Enums\TaskTypeEnum::TRANSLATION, "$source_language|$target_language");
                                                }
                                            }
                                        }
                                        if (!empty($source_language) && !empty($matecat_proofreading_task_ids)) {
                                            $target_list = array();
                                            foreach ($matecat_proofreading_task_ids as $i => $matecat_proofreading_task_id) {
                                                $target_language = $this->valid_language_for_matecat($matecat_proofreading_target_languages[$i] . '-' . $matecat_proofreading_target_countrys[$i]);
                                                if (!empty($target_language) && ($target_language != $source_language) && !in_array($target_language, $target_list)) {
                                                    $target_list[] = $target_language;
                                                    $taskDao->insertMatecatLanguagePairs($matecat_proofreading_task_id, $project->getId(), Common\Enums\TaskTypeEnum::PROOFREADING, "$source_language|$target_language");
                                                }
                                            }
                                        }

                                        if ($adminDao->isSiteAdmin($user_id)) {
                                            $mt_engine        = empty($post['mt_engine'])        ? '0' : '1';
                                            $pretranslate_100 = empty($post['pretranslate_100']) ? '0' : '1';
                                            $lexiqa           = '1';
                                            if (!empty($post['private_tm_key'])) $post['private_tm_key'] = str_replace(' ', '', $post['private_tm_key']);
                                            $private_tm_key = empty($post['private_tm_key']) ? '58f97b6f65fb5c8c8522' : $post['private_tm_key'] . ',58f97b6f65fb5c8c8522';

                                            if (!empty($post['testing_center'])) {
                                                $mt_engine        = '0';
                                                $pretranslate_100 = '0';
                                                $lexiqa           = '0';
                                                $private_tm_key   = 'new';
                                            }

                                            if (!empty($post['testing_center']) || !empty($post['private_tm_key']) || empty($post['mt_engine']) || empty($post['pretranslate_100'])) {
                                                $taskDao->set_project_tm_key($project->getId(), $mt_engine, $pretranslate_100, $lexiqa, $private_tm_key);
                                            }
                                        }
                                      }

                                        $restrict_translate_tasks = !empty($post['restrict_translate_tasks']);
                                        $restrict_revise_tasks    = !empty($post['restrict_revise_tasks']);
                                        if ($restrict_translate_tasks || $restrict_revise_tasks) $taskDao->insert_project_restrictions($project->getId(), $restrict_translate_tasks, $restrict_revise_tasks);

                                        // Create a topic in the Community forum (Discourse) and a project in Asana
                                        error_log('projectCreate create_discourse_topic(' . $project->getId() . ", $target_languages)");
                                        try {
                                           $this->create_discourse_topic($project->getId(), $target_languages);
                                        } catch (\Exception $e) {
                                            error_log('projectCreate create_discourse_topic Exception: ' . $e->getMessage());
                                        }
                                        try {
                                            $app->redirect($app->urlFor('project-view', array('project_id' => $project->getId())));
                                        } catch (\Exception $e) { // redirect throws \Slim\Exception\Stop
                                        }
                                    } catch (\Exception $e) {
                                        $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                                        try {
                                            error_log('projectCreate deleteProject(' . $project->getId() . ")");
                                            $projectDao->deleteProject($project->getId());
                                        } catch (\Exception $e) {
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $month_list = array(
            1 => Lib\Localisation::getTranslation('common_january'),
            2 => Lib\Localisation::getTranslation('common_february'),
            3 => Lib\Localisation::getTranslation('common_march'),
            4 => Lib\Localisation::getTranslation('common_april'),
            5 => Lib\Localisation::getTranslation('common_may'),
            6 => Lib\Localisation::getTranslation('common_june'),
            7 => Lib\Localisation::getTranslation('common_july'),
            8 => Lib\Localisation::getTranslation('common_august'),
            9 => Lib\Localisation::getTranslation('common_september'),
            10 => Lib\Localisation::getTranslation('common_october'),
            11 => Lib\Localisation::getTranslation('common_november'),
            12 => Lib\Localisation::getTranslation('common_december'),
        );

        $year_list = array();
        $yeari = (int)date('Y');
        for ($i = 0; $i < 10; $i++) {
            $year_list[$yeari] = $yeari;
            $yeari++;
        }
        $hour_list = array();
        for ($i = 0; $i < 24; $i++) {
            $hour_list[$i] = $i;
        }
        $minute_list = array();
        $minutei = (int)date('Y');
        for ($i = 0; $i < 60; $i++) {
            $minute_list[$i] = $i;
        }

        $extraScripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/ProjectCreate7.js\"></script>";

        $app->view()->appendData(array(
            "siteLocation"          => Common\Lib\Settings::get('site.location'),
            "siteAPI"               => Common\Lib\Settings::get('site.api'),
            "maxFileSize"           => Lib\TemplateHelper::maxFileSizeBytes(),
            "imageMaxFileSize"      => Common\Lib\Settings::get('projectImages.max_image_size'),
            "supportedImageFormats" => Common\Lib\Settings::get('projectImages.supported_formats'),
            "org_id"         => $org_id,
            "user_id"        => $user_id,
            'subscription_text' => null,
            "extra_scripts"  => $extraScripts,
            'month_list'     => $month_list,
            'selected_month' => (int)date('n'),
            'year_list'      => $year_list,
            'selected_year'  => (int)date('Y'),
            'hour_list'      => $hour_list,
            'selected_hour'  => 0,
            'minute_list'    => $minute_list,
            'selected_minute'=> 0,
            'create_memsource'=> $create_memsource,
            'languages'      => $projectDao->generate_language_selection($create_memsource),
            'showRestrictTask' => $taskDao->organisationHasQualifiedBadge($org_id),
            'isSiteAdmin'    => $adminDao->isSiteAdmin($user_id),
            'sesskey'        => $sesskey,
            'template1'      => '{"source": "en-GB", "targets": ["zh-CN", "zh-TW", "th-TH", "vi-VN", "id-ID", "tl-PH", "ko-KR", "ja-JP", "ms-MY", "my-MM", "hi-IN", "bn-IN"]}',
            'template2'      => '{"source": "en-GB", "targets": ["ar-SA", "hi-IN", "swh-KE", "fr-FR", "es-49", "pt-BR"]}',
        ));
        $app->render("project/project.create.tpl");
    }

    private function addProjectTask(
        $project,
        $memsource_project,
        $target_language,
        $target_country,
        $taskType,
        $preReqTaskId,
        &$createdTasks,
        $user_id,
        $projectDao,
        $taskDao,
        $userDao,
        $app,
        $post)
    {
        $taskPreReqs = array();
        $task = new Common\Protobufs\Models\Task();
        $task->setProjectId($project->getId());

        $task->setTitle($project->getTitle());

        $projectSourceLocale = $project->getSourceLocale();
        $taskSourceLocale = new Common\Protobufs\Models\Locale();
        $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
        $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
        $task->setSourceLocale($taskSourceLocale);
        $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);

        $taskTargetLocale = new Common\Protobufs\Models\Locale();
        $taskTargetLocale->setLanguageCode($target_language);
        $taskTargetLocale->setCountryCode($target_country);
        $task->setTargetLocale($taskTargetLocale);

        $task->setTaskType($taskType);
        $task->setWordCount($project->getWordCount());
        if ($memsource_project) {
            $deadline = strtotime($project->getDeadline());
            $deadline_less_7_days = $deadline - 7*24*60*60; // 7-4 days Translation
            $deadline_less_4_days = $deadline - 4*24*60*60; // 4-1 days Revising
            $deadline_less_1_days = $deadline - 1*24*60*60; // 1 day for pm
            $now = time();
            if ($deadline_less_7_days < $now) { // We are squashed for time
                $total = $deadline - $now;
                if ($total < 0) $total = 0;
                $deadline_less_4_days = $deadline - $total*4/7;
                $deadline_less_1_days = $deadline - $total*1/7;
            }
            if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION) $task->setDeadline(gmdate('Y-m-d H:i:s', $deadline_less_4_days));
            else                                                     $task->setDeadline(gmdate('Y-m-d H:i:s', $deadline_less_1_days));
        } else                                                       $task->setDeadline($project->getDeadline());

        if (!empty($post['publish'])) {
            $task->setPublished(1);
        } else {
            $task->setPublished(0);
        }

        if (!empty($post['testing_center']) && $taskType == Common\Enums\TaskTypeEnum::TRANSLATION) {
            $task->setPublished(0);
        }

        try {
            error_log("addProjectTask");
            $newTask = $taskDao->createTask($task);
            $newTaskId = $newTask->getId();

            if (!empty($post['testing_center']) && $taskType == Common\Enums\TaskTypeEnum::PROOFREADING) {
                $taskDao->updateRequiredTaskQualificationLevel($newTaskId, 3); // Reviser Needs to be Senior
            }

            $createdTasks[] = $newTaskId;

            $upload_error = $taskDao->saveTaskFileFromProject(
                $newTaskId,
                $user_id,
                $projectDao->getProjectFile($project->getId())
            );

            if ($memsource_project) {
                if ($preReqTaskId) {
                    $task->setTaskStatus(Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                }
                $memsource_target = $projectDao->convert_language_country_to_memsource($target_language, $target_country);
                if (!$memsource_target) return 0;

                if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION) {
                    $type_text = 'Translation';
                    $default_workflow = 1;
                } else {
                    $type_text = 'Revision';
                    $default_workflow = 2;
                }
                $levels = [$memsource_project['workflow_level_1'] => 1, $memsource_project['workflow_level_2'] => 2, $memsource_project['workflow_level_3'] => 3];
                if (!empty($levels[$type_text])) $workflow = $levels[$type_text];
                else                             $workflow = $default_workflow;
if (!empty($levels[$type_text])) error_log("levels[$type_text]: {$levels[$type_text]}");//(**)test code

error_log("$memsource_target-$workflow");//(**)
error_log(print_r($memsource_project['jobs'], true))//(**)
                if (empty($memsource_project['jobs']["$memsource_target-$workflow"])) return 0;
                $job = $memsource_project['jobs']["$memsource_target-$workflow"];
error_log(print_r($job, true))//(**)
                //(**) verify below also error checking needed?
                $projectDao->set_memsource_task($newTaskId, !empty($job['id']) ? $job['id'] : 0, $job['uid'], $job['task'],
                    empty($job['internalId'])    ? 0 : $job['internalId'],
                    empty($job['workflowLevel']) ? 0 : $job['workflowLevel'],
                    empty($job['beginIndex'])    ? 0 : $job['beginIndex'], // Begin Segment number
                    empty($job['endIndex'])      ? 0 : $job['endIndex'],
                    $preReqTaskId);
            } else {
                if ($newTaskId && $preReqTaskId) {
                    $taskDao->addTaskPreReq($newTaskId, $preReqTaskId);
                }
            }

            if (!empty($post['trackProject'])) {
                $userDao->trackTask($user_id, $newTaskId);
            }

            if (!empty($post['restrict_translate_tasks']) && $newTask->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION) {
                $taskDao->setRestrictedTask($newTaskId);
            }
            if (!empty($post['restrict_revise_tasks'])    && $newTask->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
                $taskDao->setRestrictedTask($newTaskId);
            }
        } catch (\Exception $e) {
            return 0;
        }

        error_log("Added Task: $newTaskId");
        return $newTaskId;
    }

    /**
     * Generate and return a random string of the specified length.
     *
     * @param int $length The length of the string to be created.
     * @return string
     */
    private function random_string($length=15) {
        $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        $poollen = strlen($pool);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($pool, (mt_rand()%($poollen)), 1);
        }
        return $string;
    }

    public function projectCreated($project_id)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();
        $project = $projectDao->getProject($project_id);
        $org_id = $project->getOrganisationId();

        $app->view()->appendData(array(
                "org_id" => $org_id,
                "project_id" => $project_id
        ));

        $app->render("project/project.created.tpl");
    }

    public function archiveProject($project_id, $sesskey)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();

        Common\Lib\UserSession::checkCSRFKey($sesskey, 'archiveProject');

        $project = $projectDao->getProject($project_id);
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $archivedProject = $projectDao->archiveProject($project_id, $user_id);

        if ($archivedProject) {
            $app->flash(
                "success",
                sprintf(Lib\Localisation::getTranslation('org_dashboard_9'), $project->getTitle())
            );
        } else {
            $app->flash(
                "error",
                sprintf(Lib\Localisation::getTranslation('org_dashboard_10'), $project->getTitle())
            );
        }

        $app->redirect($ref = $app->request()->getReferrer());
    }

    public function downloadProjectFile($projectId)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        if ($taskDao->isUserRestrictedFromProject($projectId, Common\Lib\UserSession::getCurrentUserID())) {
            $app->flash('error', 'You cannot access this project!');
            $app->redirect($app->urlFor('home'));
        }

        $project_tasks = $projectDao->get_tasks_for_project($projectId); // Is a memsource project if any memsource jobs
        try {
            if ($project_tasks) {
                $task_id = 0;
                $unitary = 1;
                foreach ($project_tasks as $project_task) {
                    $title = $project_task['title'];
                    if (strpos($project_task['internalId'], '.')) $title = substr($project_task['title'], strlen($project_task['internalId']) + 1);
                    if (!$task_id) { // First time through
                        $task_id = $project_task['id'];
                        $title_0 = $title;
                    }
                    if ($title_0 !== $title) $unitary = 0; // Not a unitary project with a single source file (1 => probably it is)
                }
                if (!$unitary) {
                    $app->flash('error', 'We could not find a matching file.');
                    $app->redirect($app->urlFor('home'));
                }
                $headArr = $taskDao->downloadTaskVersion($task_id, 0, 0); // Download an original Task file as "Project" file
            } else {
            $headArr = $projectDao->downloadProjectFile($projectId);
            }
            //Convert header data to array and set headers appropriately
            if (!empty($headArr)) {
                $headArr = unserialize($headArr);
                foreach ($headArr as $key => $val) {
                    $app->response->headers->set($key, $val);
                }
            }
        } catch (Common\Exceptions\SolasMatchException $e) {
            $app->flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_original_project_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            $app->redirect($app->urlFor('home'));
        }
    }

    public function downloadProjectImageFile($projectId)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();

        try {
            $headArr = $projectDao->downloadProjectImageFile($projectId);
            //Convert header data to array and set headers appropriately
            if (!empty($headArr)) {
                $headArr = unserialize($headArr);
                foreach ($headArr as $key => $val) {
                    $app->response->headers->set($key, $val);
                }
            }
        } catch (Common\Exceptions\SolasMatchException $e) {
            $app->flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_project_image_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            $app->redirect($app->urlFor('home'));
        }
    }

    public function create_discourse_topic($projectId, $targetlanguages, $memsource_project = 0)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $project = $projectDao->getProject($projectId);
        $org_id = $project->getOrganisationId();
        $orgDao = new DAO\OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        $org_name = $org->getName();

        $langDao = new DAO\LanguageDao();
        $langcodearray = explode(',', $targetlanguages);
        $i = 0;
        $languages = array();
        foreach($langcodearray as $langcode){
          if (!empty($langcode)) {
            $langcode = substr($langcode,0,strpos($langcode."-","-"));
            $language = $langDao->getLanguageByCode($langcode);
            $languages[$i++] = $language->getName();
          }
        }

        $creator = $taskDao->get_creator($projectId, $memsource_project);
        $pm = $creator['email'];
        if (strpos($pm, '@translatorswithoutborders.org') === false) $pm = 'projects@translatorswithoutborders.org';

        $discourseapiparams = array(
            'category' => '7',
            'title' => str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getTitle()),
            'raw' => "Partner: $org_name. Project Manager: $pm URL: /"."/".$_SERVER['SERVER_NAME']."/project/$projectId/view ".str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getDescription()),
        );
        $fields = '';
        foreach($discourseapiparams as $name => $value){
            $fields .= urlencode($name).'='.urlencode($value).'&';
        }
        $fields .= 'tags[]=' . urlencode($org_name);
        $language_count = 0;
        foreach($languages as $language){
            // We cannot pass the post fields as array because multiple languages mean duplicate tags[] keys
            $fields .= '&tags[]=' . urlencode($language);
            if (++$language_count == 4) break; // Limit in Discourse on number of tags?
        }
error_log("fields: $fields targetlanguages: $targetlanguages");//(**)

        $re = curl_init(Common\Lib\Settings::get('discourse.url').'/posts');
        curl_setopt($re, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($re, CURLOPT_HTTPHEADER, ['Api-Key: ' . Common\Lib\Settings::get('discourse.api_key'), 'Api-Username: ' . Common\Lib\Settings::get('discourse.api_username')]);

        $res = curl_exec($re);
        if ($error_number = curl_errno($re)) {
          error_log("Discourse API error ($error_number): " . curl_error($re));
        } else {
            $response_data = json_decode($res, true);
            if (!empty($response_data['topic_id'])) {
                $topic_id = $response_data['topic_id'];
                $projectDao->set_discourse_id($projectId, $topic_id);
            } else {
                error_log('Discourse API error: No topic_id returned');
                error_log($res);
            }
        }
        curl_close($re);

        //Asana
        $re = curl_init('https://app.asana.com/api/1.0/tasks');
        curl_setopt($re, CURLOPT_POSTFIELDS, array(
            'name' => str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getTitle()),
            'notes' => "Partner: $org_name, Target: $targetlanguages, Deadline: ".$project->getDeadline() . ' https:/'.'/'.$_SERVER['SERVER_NAME']."/project/$projectId/view",
            'projects' => Common\Lib\Settings::get('asana.project')
            )
        );

        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($re, CURLOPT_HEADER, true);
        curl_setopt($re, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . Common\Lib\Settings::get('asana.api_key')));
        curl_exec($re);
        if ($error_number = curl_errno($re)) {
          error_log("Asana API error ($error_number): " . curl_error($re));
        }
        curl_close($re);

        // Asana 2nd Project
        $re = curl_init('https://app.asana.com/api/1.0/tasks');
        curl_setopt($re, CURLOPT_POSTFIELDS, array(
            'name' => str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getTitle()),
            'notes' => "Partner: $org_name, Target: $targetlanguages, Deadline: ".$project->getDeadline() . ' https:/'.'/'.$_SERVER['SERVER_NAME']."/project/$projectId/view",
            'projects' => '1169104501864281'
            )
        );

        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($re, CURLOPT_HEADER, true);
        curl_setopt($re, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . Common\Lib\Settings::get('asana.api_key2')));
        curl_exec($re);
        if ($error_number = curl_errno($re)) {
          error_log("Asana 2 API error ($error_number): " . curl_error($re));
        }
        curl_close($re);

        // Asana 3rd Project
        $re = curl_init('https://app.asana.com/api/1.0/tasks');
        curl_setopt($re, CURLOPT_POSTFIELDS, array(
            'name' => str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getTitle()),
            'notes' => "Partner: $org_name, Target: $targetlanguages, Deadline: ".$project->getDeadline() . ' https:/'.'/'.$_SERVER['SERVER_NAME']."/project/$projectId/view",
            'projects' => '1174689961513340'
            )
        );

        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($re, CURLOPT_HEADER, true);
        curl_setopt($re, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . Common\Lib\Settings::get('asana.api_key3')));
        curl_exec($re);
        if ($error_number = curl_errno($re)) {
          error_log("Asana 3 API error ($error_number): " . curl_error($re));
        }
        curl_close($re);

        // Asana 5th Project
        $re = curl_init('https://app.asana.com/api/1.0/tasks');
        curl_setopt($re, CURLOPT_POSTFIELDS, array(
            'name' => str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getTitle()),
            'notes' => "Partner: $org_name, Target: $targetlanguages, Deadline: ".$project->getDeadline() . ' https:/'.'/'.$_SERVER['SERVER_NAME']."/project/$projectId/view",
            'projects' => '1186619555316417'
            )
        );

        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($re, CURLOPT_HEADER, true);
        curl_setopt($re, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . Common\Lib\Settings::get('asana.api_key5')));
        curl_exec($re);
        if ($error_number = curl_errno($re)) {
          error_log("Asana 5 API error ($error_number): " . curl_error($re));
        }
        curl_close($re);
    }

    public function project_cron_1_minute()
    {
      $matecat_api = Common\Lib\Settings::get('matecat.url');

      $fp_for_lock = fopen(__DIR__ . '/project_cron_1_minute_lock.txt', 'r');
      if (flock($fp_for_lock, LOCK_EX | LOCK_NB)) { // Acquire an exclusive lock, if possible, if not we will wait for next time

        $taskDao = new DAO\TaskDao();

        // status 1 => Uploaded to MateCat [This call will happen one minute after getWordCountRequestForProjects(0)]
        $projects = $taskDao->getWordCountRequestForProjects(1);
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $project_id = $project['project_id'];
                $matecat_id_project = $project['matecat_id_project'];
                $matecat_id_project_pass = $project['matecat_id_project_pass'];

                // https://www.matecat.com/api/docs#!/Project/get_status (i.e. Word Count)
                // $re = curl_init("https://www.matecat.com/api/status?id_project=$matecat_id_project&project_pass=$matecat_id_project_pass");
                $re = curl_init("{$matecat_api}api/status?id_project=$matecat_id_project&project_pass=$matecat_id_project_pass");

                // http://php.net/manual/en/function.curl-setopt.php
                curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($re, CURLOPT_COOKIESESSION, true);
                curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($re, CURLOPT_AUTOREFERER, true);

                $httpHeaders = array(
                    'Expect:'
                );
                curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

                curl_setopt($re, CURLOPT_HEADER, true);
                curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($re, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock

                $res = curl_exec($re);
                if ($error_number = curl_errno($re)) {
                    error_log("project_cron /status ($project_id) Curl error ($error_number): " . curl_error($re)); // $responseCode will be 0, so error will be caught below
                }

                $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
                $header = substr($res, 0, $header_size);
                $res = substr($res, $header_size);
                $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                curl_close($re);

                $word_count = 0;
                if ($responseCode == 200) {
                    $response_data = json_decode($res, true);

                    if ($response_data['status'] === 'DONE') {
                        if (empty($response_data['errors'])) {
                            if (!empty($response_data['data']['summary']['TOTAL_RAW_WC'])) {
                                $word_count = $response_data['data']['summary']['TOTAL_RAW_WC'];

                                if (!empty($response_data['jobs']['langpairs'])) {
                                    $langpairs = count(array_unique($response_data['jobs']['langpairs']));

                                    foreach ($response_data['jobs']['langpairs'] as $job_password => $langpair) {
                                        $matecat_id_job          = substr($job_password, 0, strpos($job_password, '-'));
                                        $matecat_id_job_password = substr($job_password, strpos($job_password, '-') + 1);
                                        $matecat_id_file         = 0;
                                        if (!empty($response_data['data']['jobs'][$matecat_id_job]['chunks'][$matecat_id_job_password])) {
                                            foreach ($response_data['data']['jobs'][$matecat_id_job]['chunks'][$matecat_id_job_password] as $i => $filename_array) {
                                                $matecat_id_file = $i;
                                                break; // Should only be one... actually now not used and could be more than one for ZIP file
                                            }
                                        } else {
                                            error_log("project_cron /status ($project_id) ['data']['jobs'][$matecat_id_job]['chunks'][$matecat_id_job_password] empty!");
                                        }

                                        if (!empty($matecat_id_job) && !empty($matecat_id_job_password) && !empty($matecat_id_file)) {
                                            // Set matecat_id_job, matecat_id_job_password, matecat_id_file
                                            // Note: SQL will not update if we were forced to use fake language pairs for word count purposes as they will not match
                                            $taskDao->updateMatecatLanguagePairs($project_id, Common\Enums\TaskTypeEnum::TRANSLATION, $langpair, $matecat_id_job, $matecat_id_job_password, $matecat_id_file);
                                            $taskDao->updateMatecatLanguagePairs($project_id, Common\Enums\TaskTypeEnum::PROOFREADING, $langpair, $matecat_id_job, $matecat_id_job_password, $matecat_id_file);
                                        } else {
                                            error_log("project_cron /status ($project_id) matecat_id_job($matecat_id_job), matecat_id_job_password($matecat_id_job_password) or matecat_id_file($matecat_id_file) empty!");
                                        }
                                    }

                                    $word_count = $word_count / $langpairs;

                                    if (!empty($word_count)) {
                                        // Set word count for the Project and its Tasks
                                        $taskDao->updateWordCountForProject($project_id, $word_count);

                                        // Change status to Complete (2)
                                        $taskDao->updateWordCountRequestForProjects($project_id, $matecat_id_project, $matecat_id_project_pass, $word_count, 2);
                                    } else {
                                        error_log("project_cron /status ($project_id) calculated wordcount empty!");
                                    }
                                } else {
                                    error_log("project_cron /status ($project_id) langpairs empty!");
                                }
                            } else {
                                error_log("project_cron /status ($project_id) TOTAL_RAW_WC empty!");
                            }
                        } else {
                            foreach ($response_data['errors'] as $error) {
                                error_log("project_cron /status ($project_id) error: " . $error);
                            }
                        }
                    } else {
                        error_log("project_cron /status ($project_id) status NOT DONE: " . $response_data['status']);
                        if ($response_data['status'] === 'NO_SEGMENTS_FOUND') {
                            // Change status to Complete (3), Give up!
                            $taskDao->updateWordCountRequestForProjects($project_id, $matecat_id_project, $matecat_id_project_pass, 0, 3);
                            $taskDao->insertWordCountRequestForProjectsErrors($project_id, $response_data['status'], empty($response_data['message']) ? '' : $response_data['message']);
                        }
                    }
                } else {
                    error_log("project_cron /status ($project_id) responseCode: $responseCode");
                }
                // Note, we will retry if there was an error and hope it is temporary and/or the analysis is not complete yet
            }
        }

        // status 0 => Waiting for Upload to MateCat
        $projects = $taskDao->getWordCountRequestForProjects(0);
        if (!empty($projects)) {
            $count = 0;
            foreach ($projects as $project) {
                if (++$count > 1) break; // Limit number done at one time, just in case

                $project_id = $project['project_id'];

                $project_file = $taskDao->getProjectFileLocation($project_id);
                if (!empty($project_file)) {
                    $filename = $project_file['filename'];
                    //$file = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/$filename";
                    $file = $taskDao->getPhysicalProjectFilePath($project_id, $filename);
                    if (!$file) {
                        error_log("project_cron ($project_id) getPhysicalProjectFilePath FAILED");
                        continue;
                    }
                } else {
                    error_log("project_cron ($project_id) getProjectFileLocation FAILED");
                    continue;
                }

                $creator = $taskDao->get_creator($project_id);

                $source_language = $project['source_language'];
                $source_language = $this->valid_language_for_matecat($source_language);
                if (empty($source_language)) $source_language = 'en-US';

                // https://www.matecat.com/api/docs#!/Project/post_new
                // $re = curl_init('https://www.matecat.com/api/new'); ... api/v1/new 20191029
                $re = curl_init("{$matecat_api}api/v1/new");

                // http://php.net/manual/en/function.curl-setopt.php
                curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($re, CURLOPT_COOKIESESSION, true);
                curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($re, CURLOPT_AUTOREFERER, true);

                $httpHeaders = array(
                    'Expect:'
                );
                curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

                // http://php.net/manual/en/class.curlfile.php
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file);
                finfo_close($finfo);
                $cfile = new \CURLFile($file, $mime, $filename);

                $target_languages = explode(',', $project['target_languages']);
                $filtered_target_languages = array();
                foreach ($target_languages as $target_language) {
                    $target_language = $this->valid_language_for_matecat($target_language);
                    if (!empty($target_language) && ($target_language != $source_language)) {
                        $filtered_target_languages[$target_language] = $target_language;
                    }
                }
                if (!empty($filtered_target_languages)) {
                    $filtered_target_languages = implode(',', $filtered_target_languages);
                } else {
                    if ($source_language != 'es-ES') {
                        $filtered_target_languages = 'es-ES';
                    } else {
                        $filtered_target_languages = 'en-US';
                    }
                }

                $private_tm_key = $taskDao->get_project_tm_key($project_id);
                if (empty($private_tm_key)) {
                    $mt_engine        = '1';
                    $pretranslate_100 = '1';
                    $lexiqa           = '1';
                    $private_tm_key   = '58f97b6f65fb5c8c8522';
                } else {
                    $mt_engine        = $private_tm_key[0]['mt_engine'];
                    $pretranslate_100 = $private_tm_key[0]['pretranslate_100'];
                    $lexiqa           = $private_tm_key[0]['lexiqa'];
                    $private_tm_key   = $private_tm_key[0]['private_tm_key'];
                }
                $fields = array(
                  'file'         => $cfile,
                  'project_name' => "proj-$project_id",
                  'source_lang'  => $source_language,
                  'target_lang'  => $filtered_target_languages,
                  'tms_engine'   => '1',
                  'mt_engine'        => $mt_engine,
                  'private_tm_key'   => $private_tm_key,
                  'pretranslate_100' => $pretranslate_100,
                  'lexiqa'           => $lexiqa,
                  'subject'      => 'general',
                  'owner_email'  => $creator['email']
                );
                if ($private_tm_key === 'new') { // Testing Center Project
                    $fields['tms_engine']         = '0';
                    $fields['get_public_matches'] = '0';
                }
                error_log("project_cron /new ($project_id) fields: " . print_r($fields, true));
                curl_setopt($re, CURLOPT_POSTFIELDS, $fields);

                curl_setopt($re, CURLOPT_HEADER, true);
                curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($re, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock

                $res = curl_exec($re);
                if ($error_number = curl_errno($re)) {
                    error_log("project_cron /new ($project_id) Curl error ($error_number): " . curl_error($re)); // $responseCode will be 0, so error will be caught below
                }

                $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
                $header = substr($res, 0, $header_size);
                $res = substr($res, $header_size);
                $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                curl_close($re);

                if ($responseCode == 200) {
                    $response_data = json_decode($res, true);

                    if ($response_data['status'] !== 'OK') {
                        error_log("project_cron /new ($project_id) status NOT OK: " . $response_data['status']);
                        error_log("project_cron /new ($project_id) status message: " . $response_data['message']);
                        // Change status to Complete (3), if there was an error!
                        $taskDao->updateWordCountRequestForProjects($project_id, 0, 0, 0, 3);
                        $taskDao->insertWordCountRequestForProjectsErrors($project_id, $response_data['status'], $response_data['message']);
                    }
                    elseif (empty($response_data['id_project']) || empty($response_data['project_pass'])) {
                        error_log("project_cron /new ($project_id) id_project or project_pass empty!");
                        // Change status to Complete (3), if there was an error!
                        $taskDao->updateWordCountRequestForProjects($project_id, 0, 0, 0, 3);
                        $taskDao->insertWordCountRequestForProjectsErrors($project_id, $response_data['status'], 'id_project or project_pass empty');
                    } else {
                        $matecat_id_project      = $response_data['id_project'];
                        $matecat_id_project_pass = $response_data['project_pass'];

                        // Change status to Uploaded (1), 0 is still placeholder for new word count
                        $taskDao->updateWordCountRequestForProjects($project_id, $matecat_id_project, $matecat_id_project_pass, 0, 1);
                    }
                } else {
                    // If this was a comms error, we will retry (as status is still 0)
                    error_log("project_cron /new ($project_id) responseCode: $responseCode");
                }
            }
        }

        // See if any chunks have been finalised in MateCat, if so mark any corresponding IN_PROGRESS (active) task(s) as complete
        // $active_tasks_for_chunks = $taskDao->all_chunked_active_projects();
        $active_tasks_for_chunks = array(); // It is now desired to have tranlators manually mark as COMPLETE
        if (!empty($active_tasks_for_chunks)) {
            $projects = array();
            foreach ($active_tasks_for_chunks as $active_task) {
                $projects[$active_task['project_id']] = $active_task['project_id'];
            }

          if (count($projects) > 10) $projects = array_rand($projects, 10); // Pick random Projects, we don't want to do too many at once.
          foreach ($projects as $project_id) {
            $chunks = $taskDao->getStatusOfSubChunks($project_id);

            foreach ($active_tasks_for_chunks as $active_task) {
                foreach ($chunks as $chunk) {
                    if ($active_task['matecat_id_job'] == $chunk['matecat_id_job'] && $active_task['matecat_id_chunk_password'] == $chunk['matecat_id_chunk_password']) {
                        if (($active_task['type_id'] == Common\Enums\TaskTypeEnum::TRANSLATION  && ($chunk['DOWNLOAD_STATUS'] === 'translated' || $chunk['DOWNLOAD_STATUS'] === 'approved')) ||
                            ($active_task['type_id'] == Common\Enums\TaskTypeEnum::PROOFREADING &&                                                $chunk['DOWNLOAD_STATUS'] === 'approved')) {

                            error_log('Setting Task COMPLETE for: ' . $active_task['task_id']);
                            $taskDao->setTaskStatus($active_task['task_id'], Common\Enums\TaskStatusEnum::COMPLETE);
                            $taskDao->sendTaskUploadNotifications($active_task['task_id'], 1);
                            // LibAPI\Notify::sendTaskUploadNotifications($active_task['task_id'], 1);
                        }
                    }
                }
            }
          }
        }

        flock($fp_for_lock, LOCK_UN); // Release the lock
      }
      fclose($fp_for_lock);

        //$app = \Slim\Slim::getInstance();
        //$app->view()->appendData(array(
        //    'body' => 'Dummy',
        //));
        //$app->render('nothing.tpl');
      die;
    }

    public function task_cron_1_minute()
    {
        $projectDao = new DAO\ProjectDao();

        $fp_for_lock = fopen(__DIR__ . '/task_cron_1_minute_lock.txt', 'r');
        if (flock($fp_for_lock, LOCK_EX | LOCK_NB)) { // Acquire an exclusive lock, if possible, if not we will wait for next time
            $queue_copy_task_original_files = $projectDao->get_queue_copy_task_original_files();
            $count = 0;
            foreach ($queue_copy_task_original_files as $queue_copy_task_original_file) {
                if (++$count > 1) break; // Limit number done at one time, just in case

                $project_id         = $queue_copy_task_original_file['project_id'];
                $task_id            = $queue_copy_task_original_file['task_id'];
                $memsource_task_uid = $queue_copy_task_original_file['memsource_task_uid'];
                $filename           = $queue_copy_task_original_file['filename'];

                $memsource_project = $projectDao->get_memsource_project($project_id);
                $memsource_project_uid = $memsource_project['memsource_project_uid'];
                $created_by_id         = $memsource_project['created_by_id'];
                $user_id = $projectDao->get_user_id_from_memsource_user($created_by_id);
//(**)                if (!$user_id) $user_id = 62927; // translators@translatorswithoutborders.org
                if (!$user_id) $user_id = 3297;

                $memsourceApiV1 = Common\Lib\Settings::get("memsource.api_url_v1");                    
                $memsourceApiToken = Common\Lib\Settings::get("memsource.memsource_api_token");
                $url = "{$memsourceApiV1}projects/$memsource_project_uid/jobs/$memsource_task_uid/original";

                $re = curl_init($url);
                $httpHeaders = ["Authorization: Bearer $memsourceApiToken"];
                curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);
                curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($re, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock

                $res = curl_exec($re);
                if ($error_number = curl_errno($re)) {
                    error_log("task_cron ($task_id) Curl error ($error_number): " . curl_error($re)); 
                }

                $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                curl_close($re);

                if ($responseCode == 200) {
                    if (strlen($res) <= 100000000) {
                        $projectDao->save_task_file($user_id, $project_id, $task_id, $filename, $res);
                    } else {
                        error_log('File too big: ' . strlen($res));
                    }
                    error_log("dequeue_copy_task_original_file() task_id: $task_id Removing");
                    $projectDao->dequeue_copy_task_original_file($task_id);
                } else {
                    if (in_array($responseCode,[400,401,403,404,405,410,415,501,202])) {
                        $projectDao->dequeue_copy_task_original_file($task_id);
                    }
                    error_log("task_cron ERROR ($task_id) responseCode: $responseCode");
                }
            }

            flock($fp_for_lock, LOCK_UN); // Release the lock
        }
        fclose($fp_for_lock);

        die;
    }

    public function valid_language_for_matecat($language_code)
    {
        global $matecat_acceptable_languages;
        if (in_array($language_code, $matecat_acceptable_languages)) return $language_code;
        // Special case...
        if ($language_code === 'tn-BW') return 'tsn-BW';
        if ($language_code === 'ca---') return 'cav-ES';
        if ($language_code === 'es-49') return 'es-MX';
        if ($language_code === 'sr-90') return 'sr-ME';
        if ($language_code === 'shu-90') return 'shu-NG';

        if (!empty($matecat_acceptable_languages[substr($language_code, 0, strpos($language_code, '-'))])) return $matecat_acceptable_languages[substr($language_code, 0, strpos($language_code, '-'))];
        return '';
    }

    public function project_get_wordcount($project_id)
    {
        $projectDao = new DAO\ProjectDao();
        $project = $projectDao->getProject($project_id);

        if (!empty($project)) {
        $this->project_cron_1_minute(); // Trigger update

        $word_count = $project->getWordCount();
        }
        if (empty($word_count) || $word_count == 1) $word_count = '-';

        \Slim\Slim::getInstance()->response()->body($word_count);
    }
}

$route_handler = new ProjectRouteHandler();
$route_handler->init();
unset ($route_handler);
