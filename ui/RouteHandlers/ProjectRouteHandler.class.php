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
            '/project_cron/',
            array($this, 'project_cron')
        )->name('project_cron');
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

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $project = $projectDao->getProject($project_id);
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
            $extra_scripts .= file_get_contents(__DIR__."/../js/TaskView.js");
            // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
            $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

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
            $extra_scripts = file_get_contents(__DIR__."/../js/TaskView.js");
            // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
            $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

            $app->view()->appendData(array(
                "extra_scripts" => $extra_scripts,
                "org" => $org,
                "project_tags" => $project_tags
            ));
        }

        $preventImageCacheToken = time(); //see http://stackoverflow.com/questions/126772/how-to-force-a-web-browser-not-to-cache-images

        $app->view()->appendData(array(
                'sesskey'       => $sesskey,
                "isOrgMember"   => $isOrgMember,
                "isAdmin"       => $isAdmin,
                "isSiteAdmin"   => $isSiteAdmin,
                "imgCacheToken" => $preventImageCacheToken,
                'userSubscribedToOrganisation' => $userSubscribedToOrganisation
        ));
        $app->render("project/project.view.tpl");
    }

    public function projectAlter($project_id)
    {
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $projectDao = new DAO\ProjectDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = $this->random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $project = $projectDao->getProject($project_id);

        if ($post = $app->request()->post()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey
                    || empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['sourceCountrySelect']) || empty($post['sourceLanguageSelect']) || empty($post['project_deadline'])
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

                $sourceLocale->setCountryCode($post['sourceCountrySelect']);
                $sourceLocale->setLanguageCode($post['sourceLanguageSelect']);
                $project->setSourceLocale($sourceLocale);

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
        if ($userIsAdmin) {
            $userIsAdmin = 1; // Just to be sure what will appear in the template and then the JavaScript
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
            'sesskey'        => $sesskey,
        ));

        $app->render("project/project.alter.tpl");
    }

    public function projectCreate($org_id)
    {
        $app = \Slim\Slim::getInstance();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $subscriptionDao = new DAO\SubscriptionDao();
        $taskDao = new DAO\TaskDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = $this->random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        if ($post = $app->request()->post()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey
                    || empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['sourceCountrySelect']) || empty($post['sourceLanguageSelect']) || empty($post['project_deadline'])
                    || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])
                    || empty($post['wordCountInput']) || !ctype_digit($post['wordCountInput'])) {
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
                $project->setWordCount($post['wordCountInput']);

                $sourceLocale->setCountryCode($post['sourceCountrySelect']);
                $sourceLocale->setLanguageCode($post['sourceLanguageSelect']);
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
                                while (!empty($post["target_language_$targetCount"]) && !empty($post["target_country_$targetCount"])) {

                                    if (!empty($post["segmentation_$targetCount"])) {
                                        // Create segmentation task
                                        $id = $this->addProjectTask(
                                            $project,
                                            $post["target_language_$targetCount"],
                                            $post["target_country_$targetCount"],
                                            Common\Enums\TaskTypeEnum::SEGMENTATION,
                                            0,
                                            $createdTasks,
                                            $user_id,
                                            $projectDao,
                                            $taskDao,
                                            $app,
                                            $post);
                                        if (!$id) {
                                            $creatingTasksSuccess = false;
                                            break;
                                        }

                                    } else {
                                        // Not a segmentation task, so translation and/or proofreading will be created.
                                        if (!empty($post["translation_$targetCount"])) {
                                            $translation_Task_Id = $this->addProjectTask(
                                                $project,
                                                $post["target_language_$targetCount"],
                                                $post["target_country_$targetCount"],
                                                Common\Enums\TaskTypeEnum::TRANSLATION,
                                                0,
                                                $createdTasks,
                                                $user_id,
                                                $projectDao,
                                                $taskDao,
                                                $app,
                                                $post);
                                            if (!$translation_Task_Id) {
                                                $creatingTasksSuccess = false;
                                                break;
                                            }

                                            if (!empty($post["proofreading_$targetCount"])) {
                                                $id = $this->addProjectTask(
                                                    $project,
                                                    $post["target_language_$targetCount"],
                                                    $post["target_country_$targetCount"],
                                                    Common\Enums\TaskTypeEnum::PROOFREADING,
                                                    $translation_Task_Id,
                                                    $createdTasks,
                                                    $user_id,
                                                    $projectDao,
                                                    $taskDao,
                                                    $app,
                                                    $post);
                                                if (!$id) {
                                                    $creatingTasksSuccess = false;
                                                    break;
                                                }
                                            }
                                        } elseif (empty($post["translation_$targetCount"]) && !empty($post["proofreading_$targetCount"])) {
                                            // Only a proofreading task to be created
                                            $id = $this->addProjectTask(
                                                $project,
                                                $post["target_language_$targetCount"],
                                                $post["target_country_$targetCount"],
                                                Common\Enums\TaskTypeEnum::PROOFREADING,
                                                0,
                                                $createdTasks,
                                                $user_id,
                                                $projectDao,
                                                $taskDao,
                                                $app,
                                                $post);
                                            if (!$id) {
                                                $creatingTasksSuccess = false;
                                                break;
                                            }
                                        }
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
                                        $projectDao->calculateProjectDeadlines($project->getId());

                                        $source_language = $post['sourceLanguageSelect'] . '-' . $post['sourceCountrySelect'];
                                        $target_languages = '';
                                        $targetCount = 0;
                                        if (!empty($post["target_language_$targetCount"]) && !empty($post["target_country_$targetCount"])) {
                                            $target_languages = $post["target_language_$targetCount"] . '-' . $post["target_country_$targetCount"];
                                        }
                                        $targetCount++;
                                        while (!empty($post["target_language_$targetCount"]) && !empty($post["target_country_$targetCount"])) {
                                            $target_languages .= ',' . $target_languages = $post["target_language_$targetCount"] . '-' . $post["target_country_$targetCount"];
                                            $targetCount++;
                                        }
                                        $taskDao->insertWordCountRequestForProjects($project->getId(), $source_language, $target_languages, $post['wordCountInput']);

                                        try {
                                            $app->redirect($app->urlFor('project-view', array('project_id' => $project->getId())));
                                        } catch (\Exception $e) { // redirect throws \Slim\Exception\Stop
                                        }
                                    } catch (\Exception $e) {
                                        $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                                        try {
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

        $subscription_text = null;
        $paypal_email = Common\Lib\Settings::get('banner.paypal_email');
        if (!empty($paypal_email)) {
            $text_start = '<p style="font-size: 14px">' . Lib\Localisation::getTranslation('project_subscription') . '<br />';

            //$siteLocation = Common\Lib\Settings::get('site.location');
            $text_end = Lib\Localisation::getTranslation('project_subscription_annual_donation') . '</p>';
            $text_end .= '<table style="font-size: 14px">';
            $text_end .= '<tr><td>';
            $text_end .=
                '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:inline;">
                <input name="business" type="hidden" value="' . Common\Lib\Settings::get('banner.paypal_email') . '" />
                <input name="cmd" type="hidden" value="_donations" />
                <input name="item_name" type="hidden" value="Subscription: Intermittent use" />
                <input name="item_number" type="hidden" value="Subscription: Intermittent use" />
                <input name="amount" type="hidden" value="35.00" />
                <input name="currency_code" type="hidden" value="EUR" />
                <button type="submit" class="btn btn-success" style="width: 40%; text-align: left; margin-bottom: 3px;">
                    <i class="icon-gift icon-white"></i> ' . Lib\Localisation::getTranslation('project_subscription_intermittent') .
                '</button>' .
                /*<input alt="PayPal - The safer, easier way to pay online" name="submit" src="' . $siteLocation . 'ui/img/p35.png" type="image" style="height:29px; width:64px;" />*/
                '</form>';
            //$text_end .= Lib\Localisation::getTranslation('project_subscription_intermittent');
            $text_end .= '</td></tr>';
            $text_end .= '<tr><td>';
            $text_end .=
                '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:inline;">
                <input name="business" type="hidden" value="' . Common\Lib\Settings::get('banner.paypal_email') . '" />
                <input name="cmd" type="hidden" value="_donations" />
                <input name="item_name" type="hidden" value="Subscription: Moderate use" />
                <input name="item_number" type="hidden" value="Subscription: Moderate use" />
                <input name="amount" type="hidden" value="75.00" />
                <input name="currency_code" type="hidden" value="EUR" />
                <button type="submit" class="btn btn-success" style="width: 40%; text-align: left; margin-bottom: 3px;">
                    <i class="icon-gift icon-white"></i> ' . Lib\Localisation::getTranslation('project_subscription_moderate') .
                '</button>' .
                /*<input alt="PayPal - The safer, easier way to pay online" name="submit" src="' . $siteLocation . 'ui/img/p75.png" type="image" style="height:29px; width:64px;" />*/
                '</form>';
            //$text_end .= Lib\Localisation::getTranslation('project_subscription_moderate');
            $text_end .= '</td></tr>';
            $text_end .= '<tr><td>';
            $text_end .=
                '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:inline;">
                <input name="business" type="hidden" value="' . Common\Lib\Settings::get('banner.paypal_email') . '" />
                <input name="cmd" type="hidden" value="_donations" />
                <input name="item_name" type="hidden" value="Subscription: Heavy use" />
                <input name="item_number" type="hidden" value="Subscription: Heavy use" />
                <input name="amount" type="hidden" value="300.00" />
                <input name="currency_code" type="hidden" value="EUR" />
                <button type="submit" class="btn btn-success" style="width: 40%; text-align: left; margin-bottom: 3px;">
                    <i class="icon-gift icon-white"></i> ' . Lib\Localisation::getTranslation('project_subscription_heavy') .
                '</button>' .
                /*<input alt="PayPal - The safer, easier way to pay online" name="submit" src="' . $siteLocation . 'ui/img/p300.jpg" type="image" style="height:29px; width:64px;" />*/
                '</form>';
            //$text_end .= Lib\Localisation::getTranslation('project_subscription_heavy');
            $text_end .= '</td></tr>';
            $text_end .= '<tr><td>';
            $text_end .=
                '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:inline;">
                <input name="business" type="hidden" value="' . Common\Lib\Settings::get('banner.paypal_email') . '" />
                <input name="cmd" type="hidden" value="_donations" />
                <input name="item_name" type="hidden" value="Subscription: Upgrade other" />
                <input name="item_number" type="hidden" value="Subscription: Upgrade other" />
                <input name="currency_code" type="hidden" value="EUR" />
                <button type="submit" class="btn btn-success" style="width: 40%; text-align: left; margin-bottom: 3px;">
                    <i class="icon-gift icon-white"></i> ' . Lib\Localisation::getTranslation('project_subscription_other') .
                '</button>' .
                /*<input alt="PayPal - The safer, easier way to pay online" name="submit" src="' . $siteLocation . 'ui/img/pother.jpg" type="image" style="height:29px; width:64px;" />*/
                '</form>';
            //$text_end .= Lib\Localisation::getTranslation('project_subscription_other');
            $text_end .= '</td></tr>';
            $text_end .= '</table>';
            $text_end .= '<p style="font-size: 14px">' . Lib\Localisation::getTranslation('project_subscription_cannot') . '</p>';

            $subscription = $orgDao->getSubscription($org_id);
            if (empty($subscription)) {
                $number_of_projects_ever = $subscriptionDao->number_of_projects_ever($org_id);

                $text_middle_pay = Lib\Localisation::getTranslation('project_subscription_initial');
                if ($number_of_projects_ever == 1) {
                    $text_middle_pay .= ' ' . Lib\Localisation::getTranslation('project_subscription_number');
                } elseif ($number_of_projects_ever > 1) {
                    $text_middle_pay .= ' ' . sprintf(Lib\Localisation::getTranslation('project_subscription_numbers'), $number_of_projects_ever);
                }
                $text_middle_pay .= '<br />';
                $text_middle_pay .= Lib\Localisation::getTranslation('project_subscription_remind') . '<br /><br />';

                if ($number_of_projects_ever < 2) {
                    $subscription_text = $text_start . $text_middle_pay . $text_end;
                } else {
                    $subscription_text = $text_start . $text_middle_pay . $text_end;
                }
            } else {
                $year_ago = gmdate('Y-m-d H:i:s', strtotime('-1 year'));
                $outside_year = $subscription['start_date'] < $year_ago;

                $number_of_projects_since_last_donation = $subscriptionDao->number_of_projects_since_last_donation($org_id);
                $number_of_projects_since_donation_anniversary = $subscriptionDao->number_of_projects_since_donation_anniversary($org_id);

                $text_middle_renew = sprintf(Lib\Localisation::getTranslation('project_subscription_last_donation'), substr($subscription['start_date'], 8, 2) . ' ' . $month_list[(int)substr($subscription['start_date'], 5, 2)] . ' ' . substr($subscription['start_date'], 0, 4)) . ' ';
                if ($number_of_projects_since_donation_anniversary == 1) {
                    $text_middle_renew .= Lib\Localisation::getTranslation('project_subscription_number_renew') . '<br />';
                } elseif ($number_of_projects_since_donation_anniversary > 1) {
                    $text_middle_renew .= sprintf(Lib\Localisation::getTranslation('project_subscription_numbers_renew'), $number_of_projects_since_donation_anniversary) . '<br />';
                }
                $text_middle_renew .= Lib\Localisation::getTranslation('project_subscription_remind_renew') . '<br /><br />';

                $text_middle_upgrade  = sprintf(Lib\Localisation::getTranslation('project_subscription_numbers_upgrade'), $number_of_projects_since_last_donation) . '<br />';
                $text_middle_upgrade .= Lib\Localisation::getTranslation('project_subscription_remind_upgrade') . '<br /><br />';

                switch ($subscription['level']) {
                    case 1000: // Free because unable to pay
                        break;
                    case 100:  // Partner
                        break;
                    case 10:   // Intermittent use for year
                        if ($outside_year) {
                            $subscription_text = $text_start . $text_middle_renew . $text_end;
                        } elseif ($number_of_projects_since_last_donation >= 3) {
                            $subscription_text = $text_start . $text_middle_upgrade . $text_end;
                        }
                        break;
                    case 20:   // Moderate use for year
                        if ($outside_year) {
                            $subscription_text = $text_start . $text_middle_renew . $text_end;
                        } elseif ($number_of_projects_since_last_donation >= 10) {
                            $subscription_text = $text_start . $text_middle_upgrade . $text_end;
                        }
                        break;
                    case 30:   // Heavy use for year
                        if ($outside_year) {
                            $subscription_text = $text_start . $text_middle_renew . $text_end;
                        }
                    break;
                }
            }
        }

        // $languages = Lib\TemplateHelper::getLanguageList(); // (code) is added to name because of settings
        // $countries = Lib\TemplateHelper::getCountryList();
        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();
        $countryDao = new DAO\CountryDao();
        $countries = $countryDao->getCountries();

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
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/ProjectCreate1.js\"></script>";

        $app->view()->appendData(array(
            "siteLocation"          => Common\Lib\Settings::get('site.location'),
            "siteAPI"               => Common\Lib\Settings::get('site.api'),
            "maxFileSize"           => Lib\TemplateHelper::maxFileSizeBytes(),
            "imageMaxFileSize"      => Common\Lib\Settings::get('projectImages.max_image_size'),
            "supportedImageFormats" => Common\Lib\Settings::get('projectImages.supported_formats'),
            "org_id"         => $org_id,
            "user_id"        => $user_id,
            'subscription_text' => $subscription_text,
            "extra_scripts"  => $extraScripts,
            'month_list'     => $month_list,
            'selected_month' => (int)date('n'),
            'year_list'      => $year_list,
            'selected_year'  => (int)date('Y'),
            'hour_list'      => $hour_list,
            'selected_hour'  => 0,
            'minute_list'    => $minute_list,
            'selected_minute'=> 0,
            'languages'      => $languages,
            'countries'      => $countries,
            'showRestrictTask' => $taskDao->organisationHasQualifiedBadge($org_id),
            'sesskey'        => $sesskey,
        ));
        $app->render("project/project.create.tpl");
    }

    private function addProjectTask(
        $project,
        $target_language,
        $target_country,
        $taskType,
        $preReqTaskId,
        &$createdTasks,
        $user_id,
        $projectDao,
        $taskDao,
        $app,
        $post)
    {
        $taskPreReqs = array();
        $task = new Common\Protobufs\Models\Task();
        try {
            $projectTasks = $projectDao->getProjectTasks($project->getId());
        } catch (\Exception $e) {
            return 0;
        }

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
        $task->setDeadline($project->getDeadline());

        if (!empty($post['publish'])) {
            $task->setPublished(1);
        } else {
            $task->setPublished(0);
        }

        try {
            error_log("addProjectTask");
            $newTask = $taskDao->createTask($task);
            $newTaskId = $newTask->getId();
            $createdTasks[] = $newTaskId;

            $upload_error = $taskDao->saveTaskFile(
                $newTaskId,
                $user_id,
                $projectDao->getProjectFile($project->getId())
            );

            if ($newTaskId && $preReqTaskId) {
                $taskDao->addTaskPreReq($newTaskId, $preReqTaskId);
            }

            if (!empty($post['trackProject'])) {
                $userDao = new DAO\UserDao();
                $userDao->trackTask($user_id, $newTaskId);
            }

            if (!empty($post['restrictTask'])) {
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

    public function project_cron()
    {
        $matecat_acceptable_languages = array(
'af-ZA',
'sq-AL',
'am-AM',
'ar-SA',
'an-ES',
'hy-AM',
'ast-ES',
'az-AZ',
'ba-RU',
'eu-ES',
'bn-IN',
'be-BY',
'fr-BE',
'bs-BA',
'br-FR',
'bg-BG',
'my-MM',
'ca-ES',
'cav-ES',
'cb-PH',
'zh-CN',
'zh-TW',
'hr-HR',
'cs-CZ',
'da-DK',
'nl-NL',
'en-GB',
'en-US',
'eo-XN',
'et-EE',
'fo-FO',
'ff-FUL',
'fi-FI',
'nl-BE',
'fr-CA',
'gl-ES',
'ka-GE',
'de-DE',
'el-GR',
'gu-IN',
'ht-HT',
'ha-HAU',
'US-HI',
'he-IL',
'mrj-RU',
'hi-IN',
'hu-HU',
'is-IS',
'id-ID',
'ga-IE',
'it-IT',
'ja-JP',
'jv-ID',
'kn-IN',
'kr-KAU',
'kk-KZ',
'km-KH',
'ko-KR',
'ku-KMR',
'ku-CKB',
'ky-KG',
'lo-LA',
'la-XN',
'lv-LV',
'ln-LIN',
'lt-LT',
'lb-LU',
'mk-MK',
'mg-MLG',
'ms-MY',
'ml-IN',
'mt-MT',
'mhr-RU',
'mi-NZ',
'mr-IN',
'mn-MN',
'sr-ME',
'nr-ZA',
'ne-NP',
'nb-NO',
'nn-NO',
'ny-NYA',
'oc-FR',
'oc-ES',
'pa-IN',
'pap-CW',
'ps-PK',
'fa-IR',
'pl-PL',
'pt-PT',
'pt-BR',
'qu-XN',
'ro-RO',
'ru-RU',
'gd-GB',
'sr-Latn-RS',
'sr-Cyrl-RS',
'nso-ZA',
'tn-ZA',
'si-LK',
'sk-SK',
'sl-SI',
'so-SO',
'es-ES',
'es-MX',
'es-CO',
'su-ID',
'sw-SZ',
'sv-SE',
'de-CH',
'tl-PH',
'tg-TJ',
'ta-IN',
'te-IN',
'tt-RU',
'th-TH',
'ts-ZA',
'tr-TR',
'tk-TM',
'udm-RU',
'uk-UA',
'ur-PK',
'uz-UZ',
'vi-VN',
'cy-GB',
'xh-ZA',
'yi-YD',
'zu-ZA',
);

        $taskDao = new DAO\TaskDao();

        // status 1 => Uploaded to MateCat [This call will happen one minute after getWordCountRequestForProjects(0)]
        $projects = $taskDao->getWordCountRequestForProjects(1);
error_log('(1)projects...');
error_log(print_r($projects, true));
$projects = array();
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $project_id = $project['project_id'];
                $matecat_id_project = $project['matecat_id_project'];
                $matecat_id_project_pass = $project['matecat_id_project_pass'];

                // https://www.matecat.com/api/docs#!/Project/get_status (i.e. Word Count)
                $re = curl_init("https://www.matecat.com/api/status?id_project=$matecat_id_project&project_pass=$matecat_id_project_pass");

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
                $res = curl_exec($re);

                $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
                $header = substr($res, 0, $header_size);
                $res = substr($res, $header_size);
                $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                curl_close($re);

                $word_count = 0;
                if ($responseCode == 200) {
                    $response_data = json_decode($res, true);

                    if ($response_data['status'] !== 'DONE') {
                        error_log("project_cron /status ($project_id) status NOT DONE: " . $response_data['status']);
                    }
                    if (!empty($response_data['errors'])) {
                        foreach ($response_data['errors'] as $error) {
                            error_log("project_cron /status ($project_id) error: " . $error);
                        }
                    }

                    if (!empty($response_data['data']['summary']['TOTAL_RAW_WC'])) {
                        $word_count = $response_data['data']['summary']['TOTAL_RAW_WC'];

                        // Set word count for the Project and its Tasks
                        $taskDao->updateWordCountForProject($project_id, $word_count);
                    } else {
                        error_log("project_cron /status ($project_id) TOTAL_RAW_WC empty!");
                    }
                } else {
                    error_log("project_cron /status ($project_id) responseCode: $responseCode");
                }

                // Change status to Complete (2), even if there was an error!?
                $taskDao->updateWordCountRequestForProjects($project_id, $matecat_id_project, $matecat_id_project_pass, $word_count, 2);
            }
        }

        // status 0 => Waiting for Upload to MateCat
        $projects = $taskDao->getWordCountRequestForProjects(0);
error_log('(0)projects...');
error_log(print_r($projects, true));
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $project_id = $project['project_id'];

                $project_file = $taskDao->getProjectFileLocation($project_id);
                if (!empty($project_file)) {
                    $filename = $project_file['filename'];
                    $file = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/$filename";
                } else {
                    error_log("project_cron ($project_id) getProjectFileLocation FAILED");
                    continue;
                }

                $source_language = $project['source_language'];
error_log("1-source_language: $source_language");
                if (!in_array($source_language, $matecat_acceptable_languages)) $source_language = 'en-US';
error_log("2-source_language: $source_language");

/*
                // https://www.matecat.com/api/docs#!/Project/post_new
                $re = curl_init('https://www.matecat.com/api/new');

                // http://php.net/manual/en/function.curl-setopt.php
                curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($re, CURLOPT_COOKIESESSION, true);
                curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($re, CURLOPT_AUTOREFERER, true);

                $httpHeaders = array(
                    'Expect:'
                );
                curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);
*/

                // http://php.net/manual/en/class.curlfile.php
error_log("1-filename: $filename, file: $file");
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file);
                finfo_close($finfo);
error_log("2-filename: $filename, file: $file");
                $cfile = new CURLFile($file, $mime, $filename);
error_log("3-filename: $filename, file: $file");

                $fields = array(
                  'file'         => $cfile,
                  'project_name' => "proj-$project_id",
                  'source_lang'  => $source_language,
                  'target_lang'  => 'es-ES',
                  'tms_engine'   => '1',
                  'mt_engine'    => '1',
                  'subject'      => 'general',
                  'owner_email'  => 'anonymous'
                );
error_log('fields...');
error_log(print_r($fields, true));
/*
                curl_setopt($re, CURLOPT_POSTFIELDS, $fields);

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

                    if ($response_data['status'] !== 'OK') {
                        error_log("project_cron /new ($project_id) status NOT OK: " . $response_data['status']);
                        error_log("project_cron /new ($project_id) status message: " . $response_data['message']);
                        // Change status to Complete (3), if there was an error!
                        $taskDao->updateWordCountRequestForProjects($project_id, 0, 0, 0, 3);
                    }
                    elseif (empty($response_data['id_project']) || empty($response_data['project_pass'])) {
                        error_log("project_cron /new ($project_id) id_project or project_pass empty!");
                        // Change status to Complete (3), if there was an error!
                        $taskDao->updateWordCountRequestForProjects($project_id, 0, 0, 0, 3);
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
*/
            }
        }
    }
}

$route_handler = new ProjectRouteHandler();
$route_handler->init();
unset ($route_handler);
