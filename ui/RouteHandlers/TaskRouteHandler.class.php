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
            '/user/{user_id}/recent/tasks[/]',
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

        $app->get(
            '/task/{task_id}/uploaded[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskUploaded')
            ->add('\SolasMatch\UI\Lib\Middleware:authenticateUserForTask')
            ->setName('task-uploaded');

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
            '/task/{task_id}/search_translators_any_country_no_source[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_search_translators_any_country_no_source')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-search_translators_any_country_no_source');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/search_translators_no_source[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_search_translators_no_source')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-search_translators_no_source');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/search_translators_any_country[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_search_translators_any_country')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-search_translators_any_country');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/search_translators[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_search_translators')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-search_translators');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/task_invites_sent/{sesskey}[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:task_invites_sent')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask')
            ->setName('task-invites_sent');

        $app->map(['GET', 'POST'],
            '/task/{task_id}/org-feedback[/]',
            '\SolasMatch\UI\RouteHandlers\TaskRouteHandler:taskOrgFeedback')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgTask_incl_community_officer')
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
            $response = $response->withStatus(404);
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

        $template_data = array_merge($template_data, array(
                                    'archivedTasks' => $archivedTasks,
                                    "page_no" => $page_no,
                                    "last" => $totalPages,
                                    "top" => $top,
                                    "bottom" => $bottom,
                                    "archivedTasksCount" => $archivedTasksCount
        ));
        return UserRouteHandler::render("task/archived-tasks.tpl", $response);
    }

    public function claimedTasks(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $user_id = $args['user_id'];
        $currentScrollPage  = isset($args['page_no']) ? $args['page_no'] : 1;
        $selectedTaskType   = isset($args['tt'])      ? $args['tt'] : 0;
        $selectedTaskStatus = isset($args['ts'])      ? $args['ts'] : 3;
        $selectedOrdering   = isset($args['o'])       ? $args['o'] : 0;

        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        $user = $userDao->getUser($user_id);

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if ($loggedInUserId != $user_id) {
            $adminDao = new DAO\AdminDao();
            if (!($adminDao->get_roles($loggedInUserId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) {
                UserRouteHandler::flash('error', 'You are not authorized to view this page');
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        }

        $taskStatusTexts = array();
        $taskStatusTexts[1] = Lib\Localisation::getTranslation('common_waiting');
        $taskStatusTexts[2] = Lib\Localisation::getTranslation('common_unclaimed');
        $taskStatusTexts[10] = 'Claimed';
        $taskStatusTexts[3] = Lib\Localisation::getTranslation('common_in_progress');
        $taskStatusTexts[4] = Lib\Localisation::getTranslation('common_complete');

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
        $show_memsource_approval = [];
        $matecat_urls = array();
        $shell_task_urls = [];
        $allow_downloads = array();
        $show_mark_chunk_complete = array();
        $memsource_tasks = [];
        $tasksIds  = [];

        $lastScrollPage = ceil($topTasksCount / $itemsPerScrollPage);
        if ($currentScrollPage <= $lastScrollPage) {
            foreach ($topTasks as $topTask) {
                $taskId = $topTask->getId();
                array_push($tasksIds,$taskId);
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
                $chunks = $userDao->getUserTaskChunks(...$tasksIds);

                if (!$memsource_task || $projectDao->are_translations_not_all_complete($topTask, $memsource_task)) $matecat_urls[$taskId] = '';
                else                                                                                               $matecat_urls[$taskId] = $taskDao->get_matecat_url($topTask, $memsource_task);
                if (Common\Enums\TaskTypeEnum::$enum_to_UI[$topTask->getTaskType()]['shell_task'] && ($shell_task_url = $taskDao->get_task_url($taskId))) $shell_task_urls[$taskId] = $shell_task_url;
                $allow_downloads[$taskId] = $taskDao->get_allow_download($topTask, $memsource_task);
                $show_mark_chunk_complete[$taskId] = 0;

                $discourse_slug[$taskId] = $projectDao->discourse_parameterize($project);

                $show_memsource_revision[$taskId] = null;
                $show_memsource_approval[$taskId] = null;
                if ($memsource_task) {
                    $project_tasks = $projectDao->get_tasks_for_project($topTask->getProjectId());
                    foreach ($project_tasks as $project_task) {
                        if ($taskId == $project_task['id']) $top_level = $projectDao->get_top_level($project_task['internalId']);
                    }
                    $revision_task = 0;
                    $revision_complete = 1;
                    $approval_task = 0;
                    $approval_complete = 1;
                    foreach ($project_tasks as $project_task) {
                        if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                            if ($project_task['task-type_id'] == Common\Enums\TaskTypeEnum::PROOFREADING) { // Revision
                                if (!$revision_task) $revision_task = $project_task['id'];
                                if ($project_task['task-status_id'] != Common\Enums\TaskStatusEnum::COMPLETE) $revision_complete = 0;
                            }
                            if ($project_task['task-type_id'] == Common\Enums\TaskTypeEnum::APPROVAL) { // Approval
                                if (!$approval_task) $approval_task = $project_task['id'];
                                if ($project_task['task-status_id'] != Common\Enums\TaskStatusEnum::COMPLETE) $approval_complete = 0;
                            }
                        }
                    }
                    if ($revision_task && $revision_complete) $show_memsource_revision[$taskId] = $revision_task;
                    if ($approval_task && $approval_complete) $show_memsource_approval[$taskId] = $approval_task;
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
                }
            }
        }

        if ($currentScrollPage == $lastScrollPage && ($topTasksCount % $itemsPerScrollPage != 0)) {
            $itemsPerScrollPage = $topTasksCount % $itemsPerScrollPage;
        }
        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ias.min.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Home.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\"  src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/pagination.js\" defer ></script>";

        $template_data = array_merge($template_data, array(
            'current_page' => 'claimed-tasks',
            'thisUser' => $user,
            'user_id' => $user_id,
            'siteLocation' => $siteLocation,
            'selectedTaskType' => $selectedTaskType,
            'selectedTaskStatus' => $selectedTaskStatus,
            'selectedOrdering' => $selectedOrdering,
            'topTasks' => $topTasks,
            'chunks' => $chunks,
            'taskStatusTexts' => $taskStatusTexts,
            'taskTags' => $taskTags,
            'created_timestamps' => $created_timestamps,
            'deadline_timestamps' => $deadline_timestamps,
            'completed_timestamps' => $completed_timestamps,
            'projectAndOrgs' => $projectAndOrgs,
            'matecat_urls' => $matecat_urls,
            'shell_task_urls' => $shell_task_urls,
            'allow_downloads' => $allow_downloads,
            'show_mark_chunk_complete' => $show_mark_chunk_complete,
            'discourse_slug' => $discourse_slug,
            'proofreadTaskIds' => $proofreadTaskIds,
            'parentTaskIds'    => $parentTaskIds,
            'show_memsource_revision' => $show_memsource_revision,
            'show_memsource_approval' => $show_memsource_approval,
            'memsource_tasks' => $memsource_tasks,
            'currentScrollPage' => $currentScrollPage,
            'itemsPerScrollPage' => $itemsPerScrollPage,
            'lastScrollPage' => $lastScrollPage,
            'extra_scripts' => $extra_scripts,
            
        ));
        return UserRouteHandler::render('task/claimed-tasks.tpl', $response);
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
            if (!($adminDao->get_roles($loggedInUserId) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER))) {
                UserRouteHandler::flash('error', "You are not authorized to view this page"); //need to move to strings.xml
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
            }
        }

        $taskStatusTexts = array();
        $taskStatusTexts[1] = Lib\Localisation::getTranslation('common_waiting');
        $taskStatusTexts[2] = Lib\Localisation::getTranslation('common_unclaimed');
        $taskStatusTexts[10] = 'Claimed';
        $taskStatusTexts[3] = Lib\Localisation::getTranslation('common_in_progress');
        $taskStatusTexts[4] = Lib\Localisation::getTranslation('common_complete');

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
        $tasksIds = array();

        $lastScrollPage = ceil($recentTasksCount / $itemsPerScrollPage);
        if ($currentScrollPage <= $lastScrollPage) {
            foreach ($recentTasks as $recentTask) {
                $taskId = $recentTask->getId();
                array_push($tasksIds,$taskId);
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
        $chunks =  $userDao->getUserTaskChunks(...$tasksIds);
        if ($currentScrollPage == $lastScrollPage && ($recentTasksCount % $itemsPerScrollPage != 0)) {
            $itemsPerScrollPage = $recentTasksCount % $itemsPerScrollPage;
        }
        $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ias.min.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Home.js\"></script>";
        $extra_scripts .= "<script type=\"text/javascript\"  src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/pagination.js\" defer ></script>";


        $template_data = array_merge($template_data, array(
            'current_page' => 'recent-tasks',
            'thisUser' => $user,
            'user_id' => $user_id,
            'siteLocation' => $siteLocation,
            'recentTasks' => $recentTasks,
            'chunks' => $chunks,
            'taskStatusTexts' => $taskStatusTexts,
            'taskTags' => $taskTags,
            'created_timestamps' => $created_timestamps,
            'deadline_timestamps' => $deadline_timestamps,
            'projectAndOrgs' => $projectAndOrgs,
            'currentScrollPage' => $currentScrollPage,
            'itemsPerScrollPage' => $itemsPerScrollPage,
            'lastScrollPage' => $lastScrollPage,
            'extra_scripts' => $extra_scripts,
        ));

        return UserRouteHandler::render('task/recent-tasks.tpl', $response);

    
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
                return $this->downloadTaskVersion($request, $response, $args);
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

        return $response->withStatus(302)->withHeader('Location', $request->getHeaderLine('REFERER'));
    }

    public function downloadTask(Request $request, Response $response, $args)
    {
        global $app;

        try {
            return $this->downloadTaskVersion($request, $response, $args);
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
                    if (!empty($val)) $response = $response->withHeader($key, $val);
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
        $adminDao = new DAO\AdminDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $taskClaimed = $taskDao->isTaskClaimed($taskId);
        if ($taskClaimed) { // Protect against someone inappropriately creating URL for this route
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-view', array('task_id' => $taskId)));
        }

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());
        $roles = $adminDao->get_roles($user_id, $project->getOrganisationId());
        if (!($roles&(SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | LINGUIST | NGO_LINGUIST)) || $taskDao->isUserRestrictedFromTask($taskId, $user_id) || !$taskDao->user_within_limitations($user_id, $taskId)) {
            UserRouteHandler::flash('error', "You are not authorized to view this page");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $memsource_task = $projectDao->get_memsource_task($taskId);

        if (Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']) {
            UserRouteHandler::flash('error', 'This type of task cannot be claimed');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
//        if (!$task->getPublished() && !$adminDao->isSiteAdmin($user_id)) {
//            UserRouteHandler::flash('error', 'This task is not published');
//            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
//        }

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskClaim')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            $user_id = Common\Lib\UserSession::getCurrentUserID();

            $success = $userDao->claimTask($user_id, $taskId, $memsource_task, $task->getProjectId(), $task);
            if ($success == 1) {
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-claimed', array('task_id' => $taskId)));
            } elseif ($success == -1) {
                UserRouteHandler::flashNow('error', 'Unable to create user in Phrase TMS.');
            } else {
                UserRouteHandler::flashNow('error', 'This task can no longer be claimed, the job has been removed from Phrase TMS and will soon be removed from here.');
            }
        }

        $sourcelocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $taskMetaData = $taskDao->getTaskInfo($taskId);

        // Used in proofreading page, link to original project file
        $projectFileDownload = $app->getRouteCollector()->getRouteParser()->urlFor("home")."project/".$task->getProjectId()."/file";


        $template_data = array_merge($template_data, array(
                    'sesskey' => $sesskey,
                    "projectFileDownload" => $projectFileDownload,
                    "task"          => $task,
                    'matecat_url'   => '',
                    'allow_download'=> $taskDao->get_allow_download($task, $memsource_task),
                    'memsource_task'=> $memsource_task,
                    "taskMetadata"  => $taskMetaData
        ));

        return UserRouteHandler::render("task/task.claim.tpl", $response);
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
            'isSiteAdmin'    => $adminDao->get_roles(Common\Lib\UserSession::getCurrentUserID()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER),
        ));

        return UserRouteHandler::render("task/task.claimed.tpl", $response);
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
                if (!empty($val)) $response = $response->withHeader($key, $val);
            }
        }
        return $response;
    }

    public function task(Request $request, Response $response, $args)
    {
        global $app;
        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-view', ['task_id' => $args['task_id']]));
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

        return UserRouteHandler::render("task/task.uploaded.tpl", $response);
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
        $deadlineError = "";

        $extra_scripts = "
        <script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\"></script>
        <script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/datetimepicker.js\" defer ></script>";

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

        $roles = $adminDao->get_roles(Common\Lib\UserSession::getCurrentUserID(), $project->getOrganisationId());

        $copy_task = unserialize(serialize($task));

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

            if ($roles & (SITE_ADMIN | PROJECT_OFFICER)) {
                if (isset($post['word_count']) && ctype_digit($post['word_count']) && $post['word_count_partner_weighted'] && ctype_digit($post['word_count_partner_weighted'])) 
                {
                    $task->setWordCount($post['word_count']);
                    $task->set_word_count_partner_weighted($post['word_count_partner_weighted']);
                    $projectDao->queue_asana_project($task->getProjectId());
                }
                 elseif (isset($post['word_count_partner_weighted']) && $post['word_count_partner_weighted'] != "" || isset($post['word_count']) && $post['word_count'] != "" ) {
                    $word_count_err = Lib\Localisation::getTranslation('task_alter_6');
                } else {
                    $word_count_err = Lib\Localisation::getTranslation('task_alter_7');
                }
            }

            if (isset($post['deadline']) && $post['deadline'] != "") {
                if ($validTime = Lib\TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);

                   if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) || $date >= $task->getDeadline()) {
                    $task->setDeadline($date);
                    if ($task->getTaskStatus() != Common\Enums\TaskStatusEnum::COMPLETE) {
                        $userDao = new DAO\UserDao();
                        $userDao->set_dateDue_in_memsource($task, $memsource_task, $date);
                    }
                   } else {
                    $deadlineError = 'You may not tighten the deadline.';
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
            }
          } else {
              if (empty($word_count_err) && empty($deadlineError)) {
                $taskDao->updateTask($task);

                if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && ($task->getTaskStatus() <= Common\Enums\TaskStatusEnum::PENDING_CLAIM) && !empty($post['required_qualification_level'])) {
                    $taskDao->updateRequiredTaskQualificationLevel($task_id, $post['required_qualification_level']);
                }
                if (($roles & (SITE_ADMIN | PROJECT_OFFICER) || in_array($project->getOrganisationId(), ORG_EXCEPTIONS) && $roles & (NGO_ADMIN + NGO_PROJECT_OFFICER)) && !empty($post['shell_task_url'])) {
                    $url = $post['shell_task_url'];
                    if (!preg_match('#^(http|https)://#i', $url)) $url = "https://$url";
                    if ($taskDao->get_task_url($task_id)) $taskDao->update_task_url($task_id, $url);
                    else                                  $taskDao->insert_task_url($task_id, $url);
                }
                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor("task-view", array("task_id" => $task_id)));
              }
          }
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

        if ($copy_task->getTaskStatus() == Common\Enums\TaskStatusEnum::IN_PROGRESS && $projectDao->are_translations_not_all_complete($copy_task, $memsource_task)) $copy_task->setTaskStatus(Common\Enums\TaskStatusEnum::CLAIMED);

        $template_data = array_merge($template_data, array(
            'task'                => $copy_task,
            'sesskey'             => $sesskey,
            "project"             => $project,
            "extra_scripts"       => $extra_scripts,
            "languages"           => $languages,
            "countries"           => $countries,
            "projectTasks"        => $projectTasks,
            "thisTaskPreReqIds"   => $thisTaskPreReqIds,
            "tasksEnabled"        => $tasksEnabled,
            "word_count_err"      => $word_count_err,
            "deadline_error"      => $deadlineError,
            "publishStatus"      => $publishStatus,
            'showRestrictTask'    => $taskDao->organisationHasQualifiedBadge($project->getOrganisationId()),
            'restrictTaskStatus'  => $restrictTaskStatus,
            'roles'               => $roles,
            'required_qualification_level' => $taskDao->getRequiredTaskQualificationLevel($task_id),
            'shell_task_url'      => $taskDao->get_task_url($task_id),
            'allow_downloads'     => $allow_downloads,
        ));

        return UserRouteHandler::render("task/task.alter.tpl", $response);
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

        $task = $taskDao->getTask($task_id);
        if (is_null($task)) {
            UserRouteHandler::flash('error', sprintf(Lib\Localisation::getTranslation('task_view_5'), $task_id));
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
        if ($taskDao->isUserRestrictedFromTask($task_id, $user_id)) {
            UserRouteHandler::flash('error', "You are not authorized to view this page");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $project = $projectDao->getProject($task->getProjectId());
        $org_id = $project->getOrganisationId();
        $roles = $adminDao->get_roles($user_id, $org_id);
        $memsource_task = $projectDao->get_memsource_task($task_id);
        $trackTaskView = $taskDao->recordTaskView($task_id, $user_id);

        $siteLocation = Common\Lib\Settings::get('site.location');
        if (!Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']) {
        $task_file_info = $taskDao->getTaskInfo($task_id);
        $file_path = "{$siteLocation}task/" . $this->encrypt_task_id($task_id) . '/download-task-external/';
        $template_data = array_merge($template_data, array(
            "file_preview_path" => $file_path,
            "filename" => $task_file_info->getFilename()
        ));
        } else $template_data['file_preview_path'] = '';

        $taskClaimed = $taskDao->isTaskClaimed($task_id);
        if ($taskClaimed) $details_claimant = $taskDao->getUserClaimedTask($task_id);
        else              $details_claimant = 0;

        $paid_status = $taskDao->get_paid_status($task_id);

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'taskView')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) && isset($post['published'])) {
                if ($post['published']) {
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);
                }
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

            if (($roles  & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['paid_status'])) {
                if ($post['paid_status'] == 2) {
                    $taskDao->set_paid_status($task_id);
                    UserRouteHandler::flashNow('success', 'The task is now marked as paid.');
                } else {
                    $taskDao->clear_paid_status($task_id);
                    UserRouteHandler::flashNow('success', 'The task is now marked as unpaid.');
                }
                $paid_status = $taskDao->get_paid_status($task_id);
            }

            if (!$taskClaimed && ($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) && ((isset($post['userIdOrEmail']) && trim($post['userIdOrEmail']) != '') || !empty($post['assignUserSelect']))) {
                if (!empty($post['assignUserSelect'])) $emailOrUserId = $post['assignUserSelect'];
                else                                   $emailOrUserId = trim($post['userIdOrEmail']);
                $userToBeAssigned = null;
                $errorOccured = False;
                if (ctype_digit($emailOrUserId)) { //checking for intergers in a string (user id)
                    $userToBeAssigned = $userDao->getUser($emailOrUserId);
                    if (is_null($userToBeAssigned)) {
                        UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('task_view_assign_id_error'));
                        $errorOccured = True;
                    }
                } else if (Lib\Validator::validateEmail($emailOrUserId)) {
                    $userToBeAssigned = $userDao->getUserByEmail($emailOrUserId);
                    if (is_null($userToBeAssigned)) {
                        $errorOccured = True;
                        UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('task_view_assign_email_error'));
                    }
                } else {
                    $errorOccured = True;
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('task_view_assign_id_or_email_error'));
                }

                if (!$errorOccured && !is_null($userToBeAssigned)) {
                    $userDisplayName = $userToBeAssigned->getDisplayName();
                    $assgneeId = $userToBeAssigned->getId();
                    $isUserBlackListedForTask = $userDao->isBlacklistedForTask($assgneeId, $task_id);
                    if ($isUserBlackListedForTask) {
                        UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('task_view_assign_task_banned_error'), $userDisplayName));
                    } else {
                      if (Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']) {
                        $userDao->claimTask_shell($assgneeId, $task_id);
                        UserRouteHandler::flash('success', sprintf(Lib\Localisation::getTranslation('task_view_assign_task_success'), $userDisplayName));
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array('project_id' => $task->getProjectId())));
                      } else {
                        $success = $userDao->claimTask($assgneeId, $task_id, $memsource_task, $task->getProjectId(), $task);
                        if ($success == 1) {
                            UserRouteHandler::flash('success', sprintf(Lib\Localisation::getTranslation('task_view_assign_task_success'), $userDisplayName));
                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array('project_id' => $task->getProjectId())));
                        } elseif ($success == -1) {
                            UserRouteHandler::flashNow('error', 'Unable to create user in Phrase TMS.');
                        } else {
                            UserRouteHandler::flashNow('error', 'This task can no longer be claimed, the job has been removed from Memsource and will soon be removed from here.');
                        }
                      }
                    }
                }
                $post['userIdOrEmail'] = '';
            }
            if (!$taskClaimed && ($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) && !empty($post['userIdOrEmailDenyList'])) {
                $userIdOrEmail = trim($post['userIdOrEmailDenyList']);
                if (ctype_digit($userIdOrEmail)) $remove_deny_user = $userDao->getUser($userIdOrEmail);
                else                             $remove_deny_user = $userDao->getUserByEmail($userIdOrEmail);
                if (empty($remove_deny_user)) {
                    UserRouteHandler::flashNow('error', 'User does not exist.');
                } else {
                    $taskDao->removeUserFromTaskBlacklist($remove_deny_user->getId(), $task_id);
                    UserRouteHandler::flashNow('success', 'Removed (assuming was actually in deny list)');
                }
            }
            if ($details_claimant && ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) && isset($post['feedback'])) {
               if (!empty($post['feedback'])) {
                   $taskDao->sendOrgFeedback($task_id, $user_id, $details_claimant->getId(), $post['feedback']);
                   UserRouteHandler::flashNow(
                       'success',
                       sprintf(
                           Lib\Localisation::getTranslation('task_org_feedback_6'),
                           $app->getRouteCollector()->getRouteParser()->urlFor('user-public-profile', ['user_id' => $details_claimant->getId()]),
                           $details_claimant->getDisplayName()
                       )
                   );
                   if (!empty($post['revokeTask'])) {
                       $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                       error_log("taskView revokeTask: $task_id by $user_id");
                       $taskDao->updateTask($task);
                       $userDao->unclaimTask($details_claimant->getId(), $task_id, null);
                       UserRouteHandler::flash(
                           'taskSuccess',
                           sprintf(
                               Lib\Localisation::getTranslation('task_org_feedback_3'),
                               $app->getRouteCollector()->getRouteParser()->urlFor('task-view', ['task_id' => $task_id]),
                               $task->getTitle(),
                               $app->getRouteCollector()->getRouteParser()->urlFor('user-public-profile', ['user_id' => $details_claimant->getId()]),
                               $details_claimant->getDisplayName()
                           )
                       );
                       return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', ['project_id' => $task->getProjectId()]));
                   }
                } else {
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('task_org_feedback_5'));
                }
            }
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['mark_purchase_order'])) {
                if (is_numeric($post['purchase_order'])) {
                    if ($paid_status['purchase_order'] != (int)$post['purchase_order']) {
                        $paid_status['payment_status'] = 'Unsettled';
                        $paid_status['status_changed'] = date('Y-m-d H:i:s');
                    }
                    $paid_status['purchase_order'] = (int)$post['purchase_order'];
                    $taskDao->update_paid_status($paid_status);
                    UserRouteHandler::flashNow('success', 'Purchase Order updated.');
                } else UserRouteHandler::flashNow('error', 'Purchase Order must be an integer.');
            }
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['mark_payment_status'])) {
                if ($paid_status['payment_status'] == 'Ready for payment'     && $post['mark_payment_status'] == 'Pending documentation'
                        ||
                    $paid_status['payment_status'] == 'Pending documentation' && $post['mark_payment_status'] == 'Ready for payment'
                        ||
                    $paid_status['payment_status'] == 'Ready for payment'     && $post['mark_payment_status'] == 'Settled'
                        ||
                    $paid_status['payment_status'] == 'Unsettled'             && $post['mark_payment_status'] == 'In-kind'
                        ||
                    $paid_status['payment_status'] == 'Unsettled'             && $post['mark_payment_status'] == 'In-house'
                        ||
                    $paid_status['payment_status'] == 'Unsettled'             && $post['mark_payment_status'] == 'Waived'
                        ||
                    $paid_status['payment_status'] == 'In-kind'               && $post['mark_payment_status'] == 'Unsettled'
                        ||
                    $paid_status['payment_status'] == 'In-house'              && $post['mark_payment_status'] == 'Unsettled'
                        ||
                    $paid_status['payment_status'] == 'Waived'                && $post['mark_payment_status'] == 'Unsettled'
                        ||
                    $paid_status['payment_status'] == 'Ready for payment'     && $post['mark_payment_status'] == 'Waived')
                {
                    $paid_status['payment_status'] = $post['mark_payment_status'];
                    $paid_status['status_changed'] = date('Y-m-d H:i:s');
                    $taskDao->update_paid_status($paid_status);
                    UserRouteHandler::flashNow('success', 'Payment Status updated.');
                } else UserRouteHandler::flashNow('error', 'Payment Status Invalid.');
            }
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['mark_unit_rate'])) {
                if (is_numeric($post['unit_rate'])) {
                    $paid_status['unit_rate'] = $post['unit_rate'];
                    $updated=$taskDao->update_paid_status($paid_status);
                    UserRouteHandler::flashNow('success', 'Unit Rate updated.');
                } else UserRouteHandler::flashNow('error', 'Unit Rate must be a number.');
            }
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['mark_unit_rate_pricing'])) {
                if (is_numeric($post['unit_rate_pricing'])) {
                    $paid_status['unit_rate_pricing'] = $post['unit_rate_pricing'];
                    $taskDao->update_paid_status($paid_status);
                    UserRouteHandler::flashNow('success', 'Unit Rate Price updated.');
                } else UserRouteHandler::flashNow('error', 'Unit Rate Price must be a number.');
            }
            if (($roles & (SITE_ADMIN | PROJECT_OFFICER)) && isset($post['mark_source_quantity'])) {
                if ((int)$post['source_quantity'] > 0) {
                    $task->set_source_quantity((int)$post['source_quantity']);
                    $taskDao->updateTask($task);
                    UserRouteHandler::flashNow('success', 'Source Units updated.');
                } else UserRouteHandler::flashNow('error', 'Source Units must be a (>0) integer.');
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

        if ($task->getTaskStatus() == Common\Enums\TaskStatusEnum::IN_PROGRESS && $projectDao->are_translations_not_all_complete($task, $memsource_task)) $task->setTaskStatus(Common\Enums\TaskStatusEnum::CLAIMED);

        if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) {
            $template_data = array_merge($template_data, ['show_actions' => 1]);
        }

        $extra_scripts = file_get_contents(__DIR__."/../js/TaskView3.js");

        $alsoViewedTasks = [];
        $alsoViewedTasksCount = 0;
        $deadline_timestamps = [];
        $projectAndOrgs = [];
        $list_qualified_translators = [];

        if (!$taskClaimed) {
            $alsoViewedTasks = $taskDao->getAlsoViewedTasks($task_id, $user_id, 0);
            if (!empty($alsoViewedTasks)) $alsoViewedTasksCount = count($alsoViewedTasks);
            if (is_array($alsoViewedTasks) || is_object($alsoViewedTasks)) {
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

            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) $list_qualified_translators = $taskDao->list_qualified_translators($task_id, $org_id, $roles & (SITE_ADMIN | PROJECT_OFFICER));
        }

        if ($taskClaimed) $details_claimed_date = $taskDao->getClaimedDate($task_id);
        else              $details_claimed_date = 0;

        $taskStatusTexts = [];
        $taskStatusTexts[1] = Lib\Localisation::getTranslation('common_waiting');
        $taskStatusTexts[2] = Lib\Localisation::getTranslation('common_unclaimed');
        $taskStatusTexts[10] = 'Claimed';
        $taskStatusTexts[3] = Lib\Localisation::getTranslation('common_in_progress');
        $taskStatusTexts[4] = Lib\Localisation::getTranslation('common_complete');
        $chunks =  $userDao->getUserTaskChunks($task_id);
        $viewedTaskIds = [];   
        if(is_array($alsoViewedTasks)) $viewedTaskIds = array_map(function($element){return $element->id; },$alsoViewedTasks);
        $chunksAlsoViews =$userDao->getUserTaskChunks(...$viewedTaskIds);

        $total_expected_cost = 0;
        $total_expected_price = 0;
        if (!empty($paid_status) && $task->getWordCount() > 1) $total_expected_cost = $task->getWordCount()*$paid_status['unit_rate'];
        if (Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['divide_rate_by_60']) $total_expected_cost /= 60;        
        if (!empty($paid_status) && $task->getWordCount() > 1) $total_expected_price = $task->get_word_count_partner_weighted()*$paid_status['unit_rate_pricing'];
        if (Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['divide_rate_by_60']) $total_expected_price /= 60;
        $extra_scripts .= "<script type=\"text/javascript\"  src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/pagination.js\" defer ></script>";
        $template_data = array_merge($template_data, array(
                'sesskey' => $sesskey,
                'siteLocation' => $siteLocation,
                "extra_scripts" => $extra_scripts,
                "project" => $project,
                'task' => $task,
                'chunks' => $chunks,
                'chunksViews' => $chunksAlsoViews,
                'taskMetaData' => $taskMetaData,
                'roles'        => $roles,
                'alsoViewedTasks' => $alsoViewedTasks,
                'alsoViewedTasksCount' => $alsoViewedTasksCount,
                'deadline_timestamps' => $deadline_timestamps,
                'projectAndOrgs' => $projectAndOrgs,
                'discourse_slug' => $projectDao->discourse_parameterize($project),
                'memsource_task' => $memsource_task,
                'matecat_url' => !Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task'] ? $taskDao->get_matecat_url_regardless($task, $memsource_task) : $taskDao->get_task_url($task_id),
                'paid_status' => $paid_status,
                'total_expected_cost' => $total_expected_cost,
                'total_expected_price' => $total_expected_price,
                'taskStatusTexts' => $taskStatusTexts,
                'list_qualified_translators' => $list_qualified_translators,
                'details_claimed_date' => $details_claimed_date,
                'details_claimant' => $details_claimant,
                'org_id' => $org_id,
                'memsource_task' => $memsource_task,
                'is_denied_for_task' => $userDao->is_denied_for_task($user_id, $task_id),
                'user_within_limitations' => $taskDao->user_within_limitations($user_id, $task_id),
        ));

        return UserRouteHandler::render("task/task.view.tpl", $response);
    }

    public function task_search_translators_any_country_no_source(Request $request, Response $response, $args)
    {
        return $this->task_search_translators($request, $response, $args, 3);
    }

    public function task_search_translators_no_source(Request $request, Response $response, $args)
    {
        return $this->task_search_translators($request, $response, $args, 2);
    }

    public function task_search_translators_any_country(Request $request, Response $response, $args)
    {
        return $this->task_search_translators($request, $response, $args, 1);
    }

    public function task_search_translators(Request $request, Response $response, $args, $any_country = 0)
    {
        global $template_data;
        $task_id = $args['task_id'];

        $taskDao    = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $adminDao   = new DAO\AdminDao();

        $task = $taskDao->getTask($task_id);
        if ($any_country < 2 && Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['sourcing'] == 1) return $this->task_search_translators($request, $response, $args, 2);

        $project    = $projectDao->getProject($task->getProjectId());

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $memsource_task = $projectDao->get_memsource_task($task_id);

        $roles = $adminDao->get_roles(Common\Lib\UserSession::getCurrentUserID()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER);

        $users_to_discard_for_search = $taskDao->users_to_discard_for_search($task->getTaskType(), $project->getOrganisationId());

        if     ($any_country == 3) $invites_not_sent = $taskDao->list_task_invites_not_sent_no_source($task_id, $roles);
        elseif ($any_country == 2) $invites_not_sent = $taskDao->list_task_invites_not_sent_no_source_strict($task_id, $roles);
        elseif ($any_country == 1) $invites_not_sent = $taskDao->list_task_invites_not_sent($task_id, $roles);
        else                       $invites_not_sent = $taskDao->list_task_invites_not_sent_strict($task_id, $roles);
        $users_in_invites_not_sent = array();
        foreach ($invites_not_sent as $user) {
            $users_in_invites_not_sent[$user['user_id']] = $user;
        }

        $task_invites_not_sent_rates = $taskDao->list_task_invites_not_sent_rates($task_id);
        $users_in_task_invites_not_sent_rates = [];
        foreach ($task_invites_not_sent_rates as $user) {
            $users_in_task_invites_not_sent_rates[$user['user_id']] = $user;
        }

        $all_users = array();
        if ($any_country < 2) $invites_not_sent_words = $taskDao->list_task_invites_not_sent_words($task_id);
        else                  $invites_not_sent_words = $taskDao->list_task_invites_not_sent_words_no_source($task_id);
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
                if (!empty($users_in_task_invites_not_sent_rates[$user['user_id']])) {
                    $user['unit_rate'] = $users_in_task_invites_not_sent_rates[$user['user_id']]['unit_rate'];
                } else {
                    $user['unit_rate'] = '';
                }
                if (!in_array($user['user_id'], $users_to_discard_for_search)) $all_users[] = $user;
            }
        }

        // $all_users then has the remaining ones
        foreach ($invites_not_sent as $user) {
            if (empty($users_in_invites_not_sent_words[$user['user_id']])) {
                $user['words_delivered'] = '';
                $user['words_delivered_last_3_months'] = '';
                if (!empty($users_in_task_invites_not_sent_rates[$user['user_id']])) {
                    $user['unit_rate'] = $users_in_task_invites_not_sent_rates[$user['user_id']]['unit_rate'];
                } else {
                    $user['unit_rate'] = '';
                }
                if (!in_array($user['user_id'], $users_to_discard_for_search)) $all_users[] = $user;
            }
        }

        $extra_scripts  = file_get_contents(__DIR__."/../js/TaskView3.js");
        $extra_scripts .= "
    <link rel=\"stylesheet\" href=\"https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css\"/>
    <script type=\"text/javascript\" src=\"https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js\"></script>
    <script type=\"text/javascript\">
      $(document).ready(function(){
        $('#myTable').DataTable(
          {
            \"paging\": false
          }
        );
      });
    </script>";

        $paid_status = $taskDao->get_paid_status($task_id);
        $total_expected_cost = 0;
        if (!empty($paid_status) && $task->getWordCount() > 1) $total_expected_cost = $task->getWordCount()*$paid_status['unit_rate'];
        if (Common\Enums\TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['divide_rate_by_60']) $total_expected_cost /= 60;

        if ($task->getTaskStatus() == Common\Enums\TaskStatusEnum::IN_PROGRESS && $projectDao->are_translations_not_all_complete($task, $memsource_task)) $task->setTaskStatus(Common\Enums\TaskStatusEnum::CLAIMED);

        $template_data = array_merge($template_data, array(
            'sesskey'         => $sesskey,
            'extra_scripts'   => $extra_scripts,
            'task'            => $task,
            'other_task_ids'  => $taskDao->getOtherPendingChunks($task_id),
            'project'         => $project,
            'roles'           => PROJECT_OFFICER,
            'discourse_slug'  => $projectDao->discourse_parameterize($project),
            'memsource_task'  => $memsource_task,
            'matecat_url'     => $taskDao->get_matecat_url_regardless($task, $memsource_task),
            'required_qualification_for_details' => $taskDao->getRequiredTaskQualificationLevel($task_id),
            'sent_users'      => $taskDao->list_task_invites_sent($task_id),
            'all_users'       => $all_users,
            'eligible'        => $taskDao->get_user_paid_eligible_pairs($task_id, $any_country == 3 || $any_country == 2 ? 1 : 0, $any_country == 3 || $any_country == 1 ? 1 : 0),
            'any_country'     => $any_country,
            'paid_status'     => $paid_status,
            'total_expected_cost' => $total_expected_cost,
        ));

        return UserRouteHandler::render("task/task.search_translators.tpl", $response);
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
        return $response->withStatus(200);
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
        $task = $taskDao->getTask($task_id);
        $taskClaimedDate = $taskDao->getClaimedDate($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

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
                        error_log("taskOrgFeedback revokeTask: $task_id by $user_id");
                        $taskDao->updateTask($task);
                        if ($claimant != null) {
                            $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id, null);
                        } else {
                            $taskRevoke = true;
                            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::PENDING_CLAIM); // updateTask() has already set this, but need setTaskStatus() for tasks_status table
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
            'isSiteAdmin' => $adminDao->get_roles($user_id, $project->getOrganisationId()) & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER),
            "task_tags" => $task_tags
        ));

        return UserRouteHandler::render("task/task.org-feedback.tpl", $response);
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
                        error_log("taskUserFeedback revokeTask: $task_id by $user_id");
                        if ($claimant != null) {
                            $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id, $post['feedback']);
                        } else {
                            $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                            $taskDao->updateTask($task);
                            $taskRevoke = true;
                            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::PENDING_CLAIM); // updateTask() has already set this, but need setTaskStatus() for tasks_status table
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
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-view', array('task_id' => $task_id)));
                    }
                } else {
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('task_user_feedback_5'));
                }
            }
        }

        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "org" => $organisation,
            "project" => $project,
            "task" => $task,
            "taskClaimedDate" =>$taskClaimedDate,
            "claimant" => $claimant,
            "task_tags" => $task_tags
        ));

        return UserRouteHandler::render("task/task.user-feedback.tpl", $response);
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
        if ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION) $action = Lib\Localisation::getTranslation('task_review_translated');

        $reviews = array();
        $preReqTasks = $taskDao->getTaskPreReqs($taskId);
        $projectDao = new DAO\ProjectDao();
        if (empty($preReqTasks) && $memsource_task = $projectDao->get_memsource_task($taskId)) {
            $preReqTasks = [];
            $top_level = $projectDao->get_top_level($memsource_task['internalId']);
            $project_tasks = $projectDao->get_tasks_for_project($task->getProjectId());
            foreach ($project_tasks as $project_task) {
                if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                    if ($memsource_task['workflowLevel'] == $project_task['workflowLevel'] + 1) { // Dependent on
                        if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                            $dummyTask = new Common\Protobufs\Models\Task();
                            $dummyTask->setId($project_task['id']);
                            $dummyTask->setProjectId($task->getProjectId());
                            $dummyTask->setTitle($project_task['title']);
                            $dummyTask->set_cancelled($project_task['beginIndex']);
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
                        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-uploaded', array("task_id" => $taskId)));
                } else {
                    UserRouteHandler::flashNow("error", $error);
                }
            }

            if (isset($post['skip'])) {
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('task-uploaded', array("task_id" => $taskId)));
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
            'is_chunked'    => 0,
            'reviews'       => $reviews,
            'formAction'    => $formAction,
            'action'        => $action
        ));

        return UserRouteHandler::render("task/task.review.tpl", $response);
    }
}

$route_handler = new TaskRouteHandler();
$route_handler->init();
unset ($route_handler);
