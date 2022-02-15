<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use SolasMatch\Common\Lib\APIHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__.'/../../api/lib/IO.class.php';
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

class TaskRouteHandler
{
    public function init()
    {
        global $app;

        $app->get(
            '/tasks/archive/p/{page_no}[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:archivedTasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('archived-tasks');

        $app->map(['GET', 'POST'],
            '/user/{user_id}/claimed/tasks[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:claimedTasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('claimed-tasks');
        
        $app->get(
            '/user/{user_id}/recent/tasks/paged/{page_no}[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:recentTasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('recent-tasks-paged');
        
        $app->get(
            '/user/{user_id}/recent/tasks',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:recentTasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('recent-tasks');
        
        $app->get(
            '/user/{user_id}/claimed/tasks/paged/{page_no}/tt/{tt}/ts/{ts}/o/{o}[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:claimedTasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('claimed-tasks-paged');

        $app->get(
            '/task/{task_id}/download-task-latest-file[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:downloadTaskLatestVersion')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForTaskDownload')
            ->setName('download-task-latest-version');

        $app->get(
            '/task/{task_id}/mark-archived[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:archiveTask')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('archive-task');

        $app->get(
            '/task/{task_id}/download-file-user[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:downloadTask')
            ->setName('download-task');

        $app->get(
            '/task/{task_id}/download-task-external[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:downloadTaskExternal')
            ->setName('download-task-external');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/claim[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskClaim')
            ->add('\SolasMatch\UI\Lib\Middleware:isBlackListed')
            ->setName('task-claim-page');

        $app->get(
            '/task/{task_id}/claimed[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskClaimed')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-claimed');

        $app->get(
            '/task/{task_id}/download-file/v/{version}[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:downloadTaskVersion')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForTaskDownload')
            ->setName('download-task-version');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/id[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('task');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/simple-upload[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskSimpleUpload')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-simple-upload');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/chunk-complete[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskChunkComplete')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-chunk-complete');

        $app->get(
            '/task/{task_id}/uploaded[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskUploaded')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-uploaded');

        $app->get(
            '/task/{task_id}/chunk-completed[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskChunkCompleted')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-chunk-completed');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/alter[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskAlter')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-alter');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/view[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskView')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('task-view');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/search_translators[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_search_translators')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('task-search_translators');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/task_invites_sent/{sesskey}[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_invites_sent')
            ->add('\SolasMatch\UI\Lib\Middleware:authIsSiteAdmin')
            ->setName('task-invites_sent');

        $app->map(['GET', 'POST'],
            '/project/{project_id}/create-task[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskCreate')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgProject')
            ->setName('task-create');

        $app->get(
            '/task/{task_id}/created[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskCreated')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-created');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/org-feedback[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskOrgFeedback')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-org-feedback');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/user-feedback[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskUserFeedback')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-user-feedback');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/review[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskReview')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-review');
    }

    public function archivedTasks(Request $request, Response $response, $args)
    {
        global $template_data;
        $page_no = $args['page_no'];

        $userDao = new DAO\UserDao();
        $userId = Common\Lib\UserSession::getCurrentUserID();

        $user = $userDao->getUser($userId);
        $tasksPerPage = 10;
        $archivedTasksCount = $userDao->getUserArchivedTasksCount($userId);

        $offset = $tasksPerPage * ($page_no - 1) ;
        $archivedTasks = $userDao->getUserArchivedTasks($userId, $offset, $tasksPerPage);
        $totalPages = ceil($archivedTasksCount / $tasksPerPage);

        if ($page_no < 1) {
            $page_no = 1;
        } elseif ($page_no > $totalPages) {
            header('HTTP/1.0 404 Not Found');
        }

        $top = 0;
        //If tasksPerPage divides into the task count with a remainder then last page will have
        //less than $tasksPerPage tasks.
        $modulus = $archivedTasksCount % $tasksPerPage;
        if ($modulus > 0 && $page_no == $totalPages) {
            $bottom = $modulus - 1;
        } else {
            $bottom = $top + $tasksPerPage - 1;
        }

        if ($bottom < 0) {
            $bottom = 0;
        } elseif ($bottom > $archivedTasksCount  - 1) {
            $bottom = $archivedTasksCount - 1;
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();

        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $template_data = array_merge($template_data, array(
                                    'archivedTasks' => $archivedTasks,
                                    "page_no" => $page_no,
                                    "last" => $totalPages,
                                    "top" => $top,
                                    "bottom" => $bottom,
                                    "taskTypeColours" => $taskTypeColours,
                                    "archivedTasksCount" => $archivedTasksCount
        ));
        UserRouteHandler::render("task/archived-tasks.tpl", $response);
        return $response;
    }

    public function claimedTasks(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];
        $currentScrollPage  = !empty($args['page_no']) ? $args['page_no'] : 1;
        $selectedTaskType   = !empty($args['tt'])      ? $args['tt'] : 0;
        $selectedTaskStatus = !empty($args['ts'])      ? $args['ts'] : 3;
        $selectedOrdering   = !empty($args['o'])       ? $args['o'] : 0;

        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        $user = $userDao->getUser($user_id);

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if ($loggedInUserId != $user_id) {
            $adminDao = new DAO\AdminDao();
            if (!$adminDao->isSiteAdmin($loggedInUserId)) {
                UserRouteHandler::flash('error', 'You are not authorized to view this page');
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        }

        $taskStatusTexts = array();
        $taskStatusTexts[1] = Lib\Localisation::getTranslation('common_waiting');
        $taskStatusTexts[2] = Lib\Localisation::getTranslation('common_unclaimed');
        $taskStatusTexts[3] = Lib\Localisation::getTranslation('common_in_progress');
        $taskStatusTexts[4] = Lib\Localisation::getTranslation('common_complete');

        $taskTypeTexts = array();
        $taskTypeTexts[Common\Enums\TaskTypeEnum::SEGMENTATION]   = Lib\Localisation::getTranslation('common_segmentation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::TRANSLATION]    = Lib\Localisation::getTranslation('common_translation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::PROOFREADING]   = Lib\Localisation::getTranslation('common_proofreading');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::DESEGMENTATION] = Lib\Localisation::getTranslation('common_desegmentation');

        $numTaskTypes = Common\Lib\Settings::get('ui.task_types');
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $siteLocation = Common\Lib\Settings::get('site.location');
        $itemsPerScrollPage = 6;
        $offset = ($currentScrollPage - 1) * $itemsPerScrollPage;
        $topTasksCount = 0;

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();

            if (isset($post['taskTypes'])) {
                $selectedTaskType = $post['taskTypes'];
            }
            if (isset($post['taskStatusFilter'])) {
                $selectedTaskStatus = $post['taskStatusFilter'];
            }
            if (isset($post['ordering'])) {
                $selectedOrdering = $post['ordering'];
            }
        }
        // Post or route handler may return '0', need an explicit zero
        $selectedTaskType   = (int)$selectedTaskType;
        $selectedTaskStatus = (int)$selectedTaskStatus;
        $selectedOrdering   = (int)$selectedOrdering;

        try {
            $topTasks      = $userDao->getFilteredUserClaimedTasks($user_id, $selectedOrdering, $itemsPerScrollPage, $offset, $selectedTaskType, $selectedTaskStatus);
            $topTasksCount = $userDao->getFilteredUserClaimedTasksCount($user_id, $selectedTaskType, $selectedTaskStatus);
        } catch (\Exception $e) {
            $topTasks = array();
            $topTasksCount = 0;
        }

        $taskTags = array();
        $created_timestamps = array();
        $deadline_timestamps = array();
        $completed_timestamps = [];
        $projectAndOrgs = array();
        $discourse_slug = array();
        $proofreadTaskIds = array();
        $parentTaskIds = [];
        $show_memsource_revision = [];
        $matecat_urls = array();
        $allow_downloads = array();
        $show_mark_chunk_complete = array();
        $memsource_tasks = [];

        $lastScrollPage = ceil($topTasksCount / $itemsPerScrollPage);
        if ($currentScrollPage <= $lastScrollPage) {
            foreach ($topTasks as $topTask) {
                $taskId = $topTask->getId();
                $project = $projectDao->getProject($topTask->getProjectId());
                $org_id = $project->getOrganisationId();
                $org = $orgDao->getOrganisation($org_id);

                $taskTags[$taskId] = $taskDao->getTaskTags($taskId);

                $created = $topTask->getCreatedTime();
                $selected_year   = (int)substr($created,  0, 4);
                $selected_month  = (int)substr($created,  5, 2);
                $selected_day    = (int)substr($created,  8, 2);
                $selected_hour   = (int)substr($created, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
                $selected_minute = (int)substr($created, 14, 2);
                $created_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

                $deadline = $topTask->getDeadline();
                $selected_year   = (int)substr($deadline,  0, 4);
                $selected_month  = (int)substr($deadline,  5, 2);
                $selected_day    = (int)substr($deadline,  8, 2);
                $selected_hour   = (int)substr($deadline, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
                $selected_minute = (int)substr($deadline, 14, 2);
                $deadline_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

                if ($topTask->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE && $completed = $taskDao->get_task_complete_date($taskId)) {
                    $selected_year   = (int)substr($completed,  0, 4);
                    $selected_month  = (int)substr($completed,  5, 2);
                    $selected_day    = (int)substr($completed,  8, 2);
                    $selected_hour   = (int)substr($completed, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not know what the local time zone is)
                    $selected_minute = (int)substr($completed, 14, 2);
                    $completed_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);
                } else {
                    $completed_timestamps[$taskId] = 0;
                }

                $projectUri = "{$siteLocation}project/{$project->getId()}/view";
                $projectName = $project->getTitle();
                $orgUri = "{$siteLocation}org/{$org_id}/profile";
                $orgName = $org->getName();
                $projectAndOrgs[$taskId]=sprintf(
                    Lib\Localisation::getTranslation('common_part_of_for'),
                    $projectUri,
                    htmlspecialchars($projectName, ENT_COMPAT, 'UTF-8'),
                    $orgUri,
                    htmlspecialchars($orgName, ENT_COMPAT, 'UTF-8')
                );

                $memsource_task = $projectDao->get_memsource_task($taskId);
                $memsource_tasks[$taskId] = $memsource_task;
                if ($projectDao->are_translations_not_all_complete($topTask, $memsource_task)) $matecat_urls[$taskId] = '';
                else                                                                           $matecat_urls[$taskId] = $taskDao->get_matecat_url($topTask, $memsource_task);
                $allow_downloads[$taskId] = $taskDao->get_allow_download($topTask, $memsource_task);
                $show_mark_chunk_complete[$taskId] = 0;
                if (!$allow_downloads[$taskId] && $matecat_urls[$taskId] && !$memsource_task) { // it's a chunk && a bit of optimisation
                    $matecat_tasks = $taskDao->getTaskChunk($taskId);
                    $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                    $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];

                    $download_status = $taskDao->getMatecatTaskStatus($taskId, $matecat_id_job, $matecat_id_job_password);
                    if ($download_status === 'approved' || ($topTask->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION && $download_status === 'translated')) {
                        $show_mark_chunk_complete[$taskId] = 1;
                    }
                }

                $discourse_slug[$taskId] = $projectDao->discourse_parameterize($project);

                $show_memsource_revision[$taskId] = null;
                if ($memsource_task) {
                    $project_tasks = $projectDao->get_tasks_for_project($topTask->getProjectId());
                    foreach ($project_tasks as $project_task) {
                        if ($taskId == $project_task['id']) $top_level = $projectDao->get_top_level($project_task['internalId']);
                    }
                    $revision_task = 0;
                    $revision_complete = 1;
                    foreach ($project_tasks as $project_task) {
                        if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                            if ($project_task['task-type_id'] == Common\Enums\TaskTypeEnum::PROOFREADING) { // Revision
                                if (!$revision_task) $revision_task = $project_task['id'];
                                if ($project_task['task-status_id'] != Common\Enums\TaskStatusEnum::COMPLETE) $revision_complete = 0;
                            }
                        }
                    }
                    if ($revision_task && $revision_complete) $show_memsource_revision[$taskId] = $revision_task;
                    $proofreadTaskIds[$taskId] = null;
                    $parentTaskIds[$taskId] = null;
                } else {
                if ($topTask->getTaskType() == 2) { // If current task is a translation task
                    try {
                        $proofreadTask = $taskDao->getProofreadTask($taskId);
                    } catch (\Exception $e) {
                        $proofreadTask = null;
                    }
                    if ($proofreadTask) {
                        $proofreadTaskIds[$taskId] = $proofreadTask->getId();
                    } else {
                        $proofreadTaskIds[$taskId] = null;
                    }
                }

                $parentTaskIds[$taskId] = null;
                if (!$allow_downloads[$taskId]) { // Chunked
                    if ($parent_translation_id = $taskDao->get_parent_transation_task($topTask)) {
                        $parent_translation_task = $taskDao->getTask($parent_translation_id);
                        if ($parent_translation_task->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE) {
                            $parentTaskIds[$taskId] = $parent_translation_id;
                        }
                    }
                }
                }
            }
        }

        if ($currentScrollPage == $lastScrollPage && ($topTasksCount % $itemsPerScrollPage != 0)) {
            $itemsPerScrollPage = $topTasksCount % $itemsPerScrollPage;
        }
        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ias.min.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Home2.js\"></script>";

        $template_data = array_merge($template_data, array(
            'current_page' => 'claimed-tasks',
            'thisUser' => $user,
            'user_id' => $user_id,
            'siteLocation' => $siteLocation,
            'selectedTaskType' => $selectedTaskType,
            'selectedTaskStatus' => $selectedTaskStatus,
            'selectedOrdering' => $selectedOrdering,
            'topTasks' => $topTasks,
            'taskStatusTexts' => $taskStatusTexts,
            'taskTypeTexts' => $taskTypeTexts,
            'taskTypeColours' => $taskTypeColours,
            'taskTags' => $taskTags,
            'created_timestamps' => $created_timestamps,
            'deadline_timestamps' => $deadline_timestamps,
            'completed_timestamps' => $completed_timestamps,
            'projectAndOrgs' => $projectAndOrgs,
            'matecat_urls' => $matecat_urls,
            'allow_downloads' => $allow_downloads,
            'show_mark_chunk_complete' => $show_mark_chunk_complete,
            'discourse_slug' => $discourse_slug,
            'proofreadTaskIds' => $proofreadTaskIds,
            'parentTaskIds'    => $parentTaskIds,
            'show_memsource_revision' => $show_memsource_revision,
            'memsource_tasks' => $memsource_tasks,
            'currentScrollPage' => $currentScrollPage,
            'itemsPerScrollPage' => $itemsPerScrollPage,
            'lastScrollPage' => $lastScrollPage,
            'extra_scripts' => $extra_scripts,
        ));
        UserRouteHandler::render('task/claimed-tasks.tpl', $response);
        return $response;
    }

    public function recentTasks(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];
        $currentScrollPage  = !empty($args['page_no']) ? $args['page_no'] : 1;

        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        $user = $userDao->getUser($user_id);

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if ($loggedInUserId != $user_id) {
            $adminDao = new DAO\AdminDao();
            if (!$adminDao->isSiteAdmin($loggedInUserId)) {
                UserRouteHandler::flash('error', "You are not authorized to view this page"); //need to move to strings.xml
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        }

        $taskStatusTexts = array();
        $taskStatusTexts[1] = Lib\Localisation::getTranslation('common_waiting');
        $taskStatusTexts[2] = Lib\Localisation::getTranslation('common_unclaimed');
        $taskStatusTexts[3] = Lib\Localisation::getTranslation('common_in_progress');
        $taskStatusTexts[4] = Lib\Localisation::getTranslation('common_complete');

        $taskTypeTexts = array();
        $taskTypeTexts[Common\Enums\TaskTypeEnum::SEGMENTATION]   = Lib\Localisation::getTranslation('common_segmentation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::TRANSLATION]    = Lib\Localisation::getTranslation('common_translation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::PROOFREADING]   = Lib\Localisation::getTranslation('common_proofreading');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::DESEGMENTATION] = Lib\Localisation::getTranslation('common_desegmentation');

        $numTaskTypes = Common\Lib\Settings::get('ui.task_types');
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $siteLocation = Common\Lib\Settings::get('site.location');
        $itemsPerScrollPage = 6;
        $offset = ($currentScrollPage - 1) * $itemsPerScrollPage;
        $recentTasksCount = 0;

        try {
            $recentTasks = $userDao->getUserRecentTasks($user_id, $itemsPerScrollPage, $offset);
            $recentTasksCount = $userDao->getUserRecentTasksCount($user_id);
        } catch (\Exception $e) {
            $recentTasks = array();
            $recentTasksCount = 0;
        }

        $taskTags = array();
        $created_timestamps = array();
        $deadline_timestamps = array();
        $projectAndOrgs = array();
        $proofreadTaskIds = array();

        $lastScrollPage = ceil($recentTasksCount / $itemsPerScrollPage);
        if ($currentScrollPage <= $lastScrollPage) {
            foreach ($recentTasks as $recentTask) {
                $taskId = $recentTask->getId();
                $project = $projectDao->getProject($recentTask->getProjectId());
                $org_id = $project->getOrganisationId();
                $org = $orgDao->getOrganisation($org_id);

                $taskTags[$taskId] = $taskDao->getTaskTags($taskId);

                $created = $recentTask->getCreatedTime();
                $selected_year   = (int)substr($created,  0, 4);
                $selected_month  = (int)substr($created,  5, 2);
                $selected_day    = (int)substr($created,  8, 2);
                $selected_hour   = (int)substr($created, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
                $selected_minute = (int)substr($created, 14, 2);
                $created_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

                $deadline = $recentTask->getDeadline();
                $selected_year   = (int)substr($deadline,  0, 4);
                $selected_month  = (int)substr($deadline,  5, 2);
                $selected_day    = (int)substr($deadline,  8, 2);
                $selected_hour   = (int)substr($deadline, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
                $selected_minute = (int)substr($deadline, 14, 2);
                $deadline_timestamps[$taskId] = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

                $projectUri = "{$siteLocation}project/{$project->getId()}/view";
                $projectName = $project->getTitle();
                $orgUri = "{$siteLocation}org/{$org_id}/profile";
                $orgName = $org->getName();
                $projectAndOrgs[$taskId]=sprintf(
                    Lib\Localisation::getTranslation('common_part_of_for'),
                    $projectUri,
                    htmlspecialchars($projectName, ENT_COMPAT, 'UTF-8'),
                    $orgUri,
                    htmlspecialchars($orgName, ENT_COMPAT, 'UTF-8')
                );
                
            }
        }

        if ($currentScrollPage == $lastScrollPage && ($recentTasksCount % $itemsPerScrollPage != 0)) {
            $itemsPerScrollPage = $recentTasksCount % $itemsPerScrollPage;
        }
        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ias.min.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Home2.js\"></script>";

        $template_data = array_merge($template_data, array(
            'current_page' => 'recent-tasks',
            'thisUser' => $user,
            'user_id' => $user_id,
            'siteLocation' => $siteLocation,
            'recentTasks' => $recentTasks,
            'taskStatusTexts' => $taskStatusTexts,
            'taskTypeTexts' => $taskTypeTexts,
            'taskTypeColours' => $taskTypeColours,
            'taskTags' => $taskTags,
            'created_timestamps' => $created_timestamps,
            'deadline_timestamps' => $deadline_timestamps,
            'projectAndOrgs' => $projectAndOrgs,
            'currentScrollPage' => $currentScrollPage,
            'itemsPerScrollPage' => $itemsPerScrollPage,
            'lastScrollPage' => $lastScrollPage,
            'extra_scripts' => $extra_scripts,
        ));
        UserRouteHandler::render('task/recent-tasks.tpl', $response);
        return $response;
    }

    public function downloadTaskLatestVersion(Request $request, Response $response, $args)
    {
        global $app;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();

        $task = $taskDao->getTask($task_id);
        $latest_version = $taskDao->getTaskVersion($task_id);
        try {
            $projectDao = new DAO\ProjectDao();
            $memsource_task = $projectDao->get_memsource_task($task_id);
            if ($memsource_task) {
                $user_id = Common\Lib\UserSession::getCurrentUserID();
                if (is_null($user_id) || $taskDao->isUserRestrictedFromTaskButAllowTranslatorToDownload($task_id, $user_id)) {
                    UserRouteHandler::flash('error', 'You are not authorized to view this page');
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
                }
                $memsource_project = $projectDao->get_memsource_project($task->getProjectId());
                $userDao = new DAO\UserDao();
                $file = $userDao->memsource_get_target_file($memsource_project['memsource_project_uid'], $memsource_task['memsource_task_uid']);
                if (empty($file)) {
                    UserRouteHandler::flash('error', 'Could not retrieve file');
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
                }
                $task_file_info = $taskDao->getTaskInfo($task_id, 0);
                $filename = $task_file_info->getFilename();
                $mime = $userDao->detectMimeType($file, $filename);
                header("Content-type: $mime");
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-length: ' . strlen($file));
                header('X-Frame-Options: ALLOWALL');
                header('Pragma: no-cache');
                header('Cache-control: no-cache, must-revalidate, no-transform');
                echo $file;
                die;
            } else {
                $args['version'] = $latest_version;
                $this->downloadTaskVersion($request, $response, $args);
            }
        } catch (Common\Exceptions\SolasMatchException $e) {
            UserRouteHandler::flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_latest_task_file_version'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
    }

    public function archiveTask(Request $request, Response $response, $args)
    {
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();

        $task = $taskDao->getTask($task_id);
        $userId = Common\Lib\UserSession::getCurrentUserID();

        $taskType = Lib\TemplateHelper::getTaskTypeFromId($task->getTaskType());
        $result = $taskDao->archiveTask($task_id, $userId);
        if ($result) {
            UserRouteHandler::flash(
                "success",
                sprintf(
                    Lib\Localisation::getTranslation('project_view_17'),
                    $taskType,
                    $task->getTitle()
                )
            );
        } else {
            UserRouteHandler::flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('project_view_18'),
                    $taskType,
                    $task->getTitle()
                )
            );
        }

        return $response->withStatus(302)->withHeader('Location', $request->getUri());
    }

    public function downloadTask(Request $request, Response $response, $args)
    {
        global $app;

        try {
            $this->downloadTaskVersion($request, $response, $args);
        } catch (Common\Exceptions\SolasMatchException $e) {
            UserRouteHandler::flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_original_task_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
    }

    public function downloadTaskExternal(Request $request, Response $response, $args)
    {
        global $app;
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();

        try {
            $headerArr = $taskDao->downloadTaskVersion($this->decrypt_task_id($taskId), 0);
            if (!empty($headerArr)) {
                $headerArr = unserialize($headerArr);
                foreach ($headerArr as $key => $val) {
                    $response->withHeader($key, $val);
                }
            }
            return $response;
        } catch (Common\Exceptions\SolasMatchException $e) {
            UserRouteHandler::flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_original_task_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
    }

    private function encrypt_task_id($task_id) {
        $key = Common\Lib\Settings::get('session.site_key');
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext_raw = openssl_encrypt("$task_id", 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        return bin2hex($iv . $hmac . $ciphertext_raw);
    }

    private function decrypt_task_id($ciphertext) {
        $key = Common\Lib\Settings::get('session.site_key');
        $c = hex2bin($ciphertext);
        $iv = substr($c, 0, 16);
        $hmac = substr($c, 16, 32);
        $ciphertext_raw = substr($c, 48);
        $original_plaintext = openssl_decrypt($ciphertext_raw, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        if (hash_equals($hmac, $calcmac)) return $original_plaintext;
        return 999999999;
    }

    /*
     *  Claim and download a task
     */
    public function taskClaim(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $languageDao = new DAO\LanguageDao();
        $projectDao = new DAO\ProjectDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $taskClaimed = $taskDao->isTaskClaimed($taskId);
        if ($taskClaimed) { // Protect against someone inappropriately creating URL for this route
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task", array("task_id" => $taskId)));
        }

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        if ($taskDao->isUserRestrictedFromTask($taskId, $user_id)) {
            UserRouteHandler::flash('error', "You are not authorized to view this page");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $memsource_task = $projectDao->get_memsource_task($taskId);

        $task = $taskDao->getTask($taskId);
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskClaim')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            $user_id = Common\Lib\UserSession::getCurrentUserID();

            $taskDao->record_task_if_translated_in_matecat($task);
            $success = $userDao->claimTask($user_id, $taskId, $memsource_task, $task->getProjectId(), $task);
            if ($success == 1) {
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-claimed', array('task_id' => $taskId)));
            } elseif ($success == -1) {
                UserRouteHandler::flashNow('error', 'Unable to create user in Memsource.');
            } else {
                UserRouteHandler::flashNow('error', 'This task can no longer be claimed, the job has been removed from Memsource and will soon be removed from here.');
            }
        }

        $sourcelocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $sourceLanguage = $languageDao->getLanguageByCode($sourcelocale->getLanguageCode());
        $targetLanguage = $languageDao->getLanguageByCode($targetLocale->getLanguageCode());
        $taskMetaData = $taskDao->getTaskInfo($taskId);

        // Used in proofreading page, link to original project file
        $projectFileDownload = $app->getRouteCollector()->getRouteParser()->urlFor("home")."project/".$task->getProjectId()."/file";


        $template_data = array_merge($template_data, array(
                    'sesskey' => $sesskey,
                    "projectFileDownload" => $projectFileDownload,
                    "task"          => $task,
                    "sourceLanguage"=> $sourceLanguage,
                    "targetLanguage"=> $targetLanguage,
                    'matecat_url'   => '',
                    'allow_download'=> $taskDao->get_allow_download($task, $memsource_task),
                    'memsource_task'=> $memsource_task,
                    "taskMetadata"  => $taskMetaData
        ));

        UserRouteHandler::render("task/task.claim.tpl", $response);
        return $response;
    }

    public function taskClaimed(Request $request, Response $response, $args)
    {
        global $template_data;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();

        $task = $taskDao->getTask($task_id);

        $memsource_task = $projectDao->get_memsource_task($task_id);

        $template_data = array_merge($template_data, array(
            'task' => $task,
            'matecat_url' => $taskDao->get_matecat_url($task, $memsource_task),
            'allow_download' => $taskDao->get_allow_download($task, $memsource_task),
            'memsource_task' => $memsource_task,
            'translations_not_all_complete' => $projectDao->are_translations_not_all_complete($task, $memsource_task),
            'isSiteAdmin'    => $adminDao->isSiteAdmin(Common\Lib\UserSession::getCurrentUserID()),
        ));

        UserRouteHandler::render("task/task.claimed.tpl", $response);
        return $response;
    }

    public function downloadTaskVersion(Request $request, Response $response, $args)
    {
        global $app;
        $taskId = $args['task_id'];
        $version = !empty($args['version']) ? $args['version'] : 0;

        $taskDao = new DAO\TaskDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        if (is_null($user_id) || $taskDao->isUserRestrictedFromTaskButAllowTranslatorToDownload($taskId, $user_id)) {
            UserRouteHandler::flash('error', "You are not authorized to view this page");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $headerArr = $taskDao->downloadTaskVersion($taskId, $version);
        if (!empty($headerArr)) {
            $headerArr = unserialize($headerArr);
            foreach ($headerArr as $key => $val) {
                $response->withHeader($key, $val);
            }
        }
        return $response;
    }

    public function task(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $adminDao = new DAO\AdminDao();
        $isSiteAdmin = $adminDao->isSiteAdmin($user_id);

        $task = $taskDao->getTask($taskId);
        if (is_null($task)) {
            UserRouteHandler::flash("error", sprintf(Lib\Localisation::getTranslation('task_view_5'), $taskId));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
        }

        if ($taskDao->isUserRestrictedFromTask($taskId, $user_id)) {
            UserRouteHandler::flash('error', "You are not authorized to view this page");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $taskClaimed = $taskDao->isTaskClaimed($taskId);
        $trackTaskView = $taskDao->recordTaskView($taskId,$user_id);

        $memsource_task = $projectDao->get_memsource_task($taskId);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'task')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            $project = $projectDao->getProject($task->getProjectId());
            $org_id=$project->getOrganisationId();

            if (isset($post['trackOrganisation'])) {
                if ($post['trackOrganisation']) {
                    $userTrackOrganisation = $userDao->trackOrganisation($user_id, $org_id);
                    if ($userTrackOrganisation) {
                        UserRouteHandler::flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_success')
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_error')
                        );
                    }
                } else {
                    $userUntrackOrganisation = $userDao->unTrackOrganisation($user_id, $org_id);
                    if ($userUntrackOrganisation) {
                        UserRouteHandler::flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_success')
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_error')
                        );
                    }
                }
            }
        }
        if ($taskClaimed) {
            UserRouteHandler::flashKeep();

           if ($memsource_task) return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-view', array('task_id' => $taskId)));

            switch ($task->getTaskType()) {
                case Common\Enums\TaskTypeEnum::DESEGMENTATION:
                    // return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-desegmentation", array("task_id" => $taskId)));
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
                    break;
                case Common\Enums\TaskTypeEnum::TRANSLATION:
                case Common\Enums\TaskTypeEnum::PROOFREADING:
                  if ($taskDao->get_allow_download($task, $memsource_task)) {
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-simple-upload", array("task_id" => $taskId)));
                  } else {
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-chunk-complete', array('task_id' => $taskId)));
                  }
                    break;
                case Common\Enums\TaskTypeEnum::SEGMENTATION:
                    // return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-segmentation", array("task_id" => $taskId)));
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
                    break;
            }
        } else {
            if ($isSiteAdmin && ((isset($post['userIdOrEmail']) && trim($post['userIdOrEmail']) != "") || !empty($post['assignUserSelect']))) {
                $emailOrUserId = trim($post['userIdOrEmail']);
                if (!empty($post['assignUserSelect'])) $emailOrUserId = $post['assignUserSelect'];
                $userToBeAssigned = null;
                $errorOccured = False;
                if (ctype_digit($emailOrUserId)) { //checking for intergers in a string (user id)
                    $userToBeAssigned = $userDao->getUser($emailOrUserId);
                    if (is_null($userToBeAssigned)) {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_view_assign_id_error'));
                        $errorOccured = True;
                    }
                } else if (Lib\Validator::validateEmail($emailOrUserId)) {
                    $userToBeAssigned = $userDao->getUserByEmail($emailOrUserId);
                    if (is_null($userToBeAssigned)) {
                        $errorOccured = True;
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_view_assign_email_error'));
                    }
                } else {
                    $errorOccured = True;
                    UserRouteHandler::flashNow("error",Lib\Localisation::getTranslation('task_view_assign_id_or_email_error'));
                }

                if (!$errorOccured && !is_null($userToBeAssigned))
                {
                    $userDisplayName = $userToBeAssigned->getDisplayName();
                    $assgneeId = $userToBeAssigned->getId();
                    $isUserBlackListedForTask = $userDao->isBlacklistedForTask($assgneeId, $taskId);
                    if ($isUserBlackListedForTask)
                    {
                        UserRouteHandler::flashNow("error", sprintf(Lib\Localisation::getTranslation('task_view_assign_task_banned_error'), $userDisplayName));
                    } else {
                        $taskDao->record_task_if_translated_in_matecat($task);
                        $success = $userDao->claimTask($assgneeId, $taskId, $memsource_task, $task->getProjectId(), $task);
                        if ($success == 1) {
                            UserRouteHandler::flash('success', sprintf(Lib\Localisation::getTranslation('task_view_assign_task_success'), $userDisplayName));
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array('project_id' => $task->getProjectId())));
                        } elseif ($success == -1) {
                            UserRouteHandler::flashNow('error', 'Unable to create user in Memsource.');
                        } else {
                            UserRouteHandler::flashNow('error', 'This task can no longer be claimed, the job has been removed from Memsource and will soon be removed from here.');
                        }
                    }
                }
                $post['userIdOrEmail']="";
            }
            if ($isSiteAdmin && !empty($post['userIdOrEmailDenyList'])) {
                $userIdOrEmail = trim($post['userIdOrEmailDenyList']);
                if (ctype_digit($userIdOrEmail)) $remove_deny_user = $userDao->getUser($userIdOrEmail);
                else                             $remove_deny_user = $userDao->getUserByEmail($userIdOrEmail);
                if (empty($remove_deny_user)) {
                    UserRouteHandler::flashNow('error', 'User does not exist.');
                } else {
                    $taskDao->removeUserFromTaskBlacklist($remove_deny_user->getId(), $taskId);
                    UserRouteHandler::flashNow('success', 'Removed (assuming was actually in deny list)');
                }
            }

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $project = $projectDao->getProject($task->getProjectId());

        /*Metadata required for Tracking Organisations*/
        $org_id = $project->getOrganisationId();
        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($user_id, $org_id);
        $isMember = $orgDao->isMember($project->getOrganisationId(), $user_id);

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }
        
        $taskTypeTexts = array();
        $taskTypeTexts[Common\Enums\TaskTypeEnum::SEGMENTATION]   = Lib\Localisation::getTranslation('common_segmentation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::TRANSLATION]    = Lib\Localisation::getTranslation('common_translation');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::PROOFREADING]   = Lib\Localisation::getTranslation('common_proofreading');
        $taskTypeTexts[Common\Enums\TaskTypeEnum::DESEGMENTATION] = Lib\Localisation::getTranslation('common_desegmentation');
        
        $taskStatusTexts = array();
        $taskStatusTexts[1] = Lib\Localisation::getTranslation('common_waiting');
        $taskStatusTexts[2] = Lib\Localisation::getTranslation('common_unclaimed');
        $taskStatusTexts[3] = Lib\Localisation::getTranslation('common_in_progress');
        $taskStatusTexts[4] = Lib\Localisation::getTranslation('common_complete');

        $converter = Common\Lib\Settings::get("converter.converter_enabled");

        $siteLocation = Common\Lib\Settings::get("site.location");

        if ($parent_translation_id = $taskDao->get_parent_transation_task($task)) {
            $task_file_info = $taskDao->getTaskInfo($parent_translation_id);
            $file_path = "{$siteLocation}task/" . $this->encrypt_task_id($parent_translation_id) . '/download-task-external/';
            $chunked_message = '(each translator will work on a specific chunk)';
        } else {
            $task_file_info = $taskDao->getTaskInfo($taskId);
            $file_path = "{$siteLocation}task/" . $this->encrypt_task_id($taskId) . '/download-task-external/';
            $chunked_message = '';
        }

        $alsoViewedTasksCount = 0;
        
        $alsoViewedTasks = $taskDao->getAlsoViewedTasks($taskId, $user_id, 0);
        if (!empty($alsoViewedTasks)) {
            $alsoViewedTasksCount = count($alsoViewedTasks);
        }
        
        $created_timestamps = array();
        $deadline_timestamps = array();
        $projectAndOrgs = array();
    
        if (is_array($alsoViewedTasks) || is_object($alsoViewedTasks))
        {
                
                foreach ($alsoViewedTasks as $alsoViewedTask) {
                $viewedTaskId = $alsoViewedTask->getId();
                $viewedProject = $projectDao->getProject($alsoViewedTask->getProjectId());
                $viewedOrgId = $viewedProject->getOrganisationId();
                $viewedOrg = $orgDao->getOrganisation($viewedOrgId);
    
                $deadline = $alsoViewedTask->getDeadline();
                $deadline_timestamps[$viewedTaskId] = $deadline;
    
                $viewedProjectUri = "{$siteLocation}project/{$project->getId()}/view";
                $viewedProjectName = $viewedProject->getTitle();
                $viewedOrgUri = "{$siteLocation}org/{$org_id}/profile";
                $viewedOrgName = $viewedOrg->getName();
                $projectAndOrgs[$viewedTaskId]=sprintf(
                    Lib\Localisation::getTranslation('common_part_of_for'),
                    $viewedProjectUri,
                    htmlspecialchars($viewedProjectName, ENT_COMPAT, 'UTF-8'),
                    $viewedOrgUri,
                    htmlspecialchars($viewedOrgName, ENT_COMPAT, 'UTF-8')
                );
    
            }
        }

        $list_qualified_translators = array();
        if ($isSiteAdmin) $list_qualified_translators = $taskDao->list_qualified_translators($taskId);

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView1.js");
        $extra_scripts .= "<script type=\"text/javascript\" >
        $(document).ready(function() {
            var member = ".$isMember.";
           /* if (member) {
                $('.claim_btn').css('margin-right', '55%');
            }
            */
        });
        </script>";

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "extra_scripts" => $extra_scripts,
            "taskTypeColours" => $taskTypeColours,
            "project" => $project,
            "converter" => $converter,
            "task" => $task,
            "file_preview_path" => $file_path,
            'chunked_message' => $chunked_message,
            "filename" => $task_file_info->getFilename(),
            "isMember" => $isMember,
            "isSiteAdmin"   => $isSiteAdmin,
            'userSubscribedToOrganisation' => $userSubscribedToOrganisation,
            'deadline_timestamps' => $deadline_timestamps,
            'alsoViewedTasks' => $alsoViewedTasks,
            'alsoViewedTasksCount' => $alsoViewedTasksCount,
            'siteLocation' => $siteLocation,
            'taskTypeTexts' => $taskTypeTexts,
            'projectAndOrgs' => $projectAndOrgs,
            'discourse_slug' => $projectDao->discourse_parameterize($project),
            'memsource_task' => $memsource_task,
            'matecat_url' => $taskDao->get_matecat_url_regardless($task, $memsource_task),
            'list_qualified_translators' => $list_qualified_translators,
            'display_treat_as_translated' => 0,
            'this_is_id' => 1,
            'taskStatusTexts' => $taskStatusTexts
        ));

        UserRouteHandler::render("task/task.view.tpl", $response);
        return $response;
        }
    }

    public function taskSimpleUpload(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $taskId = $args['task_id'];

        $matecat_api = Common\Lib\Settings::get('matecat.url');
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $taskClaimed = $taskDao->isTaskClaimed($taskId);
        if (!$taskClaimed) { // Protect against someone inappropriately creating URL for this route
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task", array("task_id" => $taskId)));
        }

        $fieldName = "fileUpload";
        $errorMessage = null;
        $userId = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());

        $memsource_task = $projectDao->get_memsource_task($taskId); // $memsource_task should never be set, protect against this
        if ($memsource_task || !$taskDao->get_allow_download($task, $memsource_task)) {
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $taskId)));
        }

        if ($request->getMethod() === 'POST') {
          $post = $request->getParsedBody();
          if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskSimpleUpload')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

          if (!empty($post['copy_from_matecat'])) {
            $matecat_tasks = $taskDao->getMatecatLanguagePairs($taskId);
            if (!empty($matecat_tasks)) {
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                $matecat_id_file = $matecat_tasks[0]['matecat_id_file'];
                if (!empty($matecat_id_job) && !empty($matecat_id_job_password) && !empty($matecat_id_file)) {
                    $matecat_id_file = ''; // Need all files in job to be downloaded
                    $re = curl_init("{$matecat_api}?action=downloadFile&id_job=$matecat_id_job&id_file=$matecat_id_file&password=$matecat_id_job_password&download_type=all");

                    curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($re, CURLOPT_COOKIESESSION, true);
                    curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($re, CURLOPT_AUTOREFERER, true);

                    $httpHeaders = array(
                        'Expect:'
                    );
                    curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

                    curl_setopt($re, CURLOPT_HEADER, false);
                    curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                    $res = curl_exec($re);
                    if( $res !== false) {
                        $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                        curl_close($re);

                        if ($responseCode == 200) {
                            try {
                                error_log("taskSimpleUpload copy_from_matecat ($taskId)");
                                $taskDao->uploadOutputFile($taskId, $userId, $res);
                            } catch (\Exception  $e) {
                                $errorMessage = Lib\Localisation::getTranslation('task_simple_upload_7') . $e->getMessage();
                                error_log($errorMessage);
                            }
                        } else {
                            $errorMessage = "Curl error ($taskId) responseCode: $responseCode";
                            error_log($errorMessage);
                        }
                    } else {
                        $errorMessage = "Curl error ($taskId): " . curl_error($re);
                        error_log($errorMessage);
                        curl_close($re);
                    }
                } else {
                    $errorMessage = "Curl error ($taskId) matecat_id_job($matecat_id_job), matecat_id_job_password($matecat_id_job_password) or matecat_id_file($matecat_id_file) empty!";
                    error_log($errorMessage);
                }
            } else {
                $errorMessage = "Curl error ($taskId) MateCat data not found!";
                error_log($errorMessage);
            }
          } else {
            try {
                Lib\TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
                $projectFile = $projectDao->getProjectFileInfo($project->getId());
                $projectFileMimeType = $projectFile->getMime();
                $projectFileType = pathinfo($projectFile->getFilename(), PATHINFO_EXTENSION);

                $fileUploadType = pathinfo($_FILES[$fieldName]["name"], PATHINFO_EXTENSION);

                //Call API to determine MIME type of file contents
                $helper = new Common\Lib\APIHelper(Common\Lib\Settings::get('ui.api_format'));
                $siteApi = Common\Lib\Settings::get("site.api");
                $filename = urlencode($_FILES[$fieldName]["name"]);
                $request_url = $siteApi . "v0/io/contentMime/$filename";
                $data = file_get_contents($_FILES[$fieldName]["tmp_name"]);
                $fileUploadMime = $helper->call(null, $request_url, Common\Enums\HttpMethodEnum::POST, null, null, $data);
                if (strcasecmp($fileUploadType, $projectFileType) != 0) {
                    throw new \Exception(sprintf(
                        Lib\Localisation::getTranslation('common_task_file_extension_mismatch'),
                        $projectFileType
                    ));
                } elseif ($fileUploadMime != $projectFileMimeType) {
                    throw new \Exception(
                        sprintf(
                            Lib\Localisation::getTranslation('task_simple_upload_6'),
                            $projectFileType,
                            $projectFileType
                        )
                    );
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }

            if (is_null($errorMessage)) {
                try {
                    $filedata = file_get_contents($_FILES[$fieldName]["tmp_name"]);

                    error_log("taskSimpleUpload _FILES ($taskId)");
                    if ($post['submit'] == 'XLIFF') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata, true);
                    } elseif ($post['submit'] == 'submit') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata);
                    }

                } catch (\Exception  $e) {
                    $errorMessage = Lib\Localisation::getTranslation('task_simple_upload_7') . $e->getMessage();
                }
            }
          }

          if (is_null($errorMessage)) {
              return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-review", array("task_id" => $taskId)));
          } else {
              UserRouteHandler::flashNow("error", $errorMessage);
          }
        }

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $taskVersion = $taskDao->getTaskVersion($task->getId());

        $file_previously_uploaded = false;
        if ($taskVersion > 0) {
            $file_previously_uploaded = true;
        }

        $taskFileInfo = $taskDao->getTaskInfo($taskId, 0);
        $filename = $taskFileInfo->getFilename();
        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");

        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $converter = Common\Lib\Settings::get("converter.converter_enabled");

        $matecat_url = '';
        $matecat_download_url = '';
        $chunks = array();
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION || $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $matecat_tasks = $taskDao->getMatecatLanguagePairs($taskId);
            if (!empty($matecat_tasks)) {
                $matecat_langpair = $matecat_tasks[0]['matecat_langpair'];
                $matecat_id_job = $matecat_tasks[0]['matecat_id_job'];
                $matecat_id_job_password = $matecat_tasks[0]['matecat_id_job_password'];
                $matecat_id_file = $matecat_tasks[0]['matecat_id_file'];
                if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password) && !empty($matecat_id_file)) {
                  if ($taskDao->getTaskSubChunks($matecat_id_job)) {
                      // This has been chunked, so need to accumulate status of all chunks
                      $chunks = $taskDao->getStatusOfSubChunks($task->getProjectId(), $matecat_langpair, $matecat_id_job, $matecat_id_job_password, $matecat_id_file);
                      $translated_status = true;
                      $approved_status   = true;
                      foreach ($chunks as $index => $chunk) {
                          if ($chunk['DOWNLOAD_STATUS'] === 'draft') $translated_status = false;
                          if ($chunk['DOWNLOAD_STATUS'] === 'draft' || $chunk['DOWNLOAD_STATUS'] === 'translated') $approved_status = false;

                          $matecat_url = $chunk['translate_url']; // As we are chunked, the $matecat_url scalar string will not be used as a URL in the template, just for logic.
                          $matecat_download_url = $chunk['matecat_download_url'];
                      }

                      if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION  && !$translated_status) $matecat_url = '';
                      if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && !$approved_status  ) $matecat_url = '';
                  } else {
                   $recorded_status = $taskDao->getMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password);
                   if ($recorded_status === 'approved') { // We do not need to query MateCat...
                       $translate = 'translate';
                       if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';
                       $matecat_url = "{$matecat_api}$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password";
                       $matecat_id_file = ''; // Need all files in job to be downloaded
                       $matecat_download_url = "{$matecat_api}?action=downloadFile&id_job=$matecat_id_job&id_file=$matecat_id_file&password=$matecat_id_job_password&download_type=all";
                   } else {
                    // https://www.matecat.com/api/docs#!/Project/get_v1_jobs_id_job_password_stats
                    $re = curl_init("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats");

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

                        if (!empty($response_data['stats']['DOWNLOAD_STATUS'])) {
                            if ($response_data['stats']['DOWNLOAD_STATUS'] === 'draft') {
                                $response_data['stats']['DOWNLOAD_STATUS'] = $recorded_status; // getMatecatRecordedJobStatus() MIGHT have a "better" status
                            }
                            if ($response_data['stats']['DOWNLOAD_STATUS'] === 'translated' || $response_data['stats']['DOWNLOAD_STATUS'] === 'approved') {
                                $translate = 'translate';
                                if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';
                                $matecat_url = "{$matecat_api}$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password";
                                $matecat_id_file = ''; // Need all files in job to be downloaded
                                $matecat_download_url = "{$matecat_api}?action=downloadFile&id_job=$matecat_id_job&id_file=$matecat_id_file&password=$matecat_id_job_password&download_type=all";

                                if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $response_data['stats']['DOWNLOAD_STATUS'] === 'translated') {
                                    $matecat_url = ''; // Disable Kató access for Proofreading if job file is only translated
                                }
                            }
                        } else {
                            error_log("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats ($taskId) DOWNLOAD_STATUS empty!");
                        }
                    } else {
                        error_log("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats ($taskId) responseCode: $responseCode");
                    }
                   }
                  }
                }
            }
        }

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView.js");

        $template_data = array_merge($template_data, array(
            'sesskey'       => $sesskey,
            "extra_scripts" => $extra_scripts,
            "task"          => $task,
            "project"       => $project,
            "org"           => $org,
            "filename"      => $filename,
            "converter"     => $converter,
            "fieldName"     => $fieldName,
            "max_file_size" => Lib\TemplateHelper::maxFileSizeMB(),
            "taskTypeColours"   => $taskTypeColours,
            'matecat_url' => $matecat_url,
            'matecat_download_url' => $matecat_download_url,
            'chunks'               => $chunks,
            'discourse_slug' => $projectDao->discourse_parameterize($project),
            "file_previously_uploaded" => $file_previously_uploaded
        ));

        UserRouteHandler::render("task/task-simple-upload.tpl", $response);
        return $response;
    }

    public function taskChunkComplete(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $taskId = $args['task_id'];

        $matecat_api = Common\Lib\Settings::get('matecat.url');
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $taskClaimed = $taskDao->isTaskClaimed($taskId);
        if (!$taskClaimed) { // Protect against someone inappropriately creating URL for this route
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task", array("task_id" => $taskId)));
        }

        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());

        $matecat_tasks = $taskDao->getTaskChunk($taskId);
        if (empty($matecat_tasks)) {
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-view', array('task_id' => $taskId)));
        }

        if ($task->getTaskStatus() != Common\Enums\TaskStatusEnum::IN_PROGRESS) {
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-chunk-completed', array('task_id' => $taskId)));
        }

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskChunkComplete')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (!empty($post['copy_from_matecat'])) {
                error_log("Setting Task COMPLETE for: $taskId");
                $taskDao->setTaskStatus($taskId, Common\Enums\TaskStatusEnum::COMPLETE);
                $taskDao->sendTaskUploadNotifications($taskId, 1);
                $taskDao->set_task_complete_date($taskId);
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-review', array('task_id' => $taskId)));
            }
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $matecat_url = '';
        $matecat_langpair        = $matecat_tasks[0]['matecat_langpair'];
        $matecat_id_job          = $matecat_tasks[0]['matecat_id_job'];
        $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];
        $job_first_segment       = $matecat_tasks[0]['job_first_segment'];
        if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
            $recorded_status = $taskDao->getMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password);
            if ($recorded_status === 'approved') { // We do not need to query MateCat...
                $translate = 'translate';
                if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';
                $matecat_url = "{$matecat_api}$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password$job_first_segment";
            } else {
                // https://www.matecat.com/api/docs#!/Project/get_v1_jobs_id_job_password_stats
                $re = curl_init("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats");

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

                    if (!empty($response_data['stats']['DOWNLOAD_STATUS'])) {
                        if ($response_data['stats']['DOWNLOAD_STATUS'] === 'draft') {
                            $response_data['stats']['DOWNLOAD_STATUS'] = $recorded_status; // getMatecatRecordedJobStatus() MIGHT have a "better" status
                        }
                        if ($response_data['stats']['DOWNLOAD_STATUS'] === 'translated' || $response_data['stats']['DOWNLOAD_STATUS'] === 'approved') {
                            $translate = 'translate';
                            if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';
                            $matecat_url = "{$matecat_api}$translate/proj-" . $task->getProjectId() . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password$job_first_segment";

                            if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $response_data['stats']['DOWNLOAD_STATUS'] === 'translated') {
                                $matecat_url = ''; // Disable Kató access for Proofreading if job file is only translated
                            }
                        }
                    } else {
                        error_log("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats ($taskId) DOWNLOAD_STATUS empty!");
                    }
                } else {
                    error_log("{$matecat_api}api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats ($taskId) responseCode: $responseCode");
                }
            }
        }
        if (empty($matecat_url)) return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-view', array('task_id' => $taskId)));

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView.js");

        $template_data = array_merge($template_data, array(
            'sesskey'         => $sesskey,
            'extra_scripts'   => $extra_scripts,
            'task'            => $task,
            'project'         => $project,
            'taskTypeColours' => $taskTypeColours,
            'matecat_url'     => $matecat_url,
            'discourse_slug'  => $projectDao->discourse_parameterize($project),
        ));

        UserRouteHandler::render('task/task-chunk-complete.tpl', $response);
        return $response;
    }

    public function taskUploaded(Request $request, Response $response, $args)
    {
        global $template_data;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $tipDao = new DAO\TipDao();

        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $tip = $tipDao->getTip();

        $template_data = array_merge($template_data, array(
            "org_name" => $org->getName(),
            "tip"      => $tip
        ));

        UserRouteHandler::render("task/task.uploaded.tpl", $response);
        return $response;
    }

    public function taskChunkCompleted(Request $request, Response $response, $args)
    {
        global $template_data;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $tipDao = new DAO\TipDao();

        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $tip = $tipDao->getTip();

        $template_data = array_merge($template_data, array(
            'org_name' => $org->getName(),
            'tip'      => $tip
        ));

        UserRouteHandler::render('task/task-chunk-completed.tpl', $response);
        return $response;
    }

    public function taskAlter(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();
        $taskPreReqIds = array();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $word_count_err = null;
        $deadlockError = null;
        $deadlineError = "";

        $extra_scripts = "
        <script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\"></script>
        <script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/DeadlinePicker.js\"></script>";

        $task = $taskDao->getTask($task_id);

        $memsource_task = $projectDao->get_memsource_task($task_id);

        $preReqTasks = $taskDao->getTaskPreReqs($task_id);
        if (!$preReqTasks) {
            $preReqTasks = array();
        }

        $project = $projectDao->getProject($task->getProjectId());

        $projectTasks = [];
        if (!$memsource_task) $projectTasks = $projectDao->getProjectTasks($task->getProjectId());
        $allow_downloads = array();
        $tasksEnabled = [];
        $thisTaskPreReqIds = 0;
        foreach ($projectTasks as $projectTask) {
            $allow_downloads[$projectTask->getId()] = $taskDao->get_allow_download($projectTask, $memsource_task);

            if ($projectTask->getTaskStatus() == Common\Enums\TaskStatusEnum::IN_PROGRESS ||
                        $projectTask->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE) {
                $tasksEnabled[$projectTask->getId()] = false;
            } else {
                $tasksEnabled[$projectTask->getId()] = true;
            }

            $taskPreReqIds[$projectTask->getId()] = array();
            $taskPreReqs = $taskDao->getTaskPreReqs($projectTask->getId());
            if ($taskPreReqs) {
                foreach ($taskPreReqs as $preReq) {
                    $taskPreReqIds[$projectTask->getId()][] = $preReq->getId();
                }
            }

            // Remove this task from list of possible pre reqs
            if ($projectTask->getId() == $task_id) {
                $thisTaskPreReqIds = $taskPreReqIds[$projectTask->getId()];
                $index = array_search($projectTask, $projectTasks);
                if ($index) {
                    unset($projectTasks[$index]);
                }
            }
        }

        $site_admin = $adminDao->isSiteAdmin(Common\Lib\UserSession::getCurrentUserID());
        $adminAccess = $site_admin || $adminDao->isOrgAdmin($project->getOrganisationId(), Common\Lib\UserSession::getCurrentUserID());

        $template_data = array_merge($template_data, ['task' => $task]);

        if ($request->getMethod() === 'POST' && sizeof($request->getParsedBody()) > 2) {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskAlter')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if ($task->getTaskStatus() < Common\Enums\TaskStatusEnum::IN_PROGRESS) {
                if (isset($post['title']) && $post['title'] != "") {
                    $task->setTitle(mb_substr($post['title'], 0, 128));
                }

                if (isset($post['publishTask'])) {
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);
                }

                if (!empty($post['restrictTask'])) {
                    $taskDao->setRestrictedTask($task_id);
                } else {
                    $taskDao->removeRestrictedTask($task_id);
                }

                $targetLocale = new Common\Protobufs\Models\Locale();

                if (isset($post['target']) && $post['target'] != "") {
                    $targetLocale->setLanguageCode($post['target']);
                }

                if (isset($post['targetCountry']) && $post['targetCountry'] != "") {
                    $targetLocale->setCountryCode($post['targetCountry']);
                }

                if (!$memsource_task) $task->setTargetLocale($targetLocale);
            }

            if ($site_admin || $task->getTaskStatus() < Common\Enums\TaskStatusEnum::IN_PROGRESS) {
                if (isset($post['word_count']) && ctype_digit($post['word_count'])) {
                    $task->setWordCount($post['word_count']);
                    $projectDao->queue_asana_project($task->getProjectId());
                } elseif (isset($post['word_count']) && $post['word_count'] != "") {
                    $word_count_err = Lib\Localisation::getTranslation('task_alter_6');
                } else {
                    $word_count_err = Lib\Localisation::getTranslation('task_alter_7');
                }
            }

            if (isset($post['deadline']) && $post['deadline'] != "") {
                if ($validTime = Lib\TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);
                    $task->setDeadline($date);
                    if ($task->getTaskStatus() != Common\Enums\TaskStatusEnum::COMPLETE) {
                        $userDao = new DAO\UserDao();
                        $userDao->set_dateDue_in_memsource($task, $memsource_task, $date);
                    }
                } else {
                    $deadlineError = Lib\Localisation::getTranslation('task_alter_8');
                }
            }

            if (isset($post['impact']) && $post['impact'] != "") {
                $task->setComment($post['impact']);
            }

          if (!$memsource_task) {
            if ($word_count_err == "" && $deadlineError == "") {
                $selectedPreReqs = array();
                if (isset($post['totalTaskPreReqs']) && $post['totalTaskPreReqs'] > 0) {
                    for ($i = 0; $i < $post['totalTaskPreReqs']; $i++) {
                        if (isset($post["preReq_$i"])) {
                            $selectedPreReqs[] = $post["preReq_$i"];
                        }
                    }
                }

                $oldPreReqs = $taskPreReqIds[$task->getId()];
                $thisTaskPreReqs = null;
                if (count($selectedPreReqs) > 0) {
                    $thisTaskPreReqs = array();
                    foreach ($selectedPreReqs as $preReq) {
                        if (is_numeric($preReq)) {
                            $thisTaskPreReqs[] = $preReq;
                        }
                    }
                }

                if ($task->getTaskStatus() > Common\Enums\TaskStatusEnum::PENDING_CLAIM) {
                    // The Template has checkboxes disabled, so they will never be sucessfully sent in POST
                    // So no not overwrite existing $taskPreReqIds for current Task
                    error_log('Existing Task taskPreReqIds: ' . implode (', ', $oldPreReqs));
                }
                else {
                    $taskPreReqIds[$task->getId()] = $thisTaskPreReqs;
                }

                $graphBuilder = new Lib\UIWorkflowBuilder();
                $graph = $graphBuilder->parseAndBuild($taskPreReqIds);

                if ($graph) {

                    $index = $graphBuilder->find($task->getId(), $graph);
                    $node = $graph->getAllNodes($index);
                    $selectedList = array();
                    foreach ($node->getPrevious() as $prevId) {
                        $selectedList[] = $prevId;
                    }

                    error_log("taskAlter (graphBuilder)");
                    $taskDao->updateTask($task);
                    if ($preReqTasks) {
                        foreach ($preReqTasks as $preReqTask) {
                            if (!in_array($preReqTask->getId(), $selectedList)) {
                                $taskDao->removeTaskPreReq($task->getId(), $preReqTask->getId());
                                $task = $taskDao->getTask($task->getId()); // Trigger will probably have changed status
                            }
                        }
                    }

                    foreach ($selectedList as $taskId) {
                        if (is_numeric($taskId)) {
                            $taskDao->addTaskPreReq($task->getId(), $taskId);
                            $task = $taskDao->getTask($task->getId()); // Trigger will probably have changed status
                        }
                    }

                    error_log("taskAlter (addTaskPreReq)");
                    $taskDao->updateTask($task);

                    if ($adminAccess && ($task->getTaskStatus() <= Common\Enums\TaskStatusEnum::PENDING_CLAIM) && !empty($post['required_qualification_level'])) {
                        $taskDao->updateRequiredTaskQualificationLevel($task_id, $post['required_qualification_level']);
                    }

                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)));
                } else {
                    //A deadlock occured
                    $deadlockError = Lib\Localisation::getTranslation('task_alter_9');
                    //Reset prereqs so as not to crash second run of the graph builder
                    $taskPreReqIds[$task->getId()] = $oldPreReqs;
                }
            }
          } else {
                $taskDao->updateTask($task);

                if ($adminAccess && ($task->getTaskStatus() <= Common\Enums\TaskStatusEnum::PENDING_CLAIM) && !empty($post['required_qualification_level'])) {
                    $taskDao->updateRequiredTaskQualificationLevel($task_id, $post['required_qualification_level']);
                }
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)));
          }
        }

        if (!$memsource_task) {
        $graphBuilder = new Lib\UIWorkflowBuilder();
        //Maybe replace with an API call
        $graph = $graphBuilder->parseAndBuild($taskPreReqIds);

        if ($graph) {

            $index = $graphBuilder->find($task_id, $graph);
            $node = $graph->getAllNodes($index);

            $currentRow = $node->getPrevious();
            $previousRow = array();

            while (!empty($currentRow) && count($currentRow) > 0) {
                foreach ($currentRow as $nodeId) {
                    $index = $graphBuilder->find($nodeId, $graph);
                    $node = $graph->getAllNodes($index);
                    $tasksEnabled[$node->getTaskId()] = false;

                    foreach ($node->getPrevious() as $prevIndex) {
                        if (!in_array($prevIndex, $previousRow)) {
                            $previousRow[] = $prevIndex;
                        }
                    }
                }
                $currentRow = $previousRow;
                $previousRow = array();
            }
        }
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();

        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        if (!$memsource_task) {
        $languages = Lib\TemplateHelper::getLanguageList();
        $countries = Lib\TemplateHelper::getCountryList();
        } else {
            $languages = [];
            $countries = [];
        }

        $publishStatus="";
        if ($task->getPublished())
        {
            $publishStatus="checked";
        }

        $restrictTaskStatus = '';
        if ($taskDao->getRestrictedTask($task_id)) {
            $restrictTaskStatus = 'checked';
        }

        $template_data = array_merge($template_data, array(
            'sesskey'             => $sesskey,
            "project"             => $project,
            "extra_scripts"       => $extra_scripts,
            "languages"           => $languages,
            "countries"           => $countries,
            "projectTasks"        => $projectTasks,
            "thisTaskPreReqIds"   => $thisTaskPreReqIds,
            "tasksEnabled"        => $tasksEnabled,
            "word_count_err"      => $word_count_err,
            "deadlockError"       => $deadlockError,
            "deadline_error"      => $deadlineError,
            "publishStatus"      => $publishStatus,
            'showRestrictTask'    => $taskDao->organisationHasQualifiedBadge($project->getOrganisationId()),
            'restrictTaskStatus'  => $restrictTaskStatus,
            'site_admin'          => $site_admin,
            'adminAccess'         => $adminAccess,
            'required_qualification_level' => $taskDao->getRequiredTaskQualificationLevel($task_id),
            'allow_downloads'     => $allow_downloads,
            "taskTypeColours"     => $taskTypeColours
        ));

        UserRouteHandler::render("task/task.alter.tpl", $response);
        return $response;
    }

    public function taskView(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $adminDao = new DAO\AdminDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $isSiteAdmin = $adminDao->isSiteAdmin($user_id);

        if ($taskDao->isUserRestrictedFromTask($task_id, $user_id)) {
            UserRouteHandler::flash('error', "You are not authorized to view this page");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $user = $userDao->getUser($user_id);

        $memsource_task = $projectDao->get_memsource_task($task_id);
        if ($memsource_task) list ($matecat_id_job, $matecat_id_job_password, $recorded_status) = [0, '', ''];
        else                 list ($matecat_id_job, $matecat_id_job_password, $recorded_status) = $taskDao->get_matecat_job_id_recorded_status($task);

        $trackTaskView = $taskDao->recordTaskView($task_id,$user_id);

        $siteLocation = Common\Lib\Settings::get("site.location");

        if ($parent_translation_id = $taskDao->get_parent_transation_task($task)) {
            $task_file_info = $taskDao->getTaskInfo($parent_translation_id, 0);
            $file_path = "{$siteLocation}task/" . $this->encrypt_task_id($parent_translation_id) . '/download-task-external/';
            $chunked_message = '(each translator will work on a specific chunk)';
        } else {
            $task_file_info = $taskDao->getTaskInfo($task_id, 0);
            $file_path = "{$siteLocation}task/" . $this->encrypt_task_id($task_id) . '/download-task-external/';
            $chunked_message = '';
        }

        $template_data = array_merge($template_data, array(
            "file_preview_path" => $file_path,
            'chunked_message' => $chunked_message,
            "filename" => $task_file_info->getFilename()
        ));


        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskView')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['published'])) {
                if ($post['published']) {
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);
                }
                error_log("taskView");
                if ($taskDao->updateTask($task)) {
                    if ($post['published']) {
                        UserRouteHandler::flashNow("success", Lib\Localisation::getTranslation('task_view_1'));
                    } else {
                        UserRouteHandler::flashNow("success", Lib\Localisation::getTranslation('task_view_2'));
                    }
                } else {
                    if ($post['published']) {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_view_3'));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_view_4'));
                    }
                }
            }

            if (isset($post['track'])) {
                if ($post['track'] == "Ignore") {
                    $response_dao = $userDao->untrackTask($user_id, $task->getId());
                    if ($response_dao) {
                        UserRouteHandler::flashNow("success", Lib\Localisation::getTranslation('task_view_12'));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_view_13'));
                    }
                } else {
                    $response_dao = $userDao->trackTask($user_id, $task->getId());
                    if ($response_dao) {
                        UserRouteHandler::flashNow("success", Lib\Localisation::getTranslation('task_view_10'));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_view_11'));
                    }
                }
            }

            if (isset($post['trackOrganisation'])) {
                $org_id = $project->getOrganisationId();
                if ($post['trackOrganisation']) {
                    $userTrackOrganisation = $userDao->trackOrganisation($user_id, $org_id);
                    if ($userTrackOrganisation) {
                        UserRouteHandler::flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_success')
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_track_error')
                        );
                    }
                } else {
                    $userUntrackOrganisation = $userDao->unTrackOrganisation($user_id, $org_id);
                    if ($userUntrackOrganisation) {
                        UserRouteHandler::flashNow(
                            "success",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_success')
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            Lib\Localisation::getTranslation('org_public_profile_org_untrack_error')
                        );
                    }
                }
            }
            if (isset($post['treat_as_translated']) && $isSiteAdmin && !empty($matecat_id_job)) {
                if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION) {
                    $recorded_status = 'translated';
                } else {
                    $recorded_status = 'approved';
                }
                $taskDao->insertMatecatRecordedJobStatus($matecat_id_job, $matecat_id_job_password, $recorded_status);
                UserRouteHandler::flashNow('success', "Task will be treated as fully $recorded_status in Kató TM.");
            }
        }

        $taskMetaData = array();
        $metaData = array();
        $registered = $userDao->isSubscribedToTask($user_id, $task_id);
        if ($registered == 1) {
            $metaData["tracking"] = true;
        } else {
            $metaData["tracking"] = false;
        }
        $taskMetaData[$task_id] = $metaData;

        $template_data = array_merge($template_data, array(
                     "task" => $task,
                     "taskMetaData" => $taskMetaData
        ));

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();

        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        if ($isOrgMember || $isSiteAdmin) {
            $template_data = array_merge($template_data, array("isOrgMember" => $isOrgMember));
        }
        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($user_id, $project->getOrganisationId());

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView1.js");
        $alsoViewedTasksCount = 0; 

        $template_data = array_merge($template_data, array(
                'sesskey' => $sesskey,
                "extra_scripts" => $extra_scripts,
                "org" => $org,
                "project" => $project,
                "registered" => $registered,
                "taskTypeColours" => $taskTypeColours,
                "isMember" => $isOrgMember,
                "isSiteAdmin" => $isSiteAdmin,
                'alsoViewedTasksCount' => $alsoViewedTasksCount,
                'discourse_slug' => $projectDao->discourse_parameterize($project),
                'memsource_task' => $memsource_task,
                'matecat_url' => $taskDao->get_matecat_url_regardless($task, $memsource_task),
                'recorded_status' => $recorded_status,
                'display_treat_as_translated' => !empty($matecat_id_job) && empty($taskDao->is_parent_of_chunk($task->getProjectId(), $task_id)),
                'this_is_id' => 0,
                "userSubscribedToOrganisation" => $userSubscribedToOrganisation
        ));

        UserRouteHandler::render("task/task.view.tpl", $response);
        return $response;
    }

    public function task_search_translators(Request $request, Response $response, $args)
    {
        global $template_data;
        $task_id = $args['task_id'];

        $taskDao    = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $task       = $taskDao->getTask($task_id);
        $project    = $projectDao->getProject($task->getProjectId());

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $memsource_task = $projectDao->get_memsource_task($task_id);

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $invites_not_sent = $taskDao->list_task_invites_not_sent($task_id);
        $users_in_invites_not_sent = array();
        foreach ($invites_not_sent as $user) {
            $users_in_invites_not_sent[$user['user_id']] = $user;
        }

        $invites_not_sent_tags = $taskDao->list_task_invites_not_sent_tags($task_id);
        $users_in_invites_not_sent_tags = array();
        foreach ($invites_not_sent_tags as $user) {
            $users_in_invites_not_sent_tags[$user['user_id']] = $user;
        }

        $all_users = array();
        $invites_not_sent_words = $taskDao->list_task_invites_not_sent_words($task_id);
        $users_in_invites_not_sent_words = array();
        // $all_users first has those with highest word count (assuming they were not already invited)
        foreach ($invites_not_sent_words as $user) {
            $users_in_invites_not_sent_words[$user['user_id']] = $user;

            if (!empty($users_in_invites_not_sent[$user['user_id']])) {
                $user['display_name']         = $users_in_invites_not_sent[$user['user_id']]['display_name'];
                $user['email']                = $users_in_invites_not_sent[$user['user_id']]['email'];
                $user['first_name']           = $users_in_invites_not_sent[$user['user_id']]['first_name'];
                $user['last_name']            = $users_in_invites_not_sent[$user['user_id']]['last_name'];
                $user['level']                = $users_in_invites_not_sent[$user['user_id']]['level'];
                $user['language_name_native'] = $users_in_invites_not_sent[$user['user_id']]['language_name_native'];
                $user['country_name_native']  = $users_in_invites_not_sent[$user['user_id']]['country_name_native'];
                if (!empty($users_in_invites_not_sent_tags[$user['user_id']])) {
                    $user['user_liked_tags'] = $users_in_invites_not_sent_tags[$user['user_id']]['user_liked_tags'];
                } else {
                    $user['user_liked_tags'] = '';
                }
                $all_users[] = $user;
            }
        }

        // $all_users then has the remaining ones
        foreach ($invites_not_sent as $user) {
            if (empty($users_in_invites_not_sent_words[$user['user_id']])) {
                $user['words_delivered'] = '';
                $user['words_delivered_last_3_months'] = '';
                if (!empty($users_in_invites_not_sent_tags[$user['user_id']])) {
                    $user['user_liked_tags'] = $users_in_invites_not_sent_tags[$user['user_id']]['user_liked_tags'];
                } else {
                    $user['user_liked_tags'] = '';
                }
                $all_users[] = $user;
            }
        }

        $extra_scripts  = file_get_contents(__DIR__."/../js/TaskView1.js");
        $extra_scripts .= "
    <link rel=\"stylesheet\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css\"/>
    <script type=\"text/javascript\" src=\"https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js\"></script>
    <script type=\"text/javascript\">
      $(document).ready(function(){
        $('#myTable').DataTable(
          {
            \"paging\": false
          }
        );
      });
    </script>";

        $template_data = array_merge($template_data, array(
            'sesskey'         => $sesskey,
            'extra_scripts'   => $extra_scripts,
            'task'            => $task,
            'other_task_ids'  => $taskDao->getOtherPendingChunks($task_id),
            'project'         => $project,
            'taskTypeColours' => $taskTypeColours,
            'isSiteAdmin'     => 1,
            'isMember'        => 1,
            'discourse_slug'  => $projectDao->discourse_parameterize($project),
            'memsource_task'  => $memsource_task,
            'matecat_url'     => $taskDao->get_matecat_url_regardless($task, $memsource_task),
            'required_qualification_for_details' => $taskDao->getRequiredTaskQualificationLevel($task_id),
            'sent_users'      => $taskDao->list_task_invites_sent($task_id),
            'all_users'       => $all_users,
        ));

        UserRouteHandler::render("task/task.search_translators.tpl", $response);
        return $response;
    }

    public function task_invites_sent(Request $request, Response $response, $args)
    {
        $task_id = $args['task_id'];
        $sesskey = $args['sesskey'];

        $taskDao = new DAO\TaskDao();

        if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($sesskey, 'task_invites_sent')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

        $user_ids = (string)$request->getBody();
        $insert = '';
        $comma = '';
        if (!empty($user_ids)) {
            $user_ids_array = explode(',', $user_ids);
            foreach ($user_ids_array as $user_id) {
                $user_id = (int)$user_id;
                if ($user_id <= 1) break;
                $insert .= "$comma($task_id,$user_id,NOW())";
                $comma = ',';
            }
            if (!empty($insert)) $taskDao->insert_task_invite_sent_to_users($insert);
        }

        // If this is a chunked task, the invites will have included other tasks
        $other_task_ids = $taskDao->getOtherPendingChunks($task_id);
        foreach ($other_task_ids as $task_id) {
            $insert = '';
            $comma = '';
            if (!empty($user_ids)) {
                foreach ($user_ids_array as $user_id) {
                    $user_id = (int)$user_id;
                    if ($user_id <= 1) break;
                    $insert .= "$comma($task_id,$user_id,NOW())";
                    $comma = ',';
                }
                if (!empty($insert)) $taskDao->insert_task_invite_sent_to_users($insert);
            }
        }
    }

    public function taskCreate(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $project_id = $args['project_id'];

        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if ($projectDao->get_memsource_project($project_id)) return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', ['project_id' => $project_id]));

        $titleError = null;
        $wordCountError = null;
        $deadlineError = null;
        $taskPreReqs = array();
        $task = new Common\Protobufs\Models\Task();
        $project = $projectDao->getProject($project_id);
        $projectTasks = $projectDao->getProjectTasks($project_id);
        $task->setProjectId($project_id);

        if ($post = $request->getParsedBody()) {
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskCreate')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['title'])) {
                $task->setTitle(mb_substr($post['title'], 0, 128));
            } else {
                $titleError = Lib\Localisation::getTranslation('task_create_5');
            }

            if (isset($post['comment'])) {
                $task->setComment($post['comment']);
            }

            $projectSourceLocale = $project->getSourceLocale();
            $taskSourceLocale = new Common\Protobufs\Models\Locale();
            $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
            $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
            $task->setSourceLocale($taskSourceLocale);
            $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);

            $taskTargetLocale = new Common\Protobufs\Models\Locale();
            if (isset($post['targetLanguage'])) {
                $taskTargetLocale->setLanguageCode($post['targetLanguage']);
            }
            if (isset($post['targetCountry'])) {
                $taskTargetLocale->setCountryCode($post['targetCountry']);
            }
            $task->setTargetLocale($taskTargetLocale);

            if (isset($post['taskType'])) {
                $task->setTaskType($post['taskType']);
            }

            if (ctype_digit($post['word_count'])) {
                $task->setWordCount($post['word_count']);
            } elseif ($post['word_count'] != "") {
                $wordCountError = Lib\Localisation::getTranslation('task_alter_6');
            } else {
                $wordCountError = Lib\Localisation::getTranslation('task_alter_7');
            }

            if (isset($post['deadline'])) {
                if ($validTime = Lib\TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);
                    $task->setDeadline($date);
                } else {
                    $deadlineError = Lib\Localisation::getTranslation('task_alter_8');
                }
            }

            if (isset($post['published'])) {
                $task->setPublished(1);
            } else {
                $task->setPublished(0);
            }

            if (is_null($titleError) && is_null($wordCountError) && is_null($deadlineError)) {
                error_log("taskCreate");
                $newTask = $taskDao->createTask($task);
                $newTaskId = $newTask->getId();

                if (!empty($post['restrictTask'])) {
                    $taskDao->setRestrictedTask($newTaskId);
                } else {
                    $taskDao->removeRestrictedTask($newTaskId);
                }

                $upload_error = null;
                try {
                    $upload_error = $taskDao->saveTaskFileFromProject(
                        $newTaskId,
                        $user_id,
                        $projectDao->getProjectFile($project_id)
                    );
                } catch (\Exception  $e) {
                    $upload_error = Lib\Localisation::getTranslation('task_simple_upload_7') . $e->getMessage();
                }

                if (isset($post['totalTaskPreReqs']) && $post['totalTaskPreReqs'] > 0) {
                    for ($i = 0; $i < $post['totalTaskPreReqs']; $i++) {
                        if (isset($post["preReq_$i"])) {
                            $taskDao->addTaskPreReq($newTaskId, $post["preReq_$i"]);
                        }
                    }
                }

                if (is_null($upload_error)) {
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-created", array("task_id" => $newTaskId)));
                } else {
                    $taskDao->deleteTask($newTaskId);
                    $template_data = array_merge($template_data, array("upload_error" => $upload_error));
                }
            }
        }


        $languages = Lib\TemplateHelper::getLanguageList();
        $countries = Lib\TemplateHelper::getCountryList();

        $taskTypes = array();
        $taskTypes[Common\Enums\TaskTypeEnum::SEGMENTATION] = "Segmentation";
        $taskTypes[Common\Enums\TaskTypeEnum::TRANSLATION] = "Translation";
        $taskTypes[Common\Enums\TaskTypeEnum::PROOFREADING] = "Proofreading";
        $taskTypes[Common\Enums\TaskTypeEnum::DESEGMENTATION] = "Desegmentation";

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();

        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $extra_scripts = "
<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\"></script>
<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/DeadlinePicker.js\"></script>
";

        $task_word_count = $task->getWordCount();
        if (empty($task_word_count) && $project->getWordCount() > 1) $task->setWordCount($project->getWordCount());

        $template_data = array_merge($template_data, array(
            'sesskey'       => $sesskey,
            "project"       => $project,
            "task"          => $task,
            "projectTasks"  => $projectTasks,
            "taskPreReqs"   => $taskPreReqs,
            "languages"     => $languages,
            "countries"     => $countries,
            "taskTypes"     => $taskTypes,
            "extra_scripts" => $extra_scripts,
            "titleError"    => $titleError,
            "wordCountError"=> $wordCountError,
            "deadlineError" => $deadlineError,
            'showRestrictTask' => $taskDao->organisationHasQualifiedBadge($project->getOrganisationId()),
            "taskTypeColours" => $taskTypeColours
        ));

        UserRouteHandler::render("task/task.create.tpl", $response);
        return $response;
    }

    public function taskCreated(Request $request, Response $response, $args)
    {
        global $template_data;
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $task = $taskDao->getTask($taskId);
        $template_data = array_merge($template_data, array(
                "project_id" => $task->getProjectId(),
                "task_id"    => $task->getId()
        ));

        UserRouteHandler::render("task/task.created.tpl", $response);
        return $response;
    }

    public function taskOrgFeedback(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $task_id = $args['task_id'];

        $userDao = new DAO\UserDao();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao = new DAO\AdminDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $isSiteAdmin = $adminDao->isSiteAdmin($user_id);
        $task = $taskDao->getTask($task_id);
        $taskClaimedDate = $taskDao->getClaimedDate($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskOrgFeedback')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['feedback'])) {
                if ($post['feedback'] != "") {
                    if ($claimant != null) {
                        $taskDao->sendOrgFeedback($task_id, $user_id, $claimant->getId(), $post['feedback']);
                        UserRouteHandler::flashNow(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('task_org_feedback_6'),
                                $app->getRouteCollector()->getRouteParser()->urlFor("user-public-profile", array("user_id" => $claimant->getId())),
                                $claimant->getDisplayName()
                            )
                        );
                    }
                    if (isset($post['revokeTask']) && $post['revokeTask']) {
                        $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                        error_log("taskOrgFeedback");
                        $taskDao->updateTask($task);
                        if ($claimant != null) {
                            $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id, null);
                        } else {
                            $taskRevoke = true;
                        }
                        if ($taskRevoke) {
                            if ($claimant != null) {
                                UserRouteHandler::flash(
                                    "taskSuccess",
                                    sprintf(
                                        Lib\Localisation::getTranslation('task_org_feedback_3'),
                                        $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)),
                                        $task->getTitle(),
                                        $app->getRouteCollector()->getRouteParser()->urlFor("user-public-profile", array("user_id" => $claimant->getId())),
                                        $claimant->getDisplayName()
                                    )
                                );
                            } else {
                                UserRouteHandler::flash(
                                    "taskSuccess",
                                    sprintf(
                                        Lib\Localisation::getTranslation('task_org_feedback_3'),
                                        $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)),
                                        $task->getTitle(),
                                        '',
                                        ''
                                    )
                                );
                            }
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("project-view", array("project_id" => $task->getProjectId())));
                        } else {
                            UserRouteHandler::flashNow(
                                "error",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_org_feedback_4'),
                                    $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle(),
                                    $app->getRouteCollector()->getRouteParser()->urlFor("user-public-profile", array("user_id" => $claimant->getId())),
                                    $claimant->getDisplayName()
                                )
                            );
                        }
                    }
                } else {
                    UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('task_org_feedback_5'));
                }
            }
        }

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "project" => $project,
            "task" => $task,
            "taskClaimedDate" => $taskClaimedDate,
            "claimant" => $claimant,
            'isSiteAdmin' => $isSiteAdmin,
            "taskTypeColours" => $taskTypeColours,
            "task_tags" => $task_tags
        ));

        UserRouteHandler::render("task/task.org-feedback.tpl", $response);
        return $response;
    }

    public function taskUserFeedback(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $task_id = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);
        $taskClaimedDate = $taskDao->getClaimedDate($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $organisation = $orgDao->getOrganisation($project->getOrganisationId());
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskUserFeedback')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['feedback'])) {
                if ($post['feedback'] != '') {
                    if ($claimant != null) {
                        $taskDao->sendUserFeedback($task_id, $claimant->getId(), $post['feedback']);
                    }
                    if (isset($post['revokeTask']) && $post['revokeTask']) {
                        if ($claimant != null) {
                            $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id, $post['feedback']);
                        } else {
                            $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                            $taskDao->updateTask($task);
                            $taskRevoke = true;
                        }
                        if ($taskRevoke) {
                            UserRouteHandler::flash(
                                "success",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_user_feedback_3'),
                                    $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle()
                                )
                            );
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("home"));
                        } else {
                            UserRouteHandler::flashNow(
                                "error",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_user_feedback_4'),
                                    $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle()
                                )
                            );
                        }
                    } else {
                        $orgProfile = $app->getRouteCollector()->getRouteParser()->urlFor("org-public-profile", array('org_id' => $organisation->getId()));
                        UserRouteHandler::flash(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('task_org_feedback_6'),
                                $orgProfile,
                                $organisation->getName()
                            )
                        );
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task", array("task_id" => $task_id)));
                    }
                } else {
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('task_user_feedback_5'));
                }
            }
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "org" => $organisation,
            "project" => $project,
            "task" => $task,
            "taskClaimedDate" =>$taskClaimedDate,
            "claimant" => $claimant,
            "taskTypeColours" => $taskTypeColours,
            "task_tags" => $task_tags
        ));

        UserRouteHandler::render("task/task.user-feedback.tpl", $response);
        return $response;
    }

    public function taskReview(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $taskId = $args['task_id'];

        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $userId = Common\Lib\UserSession::getCurrentUserID();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $task = $taskDao->getTask($taskId);
        $action = "";
        switch ($task->getTaskType()) {
            case Common\Enums\TaskTypeEnum::SEGMENTATION:
                $action = Lib\Localisation::getTranslation('task_review_segmented');
                break;
            case Common\Enums\TaskTypeEnum::TRANSLATION:
                $action = Lib\Localisation::getTranslation('task_review_translated');
                break;
            case Common\Enums\TaskTypeEnum::PROOFREADING:
                $action = Lib\Localisation::getTranslation('task_review_proofread');
                break;
            case Common\Enums\TaskTypeEnum::DESEGMENTATION:
                $action = Lib\Localisation::getTranslation('task_review_merged');
                break;
        }

        $reviews = array();
        $preReqTasks = $taskDao->getTaskPreReqs($taskId);
        if (empty($preReqTasks) && $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && !empty($matecat_tasks = $taskDao->getTaskChunk($taskId))) {
            // We are a chunk, so need to manually find the matching translation task
            $matecat_id_job          = $matecat_tasks[0]['matecat_id_job'];
            $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];
            $matching_tasks = $taskDao->getMatchingTask($matecat_id_job, $matecat_id_job_password, Common\Enums\TaskTypeEnum::TRANSLATION);
            if (!empty($matching_tasks)) {
                $dummyTask = new Common\Protobufs\Models\Task();
                $dummyTask->setId($matching_tasks[0]['id']);
                $dummyTask->setProjectId($matching_tasks[0]['projectId']);
                $dummyTask->setTitle($matching_tasks[0]['title']);
                $preReqTasks = array();
                $preReqTasks[] = $dummyTask;
                error_log('preReqTasks for chunked PROOFREADING Task... ' . print_r($preReqTasks, true));
            }
        }
        $projectDao = new DAO\ProjectDao();
        if (empty($preReqTasks) && $task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $memsource_task = $projectDao->get_memsource_task($taskId)) {
            $preReqTasks = [];
            $top_level = $projectDao->get_top_level($memsource_task['internalId']);
            $project_tasks = $projectDao->get_tasks_for_project($task->getProjectId());
            foreach ($project_tasks as $project_task) {
                if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                    if ($memsource_task['workflowLevel'] > $project_task['workflowLevel']) { // Dependent on
                        if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                            $dummyTask = new Common\Protobufs\Models\Task();
                            $dummyTask->setId($project_task['id']);
                            $dummyTask->setProjectId($task->getProjectId());
                            $dummyTask->setTitle($project_task['title']);
                            $dummyTask->setTaskType($project_task['beginIndex']);
                            $dummyTask->setTaskStatus($project_task['endIndex']);
                            $preReqTasks[] = $dummyTask;
                            error_log('preReqTasks for memsource PROOFREADING Task... ' . print_r($preReqTasks, true));
                        }
                    }
                }
            }
        }
        if ($preReqTasks == null || count($preReqTasks) == 0) {
            $projectDao = new \SolasMatch\UI\DAO\ProjectDao();
            $project = $projectDao->getProject($task->getProjectId());

            $project_reviews = $projectDao->getProjectReviews($task->getProjectId());
            if ($project_reviews) {
                foreach ($project_reviews as $projectReview) {
                    if ($projectReview->getTaskId() == null
                            && $projectReview->getUserId() == $userId) {
                        $reviews[$task->getProjectId()] = $projectReview;
                    }
                }
            }

            $dummyTask = new Common\Protobufs\Models\Task(); //Create a dummy task to hold the project info
            $dummyTask->setProjectId($task->getProjectId());
            $dummyTask->setTitle($project->getTitle());
            $preReqTasks = array();
            $preReqTasks[] = $dummyTask;
        } else {
            foreach ($preReqTasks as $pTask) {
                $taskReview = $userDao->getUserTaskReviews($userId, $pTask->getId());
                if ($taskReview) {
                    $reviews[$pTask->getId()] = $taskReview;
                }
            }
        }

        if (!empty($reviews) && count($reviews) > 0) {
            UserRouteHandler::flashNow("info", Lib\Localisation::getTranslation('task_review_4'));
        }

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskReview')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (isset($post['submitReview'])) {
                $i = 0;
                $tasks_titles = '';
                $error = null;
                while ($i < count($preReqTasks) && $error == null) {
                    $pTask = $preReqTasks[$i++];
                    $review = new Common\Protobufs\Models\TaskReview();
                    $id = $pTask->getId();
                    $tasks_titles .= $pTask->getTitle() . ' ';

                    $review->setUserId($userId);
                    if (!is_null($id)) {
                        $review->setTaskId($id);
                    }
                    $review->setProjectId($pTask->getProjectId());

                    if (is_null($id)) {
                        $id = $pTask->getProjectId();
                    }

                    if (isset($post["corrections_$id"]) && ctype_digit($post["corrections_$id"])) {
                        $value = intval($post["corrections_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setCorrections($value);
                        } else {
                            $error = Lib\Localisation::getTranslation('task_review_5');
                        }
                    }
                    if (isset($post["grammar_$id"]) && ctype_digit($post["grammar_$id"])) {
                        $value = intval($post["grammar_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setGrammar($value);
                        } else {
                            $error = Lib\Localisation::getTranslation('task_review_6');
                        }
                    }
                    if (isset($post["spelling_$id"]) && ctype_digit($post["spelling_$id"])) {
                        $value = intval($post["spelling_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setSpelling($value);
                        } else {
                            $error = Lib\Localisation::getTranslation('task_review_7');
                        }
                    }
                    if (isset($post["consistency_$id"]) && ctype_digit($post["consistency_$id"])) {
                        $value = intval($post["consistency_$id"]);
                        if ($value > 0 && $value <= 55) {
                            $review->setConsistency($value);
                        } else {
                            $error = Lib\Localisation::getTranslation('task_review_8');
                        }
                    }
                    if (isset($post["comment_$id"]) && $post["comment_$id"] != "") {
                        $review->setComment($post["comment_$id"]);
                    }

                    $review->setReviseTaskId($taskId);

                    if ($review->getProjectId() != null && $review->getUserId() != null && $error == null) {
                        $submitResult = $taskDao->submitReview($review);
                        if (!$submitResult) {
                            $error = sprintf(Lib\Localisation::getTranslation('task_review_9'), $pTask->getTitle());
                        }
                    } else {
                        if ($error != null) {
                            UserRouteHandler::flashNow("error", $error);
                        }
                    }
                }
                if ($error == null) {
                    UserRouteHandler::flash(
                        "success",
                        sprintf(Lib\Localisation::getTranslation('task_review_10'), $tasks_titles)
                    );
                    if ($taskDao->getTaskChunk($taskId)) {
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-chunk-completed', array('task_id' => $taskId)));
                    } else {
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-uploaded', array("task_id" => $taskId)));
                    }
                } else {
                    UserRouteHandler::flashNow("error", $error);
                }
            }

            if (isset($post['skip'])) {
                if ($taskDao->getTaskChunk($taskId)) {
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-chunk-completed', array('task_id' => $taskId)));
                } else {
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-uploaded', array("task_id" => $taskId)));
                }
            }
        }

        $extra_scripts = "";
        $extra_scripts .= "<script type='text/javascript'>";
        $extra_scripts .= "var taskIds = new Array();";
        $index = 0;
        foreach ($preReqTasks as $pTask) {
            if ($pTask->getId() != null) {
                $id = $pTask->getId();
            } else {
                $id = $pTask->getProjectId();
            }
            $extra_scripts .= "taskIds[$index] = $id;";
            $index++;
            $taskIds[] = $pTask->getId();
        }
        $extra_scripts .= "</script>";

        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";
        $extra_scripts .= file_get_contents(__DIR__."/../js/review.js");
        // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
        $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

        $formAction = $app->getRouteCollector()->getRouteParser()->urlFor("task-review", array('task_id' => $taskId));

        $template_data = array_merge($template_data, array(
            'sesskey'       => $sesskey,
            'extra_scripts' => $extra_scripts,
            'taskId'        => $taskId,
            'tasks'         => $preReqTasks,
            'is_chunked'    => !empty($matecat_tasks),
            'reviews'       => $reviews,
            'formAction'    => $formAction,
            'action'        => $action
        ));

        UserRouteHandler::render("task/task.review.tpl", $response);
        return $response;
    }
}

$route_handler = new TaskRouteHandler();
$route_handler->init();
unset ($route_handler);
