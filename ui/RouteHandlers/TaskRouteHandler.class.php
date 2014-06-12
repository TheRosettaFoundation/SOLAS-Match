<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use SolasMatch\Common\Lib\APIHelper;

require_once __DIR__.'/../../api/lib/IO.class.php';
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

class TaskRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();

        $app->get(
            "/tasks/archive/p/:page_no/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "archivedTasks")
        )->name("archived-tasks");

        $app->get(
            "/user/:user_id/claimed/tasks/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "claimedTasks")
        )->name("claimed-tasks");

        $app->get(
            "/task/:task_id/download-task-latest-file/",
            array($middleware, "authUserForTaskDownload"),
            array($this, "downloadTaskLatestVersion")
        )->name("download-task-latest-version");
        
        $app->get(
            "/task/:task_id/mark-archived/",
            array($middleware, "authUserForOrgTask"),
            array($this, "archiveTask")
        )->name("archive-task");

        $app->get(
            "/task/:task_id/download-file-user/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "downloadTask")
        )->name("download-task");

        $app->get(
            "/task/:task_id/claim/",
            array($middleware, "isBlackListed"),
            array($this, "taskClaim")
        )->via("POST")->name("task-claim-page");

        $app->get(
            "/task/:task_id/claimed/",
            array($middleware, "authenticateUserForTask"),
            array($this, "taskClaimed")
        )->name("task-claimed");

        $app->get(
            "/task/:task_id/download-file/v/:version/",
            array($middleware, "authUserForTaskDownload"),
            array($middleware, "authUserForTaskDownload"),
            array($this, "downloadTaskVersion")
        )->name("download-task-version");

        $app->get(
            "/task/:task_id/id/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "task")
        )->via("POST")->name("task");

        $app->get(
            "/task/:task_id/desegmentation/",
            array($middleware, "authUserIsLoggedIn"),
            array($middleware, 'authenticateUserForTask'),
            array($this, "desegmentationTask")
        )->via("POST")->name("task-desegmentation");

        $app->get(
            "/task/:task_id/simple-upload/",
            array($middleware, "authUserIsLoggedIn"),
            array($middleware, 'authenticateUserForTask'),
            array($this, "taskSimpleUpload")
        )->via("POST")->name("task-simple-upload");

        $app->get(
            "/task/:task_id/segmentation/",
            array($middleware, "authUserIsLoggedIn"),
            array($middleware, 'authenticateUserForTask'),
            array($this, "taskSegmentation")
        )->via("POST")->name("task-segmentation");

        $app->get(
            "/task/:task_id/uploaded/",
            array($middleware, "authenticateUserForTask"),
            array($this, "taskUploaded")
        )->name("task-uploaded");

        $app->get(
            "/task/:task_id/alter/",
            array($middleware, "authUserForOrgTask"),
            array($this, "taskAlter")
        )->via("POST")->name("task-alter");

        $app->get(
            "/task/:task_id/view/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "taskView")
        )->via("POST")->name("task-view");

        $app->get(
            "/project/:project_id/create-task/",
            array($middleware, "authUserForOrgProject"),
            array($this, "taskCreate")
        )->via("POST")->name("task-create");

        $app->get(
            "/task/:task_id/created/",
            array($middleware, "authenticateUserForTask"),
            array($this, "taskCreated")
        )->name("task-created");
        
        $app->get(
            "/task/:task_id/org-feedback/",
            array($middleware, "authUserForOrgTask"),
            array($this, "taskOrgFeedback")
        )->via("POST")->name("task-org-feedback");
        
        $app->get(
            "/task/:task_id/user-feedback/",
            array($middleware, "authenticateUserForTask"),
            array($this, "taskUserFeedback")
        )->via("POST")->name("task-user-feedback");

        $app->get(
            "/task/:task_id/review/",
            array($middleware, "authenticateUserForTask"),
            array($this, "taskReview")
        )->via("POST")->name("task-review");
        
        $app->get(
            Common\Lib\Settings::get("site.api"),
            array($middleware, "authUserForOrgTask")
        )->name("api");
    }

    public function archivedTasks($page_no)
    {
        $app = \Slim\Slim::getInstance();
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
        
        $app->view()->setData("archivedTasks", $archivedTasks);
        $app->view()->appendData(array(
                                    "page_no" => $page_no,
                                    "last" => $totalPages,
                                    "top" => $top,
                                    "bottom" => $bottom,
                                    "taskTypeColours" => $taskTypeColours,
                                    "archivedTasksCount" => $archivedTasksCount
        ));
        $app->render("task/archived-tasks.tpl");
    }

    public function claimedTasks($userId)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $user = $userDao->getUser($userId);

        $loggedInUserId = Common\Lib\UserSession::getCurrentUserID();
        if ($loggedInUserId != $userId) {
            $adminDao = new DAO\AdminDao();
            if (!$adminDao->isSiteAdmin($loggedInUserId)) {
                $app->flash('error', "You are not authorized to view this page");
                $app->redirect($app->urlFor('home'));
            }
        }

        $extra_scripts = "
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/web_components/dart_support.js\"></script>
<script \"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/browser/interop.js\"></script>
<script \"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/Routes/Users/ClaimedTasks.dart.js\"></script>
<span class=\"hidden\">
";

        $extra_scripts .= file_get_contents("ui/dart/web/Routes/Users/ClaimedTasksStream.html");
        $extra_scripts .= "</span>";

        $platformJS =
        "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/dart/build/web/packages/web_components/platform.js\"></script>";
        $viewData = array('thisUser' => $user);
        $viewData['extra_scripts'] = $extra_scripts;
        $viewData['current_page'] = 'claimed-tasks';
        $viewData['platformJS'] = $platformJS;

        $app->view()->appendData($viewData);
        $app->render("task/claimed-tasks.tpl");
    }

    public function downloadTaskLatestVersion($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();

        $task = $taskDao->getTask($task_id);
        $latest_version = $taskDao->getTaskVersion($task_id);
        try {
            $this->downloadTaskVersion($task_id, $latest_version);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $app->flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_latest_task_file_version'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            $app->redirect($app->urlFor('home'));
        }
    }

    public function archiveTask($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();

        $task = $taskDao->getTask($task_id);
        $userId = Common\Lib\UserSession::getCurrentUserID();
        
        $taskType = Lib\TemplateHelper::getTaskTypeFromId($task->getTaskType());
        if ($result = $taskDao->archiveTask($task_id, $user_id)) {
            $app->flash(
                "success",
                sprintf(
                    Lib\Localisation::getTranslation('project_view_17'),
                    $taskType,
                    $task->getTitle()
                )
            );
        } else {
            $app->flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('project_view_18'),
                    $taskType,
                    $task->getTitle()
                )
            );
        }
             
        $app->redirect($ref = $app->request()->getReferrer());
    }

    public function downloadTask($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $convert = $app->request()->get("convertToXliff");
        
        try {
            $this->downloadTaskVersion($taskId, 0, $convert);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $app->flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_original_task_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            $app->redirect($app->urlFor('home'));
        }
    }

    /*
     *  Claim and download a task
     */
    public function taskClaim($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $languageDao = new DAO\LanguageDao();

        $task = $taskDao->getTask($taskId);
        if ($app->request()->isPost()) {
            $user_id = Common\Lib\UserSession::getCurrentUserID();
            $userDao->claimTask($user_id, $taskId);
            $app->redirect($app->urlFor("task-claimed", array(
                "task_id" => $taskId
            )));
        }

        $convert = $app->request()->get("convertToXliff");
        if (!is_null($convert)) {
            $app->view()->setData("convert", $convert);
        } else {
            $app->view()->setData("convert", "false");
        }
        
        $sourcelocale = $task->getSourceLocale();
        $targetLocale = $task->getTargetLocale();
        $sourceLanguage = $languageDao->getLanguageByCode($sourcelocale->getLanguageCode());
        $targetLanguage = $languageDao->getLanguageByCode($targetLocale->getLanguageCode());
        $taskMetaData = $taskDao->getTaskInfo($taskId);

        // Used in proofreading page, link to original project file
        $projectFileDownload = $app->urlFor("home")."project/".$task->getProjectId()."/file";
        
        
        $app->view()->appendData(array(
                    "projectFileDownload" => $projectFileDownload,
                    "task"          => $task,
                    "sourceLanguage"=> $sourceLanguage,
                    "targetLanguage"=> $targetLanguage,
                    "taskMetadata"  => $taskMetaData
        ));
       
        $app->render("task/task.claim.tpl");
    }

    public function taskClaimed($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();

        $task = $taskDao->getTask($task_id);
        $app->view()->setData("task", $task);
        $app->render("task/task.claimed.tpl");
    }

    public function downloadTaskVersion($taskId, $version, $convert = 0)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        
        $headerArr = $taskDao->downloadTaskVersion($taskId, $version, $convert);
        $headerArr = json_decode($headerArr);
        foreach ($headerArr as $key => $val) {
            $app->response->headers->set($key, $val);
        }
    }

    public function task($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);
        if (is_null($task)) {
            $app->flash("error", sprintf(Lib\Localisation::getTranslation('task_view_5'), $taskId));
            $app->redirect($app->urlFor("home"));
        }
        $taskClaimed = $taskDao->isTaskClaimed($taskId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            $project = $projectDao->getProject($task->getProjectId());
            $org_id=$project->getOrganisationId();

            if (isset($post['trackOrganisation'])) {
                if ($post['trackOrganisation']) {
                    $userTrackOrganisation = $userDao->trackOrganisation($user_id, $org_id);
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
                    $userUntrackOrganisation = $userDao->unTrackOrganisation($user_id, $org_id);
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
        if ($taskClaimed) {
            $app->flashKeep();
            switch ($task->getTaskType()) {
                case Common\Enums\TaskTypeEnum::DESEGMENTATION:
                    $app->redirect($app->urlFor("task-desegmentation", array("task_id" => $taskId)));
                    break;
                case Common\Enums\TaskTypeEnum::TRANSLATION:
                case Common\Enums\TaskTypeEnum::PROOFREADING:
                    $app->redirect($app->urlFor("task-simple-upload", array("task_id" => $taskId)));
                    break;
                case Common\Enums\TaskTypeEnum::SEGMENTATION:
                    $app->redirect($app->urlFor("task-segmentation", array("task_id" => $taskId)));
                    break;
            }
        } else {
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
        
            $converter = Common\Lib\Settings::get("converter.converter_enabled");
        
            $task_file_info = $taskDao->getTaskInfo($taskId);
            $siteLocation = Common\Lib\Settings::get("site.location");
            $file_path = "{$siteLocation}task/$taskId/download-file-user/";

            $app->view()->appendData(array(
                "taskTypeColours" => $taskTypeColours,
                "project" => $project,
                "converter" => $converter,
                "task" => $task,
                "file_preview_path" => $file_path,
                "filename" => $task_file_info->getFilename(),
                "isMember" => $isMember,
                'userSubscribedToOrganisation' => $userSubscribedToOrganisation
            ));

            $app->render("task/task.view.tpl");
        }
    }

    public function desegmentationTask($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();

        $userId = Common\Lib\UserSession::getCurrentUserID();
        $fieldName = "mergedFile";
        $errorMessage = null;
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());

        if ($app->request()->isPost()) {
            $uploadError = false;
            try {
                Lib\TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
            } catch (\Exception $e) {
                $uploadError = true;
                $errorMessage = $e->getMessage();
            }

            if (!$uploadError) {
                try {
                    $filedata = file_get_contents($_FILES[$fieldName]['tmp_name']);
                    $taskDao->saveTaskFile($taskId, $userId, $filedata);
                } catch (\Exception  $e) {
                    $uploadError = true;
                    $errorMessage = $e->getMessage();
                }
            }

            if (is_null($errorMessage)) {
                $app->redirect($app->urlFor("task-review", array("task_id" => $taskId)));
            } else {
                $app->flashNow("error", $errorMessage);
            }
        }

        $graphBuilder = new Lib\UIWorkflowBuilder();
        $graph = $projectDao->getProjectGraph($task->getProjectId());
        $index = $graphBuilder->find($task->getId(), $graph);
        $node = $graph->getAllNodes($index);

        if ($node) {
            foreach ($node->getPreviousList() as $nodeId) {
                $pTask = $taskDao->getTask($nodeId);
                if (is_object($pTask)) {
                    $preReqTasks[] = $pTask;
                }
            }
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");

        $taskTypeColours = array();
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $converter = Common\Lib\Settings::get("converter.converter_enabled");
        
        $app->view()->appendData(array(
            "task"          => $task,
            "project"       => $project,
            "preReqTasks"   => $preReqTasks,
            "fieldName"     => $fieldName,
            "errorMessage"  => $errorMessage,
            "converter"     => $converter,
            "taskTypeColours"   => $taskTypeColours
        ));

        $app->render("task/task-desegmentation.tpl");
    }

    public function taskSimpleUpload($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        
        $fieldName = "fileUpload";
        $errorMessage = null;
        $userId = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            try {
                Lib\TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
                $projectFile = $projectDao->getProjectFileInfo($project->getId());
                $projectFileMimeType = $projectFile->getMime();
                $projectFileType = pathinfo($projectFile->getFilename(), PATHINFO_EXTENSION);
                
                $fileUploadType = pathinfo($_FILES[$fieldName]["name"], PATHINFO_EXTENSION);
                
                //Call API to determine MIME type of file contents
                $helper = new Common\Lib\APIHelper(Common\Lib\Settings::get('ui.api_format'));
                $siteApi = Common\Lib\Settings::get("site.api");
                $filename = $_FILES[$fieldName]["name"];
                $request = $siteApi."v0/io/contentMime/$filename";
                error_log("REQUEST IS: $request");
                $data = file_get_contents($_FILES[$fieldName]["tmp_name"]);
                $fileUploadMime = $helper->call(null, $request, Common\Enums\HttpMethodEnum::GET, null, null, $data);
                error_log("Got mime from calling API");
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
                error_log("caught generic exception with error message: $errorMessage");
            }
        
            if (is_null($errorMessage)) {
                try {
                    $filedata = file_get_contents($_FILES[$fieldName]["tmp_name"]);
                    
                    if ($post['submit'] == 'XLIFF') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata, true);
                    } elseif ($post['submit'] == 'submit') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata);
                    }
                
                } catch (\Exception  $e) {
                    $errorMessage = Lib\Localisation::getTranslation('task_simple_upload_7') . $e->getMessage();
                }
            }

            if (is_null($errorMessage)) {
                $app->redirect($app->urlFor("task-review", array("task_id" => $taskId)));
            } else {
                $app->flashNow("error", $errorMessage);
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

        $app->view()->appendData(array(
            "task"          => $task,
            "project"       => $project,
            "org"           => $org,
            "filename"      => $filename,
            "converter"     => $converter,
            "fieldName"     => $fieldName,
            "max_file_size" => Lib\TemplateHelper::maxFileSizeMB(),
            "taskTypeColours"   => $taskTypeColours,
            "file_previously_uploaded" => $file_previously_uploaded
        ));

        $app->render("task/task-simple-upload.tpl");
    }

    public function taskUploaded($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $tipDao = new DAO\TipDao();

        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $tip = $tipDao->getTip();
        
        $app->view()->appendData(array(
            "org_name" => $org->getName(),
            "tip"      => $tip
        ));
        
        $app->render("task/task.uploaded.tpl");
    }

    public function taskAlter($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $currentTask = $taskDao->getTask($task_id);
        $currentTaskStatus = $currentTask->getTaskStatus();

        $word_count_err = null;
        $deadlockError = null;
        $deadlineError = "";

        $extra_scripts = "
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\"></script>
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/datetime-picker.js\"></script>";

        $task = $taskDao->getTask($task_id);

        $preReqTasks = $taskDao->getTaskPreReqs($task_id);
        if (!$preReqTasks) {
            $preReqTasks = array();
        }

        $project = $projectDao->getProject($task->getProjectId());
        $projectTasks = $projectDao->getProjectTasks($task->getProjectId());
        foreach ($projectTasks as $projectTask) {
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
      
        $app->view()->setData("task", $task);
        
        if (\SolasMatch\UI\isValidPost($app)) {
            $post = $app->request()->post();
           
            if ($task->getTaskStatus() < Common\Enums\TaskStatusEnum::IN_PROGRESS) {
                if (isset($post['title']) && $post['title'] != "") {
                    $task->setTitle($post['title']);
                }

                if (isset($post['publishTask']) && $post['publishTask']) {
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);
                }
            
                $targetLocale = new Common\Protobufs\Models\Locale();
            
                if (isset($post['target']) && $post['target'] != "") {
                    $targetLocale->setLanguageCode($post['target']);
                }
             
                if (isset($post['targetCountry']) && $post['targetCountry'] != "") {
                    $targetLocale->setCountryCode($post['targetCountry']);
                }
            
                $task->setTargetLocale($targetLocale);
              
                if (isset($post['word_count']) && ctype_digit($post['word_count'])) {
                    $task->setWordCount($post['word_count']);
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
                } else {
                    $deadlineError = Lib\Localisation::getTranslation('task_alter_8');
                }
            }
            
            if (isset($post['impact']) && $post['impact'] != "") {
                $task->setComment($post['impact']);
            }

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
                $taskPreReqIds[$task->getId()] = $thisTaskPreReqs;
                $graphBuilder = new Lib\UIWorkflowBuilder();
                $graph = $graphBuilder->parseAndBuild($taskPreReqIds);
                
                if ($graph) {

                    $index = $graphBuilder->find($task->getId(), $graph);
                    $node = $graph->getAllNodes($index);
                    $selectedList = array();
                    foreach ($node->getPreviousList() as $prevId) {
                        $selectedList[] = $prevId;
                    }

                    $taskDao->updateTask($task);
                    if ($preReqTasks) {
                        foreach ($preReqTasks as $preReqTask) {
                            if (!in_array($preReqTask->getId(), $selectedList)) {
                                $taskDao->removeTaskPreReq($task->getId(), $preReqTask->getId());
                            }
                        }
                    }

                    foreach ($selectedList as $taskId) {
                        if (is_numeric($taskId)) {
                            $taskDao->addTaskPreReq($task->getId(), $taskId);
                        }
                    }

                    $app->redirect($app->urlFor("task-view", array("task_id" => $task_id)));
                } else {
                    //A deadlock occured
                    $deadlockError = Lib\Localisation::getTranslation('task_alter_9');
                    //Reset prereqs so as not to crash second run of the graph builder
                    $taskPreReqIds[$task->getId()] = $oldPreReqs;
                }
            }
        }
        
        $graphBuilder = new Lib\UIWorkflowBuilder();
        //Maybe replace with an API call
        $graph = $graphBuilder->parseAndBuild($taskPreReqIds);
                
        if ($graph) {

            $index = $graphBuilder->find($task_id, $graph);
            $node = $graph->getAllNodes($index);

            $currentRow = $node->getPreviousList();
            $previousRow = array();

            while (count($currentRow) > 0) {
                foreach ($currentRow as $nodeId) {
                    $index = $graphBuilder->find($nodeId, $graph);
                    $node = $graph->getAllNodes($index);
                    $tasksEnabled[$node->getTaskId()] = false;

                    foreach ($node->getPreviousList() as $prevIndex) {
                        if (!in_array($prevIndex, $previousRow)) {
                            $previousRow[] = $prevIndex;
                        }
                    }
                }
                $currentRow = $previousRow;
                $previousRow = array();
            }
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for ($i = 1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }
        
        $languages = Lib\TemplateHelper::getLanguageList();
        $countries = Lib\TemplateHelper::getCountryList();
       
        $app->view()->appendData(array(
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
            "taskTypeColours"     => $taskTypeColours
        ));
        
        $app->render("task/task.alter.tpl");
    }

    public function taskView($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $user = $userDao->getUser($user_id);
        
        if ($task_file_info = $taskDao->getTaskInfo($task_id)) {
            $app->view()->appendData(array(
                'task_file_info' => $task_file_info,
                'latest_version' => $taskDao->getTaskVersion($task_id)
            ));
        }
        $task_file_info = $taskDao->getTaskInfo($task_id, 0);
        $siteLocation = Common\Lib\Settings::get("site.location");
        $file_path= "{$siteLocation}task/$task_id/download-file-user/";
       
        $app->view()->appendData(array(
            "file_preview_path" => $file_path,
            "filename" => $task_file_info->getFilename()
        ));
        
         
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['published'])) {
                if ($post['published']) {
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);
                }
                if ($taskDao->updateTask($task)) {
                    if ($post['published']) {
                        $app->flashNow("success", Lib\Localisation::getTranslation('task_view_1'));
                    } else {
                        $app->flashNow("success", Lib\Localisation::getTranslation('task_view_2'));
                    }
                } else {
                    if ($post['published']) {
                        $app->flashNow("error", Lib\Localisation::getTranslation('task_view_3'));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('task_view_4'));
                    }
                }
            }

            if (isset($post['track'])) {
                if ($post['track'] == "Ignore") {
                    $response = $userDao->untrackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", Lib\Localisation::getTranslation('task_view_10'));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('task_view_11'));
                    }
                } else {
                    $response = $userDao->trackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", Lib\Localisation::getTranslation('task_view_12'));
                    } else {
                        $app->flashNow("error", Lib\Localisation::getTranslation('task_view_13'));
                    }
                }
            }

            if (isset($post['trackOrganisation'])) {
                $org_id = $project->getOrganisationId();
                if ($post['trackOrganisation']) {
                    $userTrackOrganisation = $userDao->trackOrganisation($user_id, $org_id);
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
                    $userUntrackOrganisation = $userDao->unTrackOrganisation($user_id, $org_id);
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
        
        $taskMetaData = array();
        $metaData = array();
        $registered = $userDao->isSubscribedToTask($user_id, $task_id);
        if ($registered == 1) {
            $metaData["tracking"] = true;
        } else {
            $metaData["tracking"] = false;
        }
        $taskMetaData[$task_id] = $metaData;

        $app->view()->appendData(array(
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
        $adminDao = new DAO\AdminDao();
        $isSiteAdmin = $adminDao->isSiteAdmin($user_id);
        if ($isOrgMember || $isSiteAdmin) {
            $app->view()->appendData(array("isOrgMember" => $isOrgMember));
        }
        $userSubscribedToOrganisation = $userDao->isSubscribedToOrganisation($user_id, $project->getOrganisationId());

        $app->view()->appendData(array(
                "org" => $org,
                "project" => $project,
                "registered" => $registered,
                "taskTypeColours" => $taskTypeColours,
                "isMember" => $isOrgMember,
                "userSubscribedToOrganisation" => $userSubscribedToOrganisation
        ));

        $app->render("task/task.view.tpl");
    }

    public function taskCreate($project_id)
    {
        $app = \Slim\Slim::getInstance();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $titleError = null;
        $wordCountError = null;
        $deadlineError = null;
        $taskPreReqs = array();
        $task = new Common\Protobufs\Models\Task();
        $project = $projectDao->getProject($project_id);
        $projectTasks = $projectDao->getProjectTasks($project_id);
        $task->setProjectId($project_id);

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
                    $app->redirect($app->urlFor("task-created", array("task_id" => $newTaskId)));
                } else {
                    $taskDao->deleteTask($newTaskId);
                    $app->view()->appendData(array("upload_error" => $upload_error));
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
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/lib/jquery-ui-timepicker-addon.js\"></script>
<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/datetime-picker.js\"></script>
";

        $app->view()->appendData(array(
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
            "taskTypeColours" => $taskTypeColours
        ));

        $app->render("task/task.create.tpl");
    }

    public function taskCreated($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $task = $taskDao->getTask($taskId);
        $app->view()->appendData(array(
                "project_id" => $task->getProjectId(),
                "task_id"    => $task->getId()
        ));

        $app->render("task/task.created.tpl");
    }
    
    public function taskSegmentation($task_id)
    {
        $app = \Slim\Slim::getInstance();

        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $taskTypeErr = null;
        
        $task = $taskDao->getTask($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $maxSegments = Common\Lib\Settings::get("site.max_segmentation");
        $taskTypeColours = array();
        
        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }
    
        $language_list = Lib\TemplateHelper::getLanguageList();
        $countries = Lib\TemplateHelper::getCountryList();
        
        if ($app->request()->isPost() && $task->getTaskStatus() != Common\Enums\TaskStatusEnum::COMPLETE) {
            $post = $app->request()->post();
            
            $fileInfo = $projectDao->getProjectFileInfo($project->getId());
            $canonicalExtension = explode(".", $fileInfo->getFileName());
            $canonicalExtension = strtolower($canonicalExtension[count($canonicalExtension)-1]);
            
            $errors = array();
            $fileNames = array();
            $fileHashes = array();
            foreach ($_FILES as $file) {
                $extension = explode(".", $file["name"]);
                $extension = strtolower($extension[count($extension)-1]);
                if ($extension != $canonicalExtension) {
                    $errors["incorrectExtension"] = sprintf(
                        Lib\Localisation::getTranslation("common_task_file_extension_mismatch"),
                        $canonicalExtension
                    );
                    break;
                }
                
                if ($file["error"] != UPLOAD_ERR_OK) {
                    $errors["missingFile"] = Lib\Localisation::getTranslation('task_segmentation_15');
                    break;
                }
                if (!in_array($file["name"], $fileNames)) {
                    $fileNames[] = $file["name"];
                } else {
                    $errors["uniqueFileName"] = Lib\Localisation::getTranslation('task_segmentation_16');
                    break;
                }
                
                $hash = md5_file($file["tmp_name"]);
                if (!in_array($hash, $fileHashes)) {
                    $fileHashes[] = $hash;
                } else {
                    $errors["duplicateFileContent"] = Lib\Localisation::getTranslation('task_segmentation_17');
                    break;
                }
            }
            
            if (!isset($post["translation_0"]) && !isset($post["proofreading_0"])) {
                $errors["taskTypeSet"] = Lib\Localisation::getTranslation('task_segmentation_18');
            }
            
            if (empty($errors)) {
                $segmentationValue = $post["segmentationValue"];
                $upload_error = false;
                $translationTaskIds = array();
                $proofreadTaskIds = array();
                for ($i=0; $i < $segmentationValue; $i++) {
                    try {
                        Lib\TemplateHelper::validateFileHasBeenSuccessfullyUploaded("segmentationUpload_".$i);
                        $taskModel = new Common\Protobufs\Models\Task();
                        $this->setTaskModelData($taskModel, $project, $task, $i, $segmentationValue);
                        if (isset($post["translation_0"])) {
                            $taskModel->setTaskType(Common\Enums\TaskTypeEnum::TRANSLATION);
                            $taskModel->setWordCount($post["wordCount_$i"]);
                            $createdTranslation = $taskDao->createTask($taskModel);
                            $translationTaskIds[] = $createdTranslation->getId();
                            try {
                                $filedata = file_get_contents($_FILES['segmentationUpload_'.$i]['tmp_name']);
                                $taskDao->saveTaskFile($createdTranslation->getId(), $user_id, $filedata);
                            } catch (\Exception  $e) {
                                $upload_error = true;
                                $errors["transTask$i"] = "<strong>File #$i:</strong> {$e->getMessage()}";
                            }
                            
                        }

                        if (isset($post["proofreading_0"])) {
                            $taskModel->setTaskType(Common\Enums\TaskTypeEnum::PROOFREADING);
                            $taskModel->setWordCount($post["wordCount_$i"]);
                            $createdProofReading = $taskDao->createTask($taskModel);
                            $proofreadTaskIds[] = $createdProofReading->getId();
                            try {
                                $filedata = file_get_contents($_FILES['segmentationUpload_'.$i]['tmp_name']);
                                $taskDao->saveTaskFile($createdProofReading->getId(), $user_id, $filedata);
                            } catch (\Exception  $e) {
                                $upload_error = true;
                                $errors["proofTask$i"] = "<strong>File #$i:</strong> {$e->getMessage()}";
                                $taskDao->deleteTask($createdProofReading->getId());
                            }
                        }
                    } catch (\Exception $e) {
                        $upload_error = true;
                        $error_message = $e->getMessage();
                    }
                }
                
                if (!$upload_error) {

                    $taskModel = new Common\Protobufs\Models\Task();
                    $this->setTaskModelData($taskModel, $project, $task);
                    $taskModel->setWordCount($task->getWordCount());
                    $taskModel->setTaskType(Common\Enums\TaskTypeEnum::DESEGMENTATION);
                    $createdDesegmentation = $taskDao->createTask($taskModel);
                    $createdDesegmentationId = $createdDesegmentation->getId();

                    try {
                        $filedata = file_get_contents($_FILES["segmentationUpload_0"]["tmp_name"]);
                        $error_message = $taskDao->saveTaskFile($createdDesegmentation->getId(), $user_id, $filedata);
                    } catch (Common\Exceptions\SolasMatchException  $e) {
                        $upload_error = true;
                        $error_message = "File error: " . $e->getMessage();
                    }

                    $task->setTaskStatus(Common\Enums\TaskStatusEnum::COMPLETE);
                    $taskDao->updateTask($task);
                    for ($i=0; $i < $segmentationValue; $i++) {
                        if (isset($post["translation_0"]) && isset($post["proofreading_0"])) {
                            $taskDao->addTaskPreReq($translationTaskIds[$i], $task_id);
                            $taskDao->addTaskPreReq($proofreadTaskIds[$i], $translationTaskIds[$i]);
                            $taskDao->addTaskPreReq($createdDesegmentationId, $proofreadTaskIds[$i]);
                        }
                        if (!isset($post["translation_0"]) && isset($post["proofreading_0"])) {
                            $taskDao->addTaskPreReq($proofreadTaskIds[$i], $task_id);
                            $taskDao->addTaskPreReq($createdDesegmentationId, $proofreadTaskIds[$i]);
                        }
                        if (isset($post["translation_0"]) && !isset($post["proofreading_0"])) {
                            $taskDao->addTaskPreReq($translationTaskIds[$i], $task_id);
                            $taskDao->addTaskPreReq($createdDesegmentationId, $translationTaskIds[$i]);
                        }
                    }
                    $projectDao->calculateProjectDeadlines($project->getId());
                    $app->redirect($app->urlFor("task-review", array("task_id" => $task->getId())));
                } else {
                    if (!empty($translationTaskIds)) {
                        foreach ($translationTaskIds as $taskId) {
                            $taskDao->deleteTask($taskId);
                        }
                    }
                    if (!empty($proofreadTaskIds)) {
                        foreach ($proofreadTaskIds as $taskId) {
                            $taskDao->deleteTask($taskId);
                        }
                    }
                    $app->view()->appendData(array(
                        "errors" => $errors
                    ));
                }
            } else {
                $app->view()->appendData(array(
                    "errors" => $errors
                ));
            }
        }
        
        $extraScripts = file_get_contents(
            "http://".$_SERVER["HTTP_HOST"]."{$app->urlFor("home")}ui/js/task-segmentation.js"
        );
        
        $app->view()->appendData(array(
            "project"           => $project,
            "task"              => $task,
            "taskTypeColours"   => $taskTypeColours,
            "maxSegmentation"   => $maxSegments,
            "languages"         => $language_list,
            "countries"         => $countries,
            "extra_scripts"     => $extraScripts
        ));
        
        $app->render("task/task-segmentation.tpl");
    }
    
    public function taskOrgFeedback($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
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

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if (isset($post['feedback'])) {
                if ($post['feedback'] != "") {
                    $taskDao->sendOrgFeedback($task_id, $user_id, $claimant->getId(), $post['feedback']);
                    $app->flashNow(
                        "success",
                        sprintf(
                            Lib\Localisation::getTranslation('task_org_feedback_6'),
                            $app->urlFor("user-public-profile", array("user_id" => $claimant->getId())),
                            $claimant->getDisplayName()
                        )
                    );
                    if (isset($post['revokeTask']) && $post['revokeTask']) {
                        $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                        $taskDao->updateTask($task);
                        $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id);
                        if ($taskRevoke) {
                            $app->flash(
                                "taskSuccess",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_org_feedback_3'),
                                    $app->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle(),
                                    $app->urlFor("user-public-profile", array("user_id" => $claimant->getId())),
                                    $claimant->getDisplayName()
                                )
                            );
                            $app->redirect($app->urlFor("project-view", array("project_id" => $task->getProjectId())));
                        } else {
                            $app->flashNow(
                                "error",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_org_feedback_4'),
                                    $app->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle(),
                                    $app->urlFor("user-public-profile", array("user_id" => $claimant->getId())),
                                    $claimant->getDisplayName()
                                )
                            );
                        }
                    }
                } else {
                    $app->flashNow("error", Lib\Localisation::getTranslation('task_org_feedback_5'));
                }
            }
        }
        
        $app->view()->appendData(array(
            "project" => $project,
            "task" => $task,
            "taskClaimedDate" => $taskClaimedDate,
            "claimant" => $claimant,
            "taskTypeColours" => $taskTypeColours,
            "task_tags" => $task_tags
        ));
        
        $app->render("task/task.org-feedback.tpl");
    }
    
    public function taskUserFeedback($task_id)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $projectDao = new DAO\ProjectDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);
        $taskClaimedDate = $taskDao->getClaimedDate($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $organisation = $orgDao->getOrganisation($project->getOrganisationId());
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['feedback'])) {
                if ($post['feedback'] != '') {
                    $taskDao->sendUserFeedback($task_id, $claimant->getId(), $post['feedback']);
                    if (isset($post['revokeTask']) && $post['revokeTask']) {
                        $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id);
                        if ($taskRevoke) {
                            $app->flash(
                                "success",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_user_feedback_3'),
                                    $app->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle()
                                )
                            );
                            $app->redirect($app->urlFor("home"));
                        } else {
                            $app->flashNow(
                                "error",
                                sprintf(
                                    Lib\Localisation::getTranslation('task_user_feedback_4'),
                                    $app->urlFor("task-view", array("task_id" => $task_id)),
                                    $task->getTitle()
                                )
                            );
                        }
                    } else {
                        $orgProfile = $app->urlFor("org-public-profile", array('org_id' => $organisation->getId()));
                        $app->flash(
                            "success",
                            sprintf(
                                Lib\Localisation::getTranslation('task_org_feedback_6'),
                                $orgProfile,
                                $organisation->getName()
                            )
                        );
                        $app->redirect($app->urlFor("task", array("task_id" => $task_id)));
                    }
                } else {
                    $app->flashNow('error', Lib\Localisation::getTranslation('task_user_feedback_5'));
                }
            }
        }
        
        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }
        
        $app->view()->appendData(array(
            "org" => $organisation,
            "project" => $project,
            "task" => $task,
            "taskClaimedDate" =>$taskClaimedDate,
            "claimant" => $claimant,
            "taskTypeColours" => $taskTypeColours,
            "task_tags" => $task_tags
        ));
        
        $app->render("task/task.user-feedback.tpl");
    }

    public function taskReview($taskId)
    {
        $app = \Slim\Slim::getInstance();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $userId = Common\Lib\UserSession::getCurrentUserID();

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
        if ($preReqTasks == null || count($preReqTasks) == 0) {
            $projectDao = new \SolasMatch\UI\DAO\ProjectDao();
            $project = $projectDao->getProject($task->getProjectId());

            $reviews = $projectDao->getProjectReviews($task->getProjectId());
            if ($reviews) {
                foreach ($reviews as $projectReview) {
                    if ($projectReview->getTaskId() == null
                            && $projectReview->getUserId() == $userId) {
                        $reviews[$task->getProjectId()] = $projectReview;
                    }
                }
            }

            $dummyTask = new Common\Protobufs\Models\Task();        //Create a dummy task to hold the project info
            $dummyTask->setProjectId($task->getProjectId());
            $dummyTask->setTitle($project->getTitle());
            $preReqTasks = array();
            $preReqTasks[] = $dummyTask;
        } else {
            foreach ($preReqTasks as $pTask) {
                if ($taskReview = $userDao->getUserTaskReviews($userId, $pTask->getId())) {
                    $reviews[$pTask->getId()] = $taskReview;
                }
            }
        }

        if (count($reviews) > 0) {
            $app->flashNow("info", Lib\Localisation::getTranslation('task_review_4'));
        }

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['submitReview'])) {
                $i = 0;
                $error = null;
                while ($i < count($preReqTasks) && $error == null) {
                    $pTask = $preReqTasks[$i++];
                    $review = new Common\Protobufs\Models\TaskReview();
                    $id = $pTask->getId();

                    $review->setUserId($userId);
                    $review->setTaskId($id);
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
                        if ($value > 0 && $value <= 5) {
                            $review->setConsistency($value);
                        } else {
                            $error = Lib\Localisation::getTranslation('task_review_8');
                        }
                    }
                    if (isset($post["comment_$id"]) && $post["comment_$id"] != "") {
                        $review->setComment($post["comment_$id"]);
                    }

                    if ($review->getProjectId() != null && $review->getUserId() != null && $error == null) {
                        if (!$taskDao->submitReview($review)) {
                            $error = sprintf(Lib\Localisation::getTranslation('task_review_9'), $pTask->getTitle());
                        }
                    } else {
                        if ($error != null) {
                            $app->flashNow("error", $error);
                        }
                    }
                }
                if ($error == null) {
                    $app->flash(
                        "success",
                        sprintf(Lib\Localisation::getTranslation('task_review_10'), $pTask->getTitle())
                    );
                    $app->redirect($app->urlFor('task-uploaded', array("task_id" => $taskId)));
                } else {
                    $app->flashNow("error", $error);
                }
            }

            if (isset($post['skip'])) {
                $app->redirect($app->urlFor('task-uploaded', array("task_id" => $taskId)));
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

        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";
        $extra_scripts .= file_get_contents(__DIR__."/../js/review.js");

        $formAction = $app->urlFor("task-review", array('task_id' => $taskId));

        $app->view()->appendData(array(
            'extra_scripts' => $extra_scripts,
            'taskId'        => $taskId,
            'tasks'         => $preReqTasks,
            'reviews'       => $reviews,
            'formAction'    => $formAction,
            'action'        => $action
        ));

        $app->render("task/task.review.tpl");
    }
    
    private function setTaskModelData($taskModel, $project, $task, $i = null, $segmentationValue = null)
    {
        if (is_null($i) && is_null($segmentationValue)) {
            $taskModel->setTitle($project->getTitle());
        } else {
            $taskModel->setTitle($project->getTitle()." (".($i+1)." of $segmentationValue)");
        }
        
        $taskModel->setSourceLocale($project->getSourceLocale());
        $taskModel->setTargetLocale($task->getTargetLocale());
        
        $taskModel->setProjectId($project->getId());
        $taskModel->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);
    }
}

$route_handler = new TaskRouteHandler();
$route_handler->init();
unset ($route_handler);
