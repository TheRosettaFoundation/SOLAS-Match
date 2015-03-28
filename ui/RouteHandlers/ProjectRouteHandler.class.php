<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/Enums/TaskTypeEnum.class.php";
require_once __DIR__."/../../Common/Enums/TaskStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

//require_once __DIR__."/../../api/v0/IO.php"; //(**) ALAN Work In Progress

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

        if ($post = $app->request()->post()) {
            if (empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['sourceCountrySelect']) || empty($post['sourceLanguageSelect']) || empty($post['project_deadline'])
                    || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])
                    || empty($post['wordCountInput'] || !ctype_digit($post['wordCountInput']))) {
                // Note the deadline date validation above is only partial (these checks have been done more rigorously on client size, if that is to be trusted)
                $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_to_create_project'), 'project_title'));
            } else {
                $sourceLocale = new Common\Protobufs\Models\Locale();
                $project = new Common\Protobufs\Models\Project();

                $project->setId(null);
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

                $project = $projectDao->createProject($project);
                if (empty($project) || $project->getId() <= 0) {
                    $app->flashNow('error', Lib\Localisation::getTranslation('project_create_title_conflict'));
                } else {
                    $projectTagList = array();
                    if (!empty($post['tagList'])) {
                        $tagLabels = explode(' ', $post['tagList']);
                        foreach ($tagLabels as $tagLabel) {
                            $tagLabel = trim($tagLabel);
                            if (!empty($tagLabel)) {
                                $tag = new Common\Protobufs\Models\Tag();
                                $tag->setLabel($tagLabel);
                                $projectTagList[] = $tag;
                            }
                        }
                    }

                    $projectTags = API\DAO\TagsDao::updateTags($project->getId(), $projectTagList);
                    $project->clearTag();
                    foreach ($projectTags as $projectTag) {
                        $project->addTag($projectTag);
                    }

                    $projectDao->updateProject($project);

                    if (empty($_FILES['projectFile']['name']) || !empty($_FILES['projectFile']['error']) || empty($_FILES['projectFile']['tmp_name'])
                            || (($data = file_get_contents($_FILES['projectFile']['tmp_name'])) === false)) {
                        $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), $_FILES['projectFile']['name']));
                        $projectDao->deleteProject($project->getId());
                    } else {
//                        $success = \SolasMatch\API\V0\IO::saveProjectFileToFileSystem($project->getId(), $data, $_FILES['projectFile']['name'], $user_id);
                        if (!success) {
                            $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('common_error_file_stopped_by_extension')));
                            $projectDao->deleteProject($project->getId());
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
                                    if (!in_array($extension, explode(",", Common\Lib\Settings::get('project_images.supported_formats')))) {
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
                                    $imageMaxWidth  = Common\Lib\Settings::get('project_images.max_width');
                                    $imageMaxHeight = Common\Lib\Settings::get('project_images.max_height');
                                    list($width, $height) = getimagesize($_FILES['projectImageFile']['tmp_name']);

                                    if (empty($width) || empty($height) || (($width <= $imageMaxWidth) && ($height <= $imageMaxHeight))) {
//                                        $success = \SolasMatch\API\V0\IO::saveProjectImageFileToFileSystem($project->getId(), $data, $projectImageFileName, $user_id);
                                    } else { // Resize the image
                                        $ratio = min($imageMaxWidth / $width, $imageMaxHeight / $height);
                                        $newWidth  = floor($width * $ratio);
                                        $newHeight = floor($height * $ratio);

                                        $img = '';
                                        if ($extension == 'gif') {
                                            $img = imagecreatefromgif($_FILES['projectImageFile']['tmp_name']);
                                        } elseif ($extension == 'png') {
                                            $img = imagecreatefrompng($_FILES['projectImageFile']['tmp_name']);
                                        } else {
                                            $img = imagecreatefromjpeg($_FILES['projectImageFile']['tmp_name']);
                                        }

                                        $tci = imagecreatetruecolor($newWidth, $newHeight);
                                        if (!empty($img) && $tci !== false) {
                                            if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                                                imagejpeg($tci, $_FILES['projectImageFile']['tmp_name']); // Overwrite
                                                // If we did not get this far, give up and use the un-resized image
                                            }
                                        }

                                        $data = file_get_contents($_FILES['projectImageFile']['tmp_name']);
                                        if ($data !== false) {
//                                            $success = \SolasMatch\API\V0\IO::saveProjectImageFileToFileSystem($project->getId(), $data, $projectImageFileName, $user_id);
                                        } else {
                                            $success = false;
                                        }
                                    }
                                    if (!success) {
                                      $image_failed = true;
                                    }
                                }
                            }
                            if ($image_failed) {
                                $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_image'), $_FILES['projectImageFile']['name']));
                                $projectDao->deleteProject($project->getId());
                            } else {
                                // Continue here whether there is, or is not, an image file uploaded as long as there was not an explicit failure

                                // Add Tasks for the new Project
                                $targetCount = 0;
                                $creatingTasksSuccess = true;
                                $createdTasks = array();
                                while (!empty($post["target_language$targetCount"]) && !empty($post["target_country$targetCount"])) {

                                    if (!empty($post["segmentation_$targetCount"])) {
                                        // Create segmentation task
                                        $id = addProjectTask(
                                            $project,
                                            $post["target_language$targetCount"],
                                            $post["target_country$targetCount"],
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
                                            $translation_Task_Id = addProjectTask(
                                                $project,
                                                $post["target_language$targetCount"],
                                                $post["target_country$targetCount"],
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
                                                $id = addProjectTask(
                                                    $project,
                                                    $post["target_language$targetCount"],
                                                    $post["target_country$targetCount"],
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
                                            $id = addProjectTask(
                                                $project,
                                                $post["target_language$targetCount"],
                                                $post["target_country$targetCount"],
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
                                            $taskDao->deleteTask($taskIdToDelete);
                                        }
                                    }
                                    $app->flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), $_FILES['projectFile']['name']));
                                    $projectDao->deleteProject($project->getId());
                                } else {
                                    $projectDao->calculateProjectDeadlines($project->getId());

                                    $app->redirect($app->urlFor('project-view', array('project_id' => $project->getId())));
                                }
                            }
                        }
                    }
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
        for ($i = 0; $i < 10; $i++) {
            $year_list[$yeari] = $yeari++;
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
        ));
        $app->render("project/project.create1.tpl");
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
        $projectTasks = $projectDao->getProjectTasks($project->getId());
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

        $newTask = $taskDao->createTask($task);
        $newTaskId = $newTask->getId();
        $createdTasks[] = $newTaskId;

        try {
            $upload_error = $taskDao->saveTaskFile(
                $newTaskId,
                $user_id,
                $projectDao->getProjectFile($project->getId())
            );
        } catch (\Exception  $e) {
            $newTaskId = 0;
        }

        if ($newTaskId && $preReqTaskId) {
            $taskDao->addTaskPreReq($newTaskId, $preReqTaskId);
        }

        if (!empty($post['trackProject'])) {
            $userDao = new DAO\UserDao();
            $userDao->trackTask($user_id, $newTaskId);
        }

        return $newTaskId;
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
