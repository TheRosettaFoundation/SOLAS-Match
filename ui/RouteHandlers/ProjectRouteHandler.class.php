<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/TaskTypeEnum.class.php";
require_once __DIR__."/../../Common/Enums/TaskStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

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

//(**) ALAN Work In Progress
        $app->get(
            "/project/:org_id/create1/",
            file...
            array($middleware, "authUserForOrg"),
            array($this, "projectCreate1")
        )->via("GET", "POST")->name("project-create1");
//(**) ALAN Work In Progress

        $app->get(
            "/project/id/:project_id/created/",
            array($middleware, "authUserForOrgProject"),
            array($this, "projectCreated")
        )->name("project-created");

        $app->get(
            "/project/id/:project_id/mark-archived/",
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
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $project = $projectDao->getProject($project_id);
        $app->view()->setData("project", $project);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

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
        }

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $project_tags = $projectDao->getProjectTags($project_id);
        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($user_id, $project->getOrganisationId());

        $isSiteAdmin = $adminDao->isSiteAdmin($user_id);
        $isAdmin = $adminDao->isOrgAdmin($project->getOrganisationId(), $user_id) || $isSiteAdmin;

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
                }
            }

            $graph = $projectDao->getProjectGraph($project_id);
            $viewer = new Lib\GraphViewer($graph);
            $graphView = $viewer->constructView();

            $extra_scripts = "";
            $extra_scripts .= $viewer->generateDataScript();
            $extra_scripts .= file_get_contents(__DIR__."/../js/GraphHelper.js");
            $extra_scripts .= file_get_contents(__DIR__."/../js/project-view.js");

            $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
            $taskTypeColours = array();

            for ($i=1; $i <= $numTaskTypes; $i++) {
                $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
            }

            $app->view()->appendData(array(
                    "org" => $org,
                    "graph" => $graphView,
                    "extra_scripts" => $extra_scripts,
                    "projectTasks" => $project_tasks,
                    "taskMetaData" => $taskMetaData,
                    "taskTypeColours" => $taskTypeColours,
                    "userSubscribedToProject" => $userSubscribedToProject,
                    "project_tags" => $project_tags,
                    "taskLanguageMap" => $taskLanguageMap
            ));
        } else {
            $app->view()->appendData(array(
                "org" => $org,
                "project_tags" => $project_tags
            ));
        }

        $preventImageCacheToken = time(); //see http://stackoverflow.com/questions/126772/how-to-force-a-web-browser-not-to-cache-images

        $app->view()->appendData(array(
                "isOrgMember"   => $isOrgMember,
                "isAdmin"       => $isAdmin,
                "isSiteAdmin"   => $isSiteAdmin,
                "imgCacheToken" => $preventImageCacheToken,
                'userSubscribedToOrganisation' => $userSubscribedToOrganisation
        ));
        $app->render("project/project.view.tpl");
    }


    public function projectAlter($projectId)
    {
        $app = \Slim\Slim::getInstance();
        $userId = Common\Lib\UserSession::getCurrentUserID();

        $extraScripts = "
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/web_components/dart_support.js\"></script>
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/browser/interop.js\"></script>
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/Routes/Projects/ProjectAlter.dart.js\"></script>
<span class=\"hidden\">
";
        $extraScripts .= file_get_contents("ui/dart/web/Routes/Projects/ProjectAlterForm.html");
        $extraScripts .= "</span>";
        $platformJS =
        "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/web_components/platform.js\"></script>";

        $app->view()->appendData(array(
        	"projectId" => $projectId,
            "userId" => $userId,
            "maxFileSize" => 2,
            "extra_scripts" => $extraScripts,
            "platformJS" => $platformJS
        ));
        $app->render("project/project.alter.tpl");
    }

    public function projectCreate($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $extraScripts = "
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/web_components/dart_support.js\"></script>
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/browser/interop.js\"></script>
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/Routes/Projects/ProjectCreate.dart.js\"></script>
<span class=\"hidden\">
";
        $extraScripts .= file_get_contents("ui/dart/web/Routes/Projects/ProjectCreateForm.html");
        $extraScripts .= "</span>";
        $platformJS =
        "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/web_components/platform.js\"></script>";

        $app->view()->appendData(array(
            "maxFileSize"   => Lib\TemplateHelper::maxFileSizeBytes(),
            "org_id"        => $org_id,
            "user_id"       => $user_id,
            "extra_scripts" => $extraScripts,
            "platformJS"    => $platformJS
        ));
        $app->render("project/project.create.tpl");
    }

//(**) ALAN Work In Progress
    public function projectCreate1($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

(**)... Maybe...
        $titleError = null;
        $wordCountError = null;
        $deadlineError = null;
        $taskPreReqs = array();
        $task = new Common\Protobufs\Models\Task();
        $project = $projectDao->getProject($project_id);???
        $projectTasks = $projectDao->getProjectTasks($project_id);
        $task->setProjectId($project_id);
...(**)

        if ($post = $app->request()->post()) {

            if (isset($post['title'])) {
                $task->setTitle($post['title']);
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
                $newTask = $taskDao->createTask($task);
                $newTaskId = $newTask->getId();

                $upload_error = null;
                try {
                    $upload_error = $taskDao->saveTaskFile(
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
  UI HAD THIS(**) window.location.assign(siteLocation + "project/" + project.id + "/view");
                    $app->redirect($app->urlFor("task-created", array("task_id" => $newTaskId)));
                } else {
                    $taskDao->deleteTask($newTaskId);
                    $app->view()->appendData(array("upload_error" => $upload_error));
                }
            }
        }



        $languages = Lib\TemplateHelper::getLanguageList();
        $countries = Lib\TemplateHelper::getCountryList();

        $month_list = array(
            1 => Localisation::getTranslation('common_january'),
            2 => Localisation::getTranslation('common_february'),
            3 => Localisation::getTranslation('common_march'),
            4 => Localisation::getTranslation('common_april'),
            5 => Localisation::getTranslation('common_may'),
            6 => Localisation::getTranslation('common_june'),
            7 => Localisation::getTranslation('common_july'),
            8 => Localisation::getTranslation('common_august'),
            9 => Localisation::getTranslation('common_september'),
            10 => Localisation::getTranslation('common_october'),
            11 => Localisation::getTranslation('common_november'),
            12 => Localisation::getTranslation('common_december'),
        );
        $year_list = array();
        $yeari = (int)date('Y');
        for (i = 0; i < 10; i++) {
            $year_list[$yeari] = $yeari++;
        }
        $hour_list = array();
        for (i = 0; i < 24; i++) {
            $hour_list[$i] = $i;
        }
        $minute_list = array();
        $minutei = (int)date('Y');
        for (i = 0; i < 60; i++) {
            $minute_list[$i] = $i;
        }

        $extraScripts  = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/ProjectCreate.js\"></script>";

        $app->view()->appendData(array(
            "maxFileSize"    => Lib\TemplateHelper::maxFileSizeBytes(),
            "org_id"         => $org_id,
            "user_id"        => $user_id,
            "extra_scripts"  => $extraScripts,
            'month_list'     => $month_list,
            'selected_month' => (int)date('n'),
            'year_list'      => $year_list,
            'selected_year'  => (int)date('Y'),
            'hour_list'      => $hour_list,
            'selected_hour'  => 0,
            'minute_list'    => $minute_list,
            'selected_minute'=> 0,
            "languages"      => $languages,
            "countries"      => $countries,
            "titleError"    => $titleError,(**)MAYBE
            "wordCountError"=> $wordCountError,(**)MAYBE
            "deadlineError" => $deadlineError,(**)MAYBE
        ));
        $app->render("project/project.create1.tpl");
    }
//(**) ALAN Work In Progress

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

    public function archiveProject($project_id)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();

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

        try {
            $headArr = $projectDao->downloadProjectFile($projectId);
            //Convert header data to array and set headers appropriately
            $headArr = json_decode($headArr);
            foreach ($headArr as $key => $val) {
                $app->response->headers->set($key, $val);
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
            $headArr = json_decode($headArr);
            foreach ($headArr as $key => $val) {
                $app->response->headers->set($key, $val);
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

}

$route_handler = new ProjectRouteHandler();
$route_handler->init();
unset ($route_handler);
