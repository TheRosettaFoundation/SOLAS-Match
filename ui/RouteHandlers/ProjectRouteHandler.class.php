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
            //array($middleware, "authUserForProjectImage"),
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
        }

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $project_tags = $projectDao->getProjectTags($project_id);
        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($user_id, $project->getOrganisationId());
        
        $adminDao = new DAO\AdminDao();
        $isAdmin = $adminDao->isOrgAdmin($project->getOrganisationId(), $user_id) || $adminDao->isSiteAdmin($user_id);

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
        
        $app->view()->appendData(array(
                "isOrgMember"   => $isOrgMember,
                "isAdmin"       => $isAdmin,
                'userSubscribedToOrganisation' => $userSubscribedToOrganisation
        ));
        $app->render("project/project.view.tpl");
    }
    
    public function projectAlter($project_id)
    {
        $app = \Slim\Slim::getInstance();
        $deadlineError = '';
        $projectDao = new DAO\ProjectDao();

        $project = $projectDao->getProject($project_id);
        if (\SolasMatch\UI\isValidPost($app)) {
            $post = $app->request()->post();
            
            if (isset($post['title'])) {
                $project->setTitle(htmlspecialchars($post['title']));
            }
            if (isset($post['description'])) {
                $project->setDescription($post['description']);
            }

            if (isset($post['word-count'])) {
                 $result=$projectDao->updateProjectWordCount($project_id, $post['word-count']);
                if ($result==1) {
                    $project->setWordCount($post['word-count']);
                } elseif ($result==2) {
                    $app->flash("error", Lib\Localisation::getTranslation('project_alter_word_count_error_1'));
                } else {
                    $app->flash("error", Lib\Localisation::getTranslation('project_alter_word_count_error_2'));
                }
            }

            if (isset($post['impact'])) {
                $project->setImpact($post['impact']);
            }
            if (isset($post['deadline'])) {
                if ($validTime = Lib\TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);
                    $project->setDeadline($date);
                } else {
                    $deadlineError = Lib\Localisation::getTranslation('project_alter_11');
                }
            }
            
            $sourceLocale = new Common\Protobufs\Models\Locale();
            if (isset($post['sourceLanguage'])) {
                $sourceLocale->setLanguageCode($post['sourceLanguage']);
            }
            if (isset($post['sourceCountry'])) {
                $sourceLocale->setCountryCode($post['sourceCountry']);
            }
            if (isset($post['reference']) && $post['reference'] != "http://") {
                $project->setReference($post['reference']);
            }
            $project->setSourceLocale($sourceLocale);
                        
            if (isset($post['tags'])) {
                $tagLabels = Lib\TemplateHelper::separateTags($post['tags']);
                if ($tagLabels) {
                    $project->clearTag();
                    foreach ($tagLabels as $tagLabel) {
                        $newTag = new Common\Protobufs\Models\Tag();
                        $newTag->setLabel($tagLabel);
                        $project->addTag($newTag);
                    }
                }
            }
            
            if ($deadlineError == '') {
                $projectDao->updateProject($project);
                $app->redirect($app->urlFor("project-view", array("project_id" => $project_id)));
            }
        }
         
        $languages = Lib\TemplateHelper::getLanguageList();
        $countries = Lib\TemplateHelper::getCountryList();
        
        $tags = $project->getTagList();
        $tag_list = "";
        if ($tags != null) {
            foreach ($tags as $tag) {
                $tag_list .= $tag->getLabel() . " ";
            }
        }

        $tagList = "[";
        $tagDao = new DAO\TagDao();
        $tags = $tagDao->getTags(null);
        if ($tags) {
            foreach ($tags as $tag) {
                $tagList .= "\"{$tag->getLabel()}\", ";
            }
        }
        $tagList = substr($tagList, 0, strlen($tagList) - 2);
        $tagList .= "]";

        $extra_scripts = "
            <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\">".
            "</script>".
            "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/datetime-picker.js\"></script>".
            "<script type=\"text/javascript\">
                var tagList = $tagList;
            </script>"
            .file_get_contents(__DIR__."/../js/tags-autocomplete.js");
        
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $adminDao = new DAO\AdminDao();
        $isAdmin = $adminDao->isSiteAdmin($user_id);
        
        
        $word_count= $project->getWordCount();
        $app->view()->appendData(array(
                              "project"         => $project,
                              "languages"       => $languages,
                              "countries"       => $countries,
                              "tag_list"        => $tag_list,
                              "deadlineError"   => $deadlineError,
                              "extra_scripts"   => $extra_scripts,
                              "isAdmin" => $isAdmin,
                              "word_count"   => $word_count
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
