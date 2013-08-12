<?php

require_once __DIR__.'/../../api/lib/IO.class.php';
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

class TaskRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get("/tasks/archive/p/:page_no", array($middleware, "authUserIsLoggedIn")
        , array($this, "archivedTasks"))->name("archived-tasks");

        $app->get("/tasks/claimed/p/:page_no", array($middleware, "authUserIsLoggedIn")
        , array($this, "claimedTasks"))->name("claimed-tasks");        

        $app->get("/task/:task_id/download-task-latest-file", array($middleware, "authUserForTaskDownload")
        , array($this, "downloadTaskLatestVersion"))->name("download-task-latest-version");
        
        $app->get("/task/:task_id/mark-archived", array($middleware, "authUserForOrgTask")
        , array($this, "archiveTask"))->name("archive-task");

        $app->get("/task/:task_id/download-file-user", array($middleware, "authUserIsLoggedIn")
        , array($this, "downloadTask"))->name("download-task");

        $app->get("/task/:task_id/claim", array($middleware, "isBlackListed")
        , array($this, "taskClaim"))->via("POST")->name("task-claim-page");

        $app->get("/task/:task_id/claimed", array($middleware, "authenticateUserForTask")
        , array($this, "taskClaimed"))->name("task-claimed");

        $app->get("/task/:task_id/download-file/v/:version", array($middleware, "authUserForTaskDownload")
        , array($middleware, "authUserForTaskDownload")
        , array($this, "downloadTaskVersion"))->name("download-task-version");

        $app->get("/task/:task_id/id", array($middleware, "authUserIsLoggedIn")
        , array($this, "task"))->via("POST")->name("task");

        $app->get("/task/:task_id/desegmentation", array($middleware, "authUserIsLoggedIn"), 
        array($middleware, 'authenticateUserForTask'), 
        array($this, "desegmentationTask"))->via("POST")->name("task-desegmentation");

        $app->get("/task/:task_id/simple-upload", array($middleware, "authUserIsLoggedIn"),
        array($middleware, 'authenticateUserForTask'),
        array($this, "taskSimpleUpload"))->via("POST")->name("task-simple-upload");

        $app->get("/task/:task_id/segmentation", array($middleware, "authUserIsLoggedIn"),
        array($middleware, 'authenticateUserForTask'),
        array($this, "taskSegmentation"))->via("POST")->name("task-segmentation");

        $app->get("/task/:task_id/uploaded", array($middleware, "authenticateUserForTask")
        , array($this, "taskUploaded"))->name("task-uploaded");

        $app->get("/task/:task_id/alter", array($middleware, "authUserForOrgTask")
        , array($this, "taskAlter"))->via("POST")->name("task-alter");

        $app->get("/task/:task_id/view", array($middleware, "authUserIsLoggedIn")
        , array($this, "taskView"))->via("POST")->name("task-view");

        $app->get("/project/:project_id/create-task", array($middleware, "authUserForOrgProject")
        , array($this, "taskCreate"))->via("GET", "POST")->name("task-create");

        $app->get("/task/:task_id/created", array($middleware, "authenticateUserForTask")
        , array($this, "taskCreated"))->name("task-created");
        
        $app->get("/task/:task_id/org-feedback/", array($middleware, "authUserForOrgTask")
        , array($this, "taskOrgFeedback"))->via("POST")->name("task-org-feedback");
        
        $app->get("/task/:task_id/user-feedback/", array($middleware, "authenticateUserForTask")
        , array($this, "taskUserFeedback"))->via("POST")->name("task-user-feedback");   

        $app->get("/task/:task_id/review", array($middleware, "authenticateUserForTask")
        , array($this, "taskReview"))->via("POST")->name("task-review");
        
        $app->get(Settings::get("site.api"), array($middleware, "authUserForOrgTask"))->name("api");
    }

    public function archivedTasks($page_no)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $user_id = UserSession::getCurrentUserID();
        
        $user = $userDao->getUser($user_id);
        $archived_tasks = $userDao->getUserArchivedTasks($user_id, 10);
        $tasks_per_page = 10;
        $total_pages = ceil(count($archived_tasks) / $tasks_per_page);
        
        if ($page_no < 1) {
            $page_no = 1;
        } elseif ($page_no > $total_pages) {
            $page_no = $total_pages;
        }   
        
        $top = (($page_no - 1) * $tasks_per_page);
        $bottom = $top + $tasks_per_page - 1;
        
        if ($top < 0) {
            $top = 0;
        } elseif ($top > count($archived_tasks) - 1) {
            $top = count($archived_tasks) - 1; 
        }
        
        if ($bottom < 0) {
            $bottom = 0;
        } elseif ($bottom > count($archived_tasks) - 1) {
            $bottom = count($archived_tasks) - 1; 
        }   
        
        $app->view()->setData("archived_tasks", $archived_tasks);
        $app->view()->appendData(array(
                                    "page_no" => $page_no,
                                    "last" => $total_pages,
                                    "top" => $top,
                                    "bottom" => $bottom
        ));
        $app->render("task/archived-tasks.tpl");
    }

    public function claimedTasks($page_no)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();

        $user_id = UserSession::getCurrentUserID();

        $activeTasks = $userDao->getUserTasks($user_id);
        if ($activeTasks) {
            for ($i = 0; $i < count($activeTasks); $i++) {
                $activeTasks[$i]['Project'] = $projectDao->getProject($activeTasks[$i]->getProjectId());
                $activeTasks[$i]['Org'] = $orgDao->getOrganisation($activeTasks[$i]['Project']->getOrganisationId());
            }
        }
        
        $tasks_per_page = 10;
        $total_pages = ceil(count($activeTasks) / $tasks_per_page);
        
        if ($page_no < 1) {
            $page_no = 1;
        } elseif ($page_no > $total_pages) {
            $page_no = $total_pages;
        }   
        
        $top = (($page_no - 1) * $tasks_per_page);
        $bottom = $top + $tasks_per_page - 1;
        
        if ($top < 0) {
            $top = 0;
        } elseif ($top > count($activeTasks) - 1) {
            $top = count($activeTasks) - 1; 
        }   
        
        if ($bottom < 0) {
            $bottom = 0;
        } elseif ($bottom > count($activeTasks) - 1) {
            $bottom = count($activeTasks) - 1;
        }
        
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }    
        
        $app->view()->setData("active_tasks", $activeTasks);
        $app->view()->appendData(array(
                        "page_no" => $page_no,
                        "last" => $total_pages,
                        "top" => $top,
                        "bottom" => $bottom,
                        "current_page" => "claimed-tasks",
                        "taskTypeColours" => $taskTypeColours
        ));
        
        $app->render("task/claimed-tasks.tpl");
    }

    public function downloadTaskLatestVersion($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

        $task = $taskDao->getTask($task_id);
        $latest_version = $taskDao->getTaskVersion($task_id);
        $this->downloadTaskVersion($task_id, $latest_version);
    }

    public function archiveTask($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

        $task = $taskDao->getTask($task_id);
        $user_id = UserSession::getCurrentUserID();
        
        $taskType = TemplateHelper::getTaskTypeFromId($task->getTaskType());
        if($result = $taskDao->archiveTask($task_id, $user_id)) {
            $app->flash("success", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_1), $taskType, $task->getTitle()));
        } else {
            $app->flash("error",  sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_2), $taskType, $task->getTitle()));
        }    
             
        $app->redirect($ref = $app->request()->getReferrer());
    }

    public function downloadTask($task_id)
    {
        $app = Slim::getInstance();
        $convert = $app->request()->get("convertToXliff");
        $this->downloadTaskVersion($task_id, 0, $convert);
    }

    /*
     *  Claim and download a task
     */
    public function taskClaim($taskId)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $userDao = new UserDao();
        $languageDao = new LanguageDao();

        $task = $taskDao->getTask($taskId);
        if ($app->request()->isPost()) {
            $user_id = UserSession::getCurrentUserID();
            $userDao->claimTask($user_id, $task);
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
        
        $app->view()->appendData(array(
                    "task"          => $task,
                    "sourceLanguage"=> $sourceLanguage,
                    "targetLanguage"=> $targetLanguage,
                    "taskMetadata"  => $taskMetaData
        ));
       
        $app->render("task/task.claim.tpl");
    }

    public function taskClaimed($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

        $task = $taskDao->getTask($task_id);
        $app->view()->setData("task", $task);
        $app->render("task/task.claimed.tpl");
    }

    public function downloadTaskVersion($task_id, $version, $convert = 0)
    {
        $app = Slim::getInstance();
        $siteApi = Settings::get("site.api");
        $app->redirect("{$siteApi}v0/tasks/$task_id/file/?version=$version&convertToXliff=$convert");   
    }

    public function task($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();

        $user_id = UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);
        $taskClaimed = $taskDao->isTaskClaimed($task_id);

        if ($taskClaimed) {
            switch ($task->getTaskType()) {
                case TaskTypeEnum::DESEGMENTATION:
                    $app->redirect($app->urlFor("task-desegmentation", array("task_id" => $task_id)));
                    break;
                case TaskTypeEnum::TRANSLATION:
                case TaskTypeEnum::PROOFREADING:
                    $app->redirect($app->urlFor("task-simple-upload", array("task_id" => $task_id)));
                    break;
                case TaskTypeEnum::SEGMENTATION:
                    $app->redirect($app->urlFor("task-segmentation", array("task_id" => $task_id)));
                    break;
            }
        }else{
     
            $user_id = UserSession::getCurrentUserID();
            $task = $taskDao->getTask( $task_id);
            $project = $projectDao->getProject($task->getProjectId());
            $numTaskTypes = Settings::get("ui.task_types");

            $taskTypeColours = array();
            for($i=1; $i <= $numTaskTypes; $i++) {
                $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
            }
        
            $converter = Settings::get("converter.converter_enabled");
        
            $task_file_info = $taskDao->getTaskInfo($task_id);
            $siteApi = Settings::get("site.api");
            $file_path= "{$siteApi}v0/tasks/$task_id/file";

            $app->view()->appendData(array(
                        "taskTypeColours" => $taskTypeColours,
                        "project" => $project,
                        "converter"     => $converter,
                        "task" => $task,
                        "file_preview_path" => $file_path,
                        "filename" => $task_file_info->getFilename()
            ));

            $app->render("task/task.view.tpl");
        }
    }

    public function desegmentationTask($taskId)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();

        $userId = UserSession::getCurrentUserID();
        $fieldName = "mergedFile";
        $errorMessage = null;
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());

        if ($app->request()->isPost()) {
            $uploadError = false;
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
            } catch (Exception $e) {
                $uploadError = true;
                $errorMessage = $e->getMessage();
            }

            if (!$uploadError) {
                try {
                    $filedata = file_get_contents($_FILES[$fieldName]['tmp_name']);
                    $taskDao->saveTaskFile($taskId, $userId, $filedata);
                } catch (Exception  $e) {
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

        $graphBuilder = new UIWorkflowBuilder();
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

        $numTaskTypes = Settings::get("ui.task_types");

        $taskTypeColours = array();
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        $converter = Settings::get("converter.converter_enabled");
        
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
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        
        $fieldName = "fileUpload";
        $errorMessage = null;
        $userId = UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);
        $project = $projectDao->getProject($task->getProjectId());
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
                $projectFile = $projectDao->getProjectFileInfo($project->getId());
                $projectFileMimeType = $projectFile->getMime();
                $projectFileType = pathinfo($projectFile->getFilename(), PATHINFO_EXTENSION);
                
                $fileUploadType = pathinfo($_FILES[$fieldName]["name"], PATHINFO_EXTENSION);
                $fileUploadMime = IO::detectMimeType(file_get_contents($_FILES[$fieldName]["tmp_name"]), $_FILES[$fieldName]["name"]);

                if(strcasecmp($fileUploadType,$projectFileType) != 0) {
                    throw new Exception(sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_3), $projectFileType));
                } else if($fileUploadMime != $projectFileMimeType) {
                    throw new Exception(sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_4), $projectFileType, $projectFileType));
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        
            if (is_null($errorMessage)) {
                try {
                    $filedata = file_get_contents($_FILES[$fieldName]["tmp_name"]);
                    
                    if ($post['submit'] == 'XLIFF') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata, true);
                    } else if ($post['submit'] == 'submit') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata);
                    }
                
                } catch (Exception  $e) {
                    $errorMessage = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_5) . $e->getMessage();
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
        $numTaskTypes = Settings::get("ui.task_types");

        $taskTypeColours = array();
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        $converter = Settings::get("converter.converter_enabled");

        $app->view()->appendData(array(
                    "task"          => $task,
                    "project"       => $project,
                    "org"           => $org,
                    "filename"      => $filename,
                    "converter"     => $converter,
                    "fieldName"     => $fieldName,
                    "max_file_size" => TemplateHelper::maxFileSizeMB(),
                    "taskTypeColours"   => $taskTypeColours,
                    "file_previously_uploaded" => $file_previously_uploaded
        ));

        $app->render("task/task-simple-upload.tpl");
    }

    public function taskUploaded($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        $tipDao = new TipDao();

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
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $currentTask = $taskDao->getTask($task_id);
        $currentTaskStatus = $currentTask->getTaskStatus();

        $word_count_err = null;
        $deadlockError = null;
        $deadlineError = "";

        $extra_scripts = "
        <script type=\"text/javascript\">".file_get_contents(__DIR__."/../js/lib/jquery-ui-timepicker-addon.js")."</script>"
        .file_get_contents(__DIR__."/../js/datetime-picker.js");

        $task = $taskDao->getTask($task_id);

        $preReqTasks = $taskDao->getTaskPreReqs($task_id); 
        if (!$preReqTasks) {
            $preReqTasks = array();
        }

        $project = $projectDao->getProject($task->getProjectId());
        $projectTasks = $projectDao->getProjectTasks($task->getProjectId());
        foreach ($projectTasks as $projectTask) {
            if ($projectTask->getTaskStatus() == TaskStatusEnum::IN_PROGRESS ||
                        $projectTask->getTaskStatus() == TaskStatusEnum::COMPLETE) {
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
        
        if (isValidPost($app)) {
            $post = $app->request()->post();
           
            if ($task->getTaskStatus() < TaskStatusEnum::IN_PROGRESS) {
                if (isset($post['title']) && $post['title'] != "") {
                    $task->setTitle($post['title']);
                }

                if(isset($post['publishTask']) && $post['publishTask']) {
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);
                }
            
                $targetLocale = new Locale();
            
                if (isset($post['target']) && $post['target'] != "") {
                    $targetLocale->setLanguageCode($post['target']);
                }   
             
                if (isset($post['targetCountry']) && $post['targetCountry'] != "") {
                    $targetLocale->setCountryCode($post['targetCountry']);
                }   
            
                $task->setTargetLocale($targetLocale);
              
                if (isset($post['word_count']) && ctype_digit($post['word_count'])) {
                    $task->setWordCount($post['word_count']);                
                } else if (isset($post['word_count']) && $post['word_count'] != "") {
                    $word_count_err = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_6);
                } else {
                    $word_count_err = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_7);
                }
            }

            if (isset($post['deadline']) && $post['deadline'] != "") {
                if ($validTime = TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);  
                    $task->setDeadline($date);
                } else {
                    $deadlineError = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_8);
                }
            }
            
            if (isset($post['impact']) && $post['impact'] != "") {
                $task->setComment($post['impact']);
            }

            if ($word_count_err == "" && $deadlineError == "") {
                $selectedPreReqs = array();
                if(isset($post['totalTaskPreReqs']) && $post['totalTaskPreReqs'] > 0) {
                    for($i=0; $i < $post['totalTaskPreReqs']; $i++) {                        
                        if(isset($post["preReq_$i"])) $selectedPreReqs[] = $post["preReq_$i"];
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
                $graphBuilder = new UIWorkflowBuilder();
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
                            if(!in_array($preReqTask->getId(), $selectedList)) {
                                $taskDao->removeTaskPreReq($task->getId(), $preReqTask->getId());
                            }
                        }
                    }

                    foreach($selectedList as $taskId) {
                        if (is_numeric($taskId)) {
                            $taskDao->addTaskPreReq($task->getId(), $taskId);
                        }
                    }   

                    $app->redirect($app->urlFor("task-view", array("task_id" => $task_id)));
                } else {
                    //A deadlock occured
                    $deadlockError = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_9);
                    //Reset prereqs so as not to crash second run of the graph builder
                    $taskPreReqIds[$task->getId()] = $oldPreReqs;
                }
            }
        }
        
        $graphBuilder = new UIWorkflowBuilder();
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

        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        } 
        
        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
       
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
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $user_id = UserSession::getCurrentUserID();
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
        $siteApi = Settings::get("site.api");
        $file_path= "{$siteApi}v0/tasks/$task_id/file";
       
        $app->view()->appendData(array(
            "file_preview_path" => $file_path,
            "filename" => $task_file_info->getFilename()
        ));      
        
         
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if(isset($post['published'])) {
                if($post['published']) {                     
                    $task->setPublished(1);                    
                } else {
                    $task->setPublished(0);                    
                }
                $taskDao->updateTask($task);                 
                
            }

            if (isset($post['track'])) {
                if ($post['track'] == "Ignore") {
                    $response = $userDao->untrackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_10));
                    } else {
                        $app->flashNow("error", Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_11));
                    }
                } else {
                    $response = $userDao->trackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_12));
                    } else {
                        $app->flashNow("error", Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_13));
                    }
                }
            }
        } 
        
        $taskMetaData = array();
        $metaData = array();
        $registered = $userDao->isSubscribedToTask($user_id, $task_id);
        if($registered == 1) {
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
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }
        
        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        if($isOrgMember) {     
            $app->view()->appendData(array("isOrgMember" => $isOrgMember));
        }

        $app->view()->appendData(array(
                "org" => $org,
                "project" => $project,
                "registered" => $registered,
                "taskTypeColours" => $taskTypeColours
        ));

        $app->render("task/task.view.tpl");
    }

    public function taskCreate($project_id)
    {
        $app = Slim::getInstance();
        $projectDao = new ProjectDao();
        $taskDao = new TaskDao();
        $user_id = UserSession::getCurrentUserID();

        $titleError = null;
        $wordCountError = null;
        $deadlineError = null;
        $taskPreReqs = array();
        $task = new Task();
        $project = $projectDao->getProject($project_id);
        $projectTasks = $projectDao->getProjectTasks($project_id);
        $task->setProjectId($project_id);

        if($post = $app->request()->post()) { 
                    
            if(isset($post['title'])) {
                $task->setTitle($post['title']);
            } else {
                $titleError = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_14);
            }

            if(isset($post['comment'])) $task->setComment($post['comment']);            
            
            $projectSourceLocale = $project->getSourceLocale();
            $taskSourceLocale = new Locale();
            $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
            $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());            
            $task->setSourceLocale($taskSourceLocale);
            $task->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
            
            $taskTargetLocale = new Locale();            
            if(isset($post['targetLanguage'])) $taskTargetLocale->setLanguageCode($post['targetLanguage']);
            if(isset($post['targetCountry'])) $taskTargetLocale->setCountryCode($post['targetCountry']);
            $task->setTargetLocale($taskTargetLocale);
            
            if(isset($post['taskType'])) $task->setTaskType($post['taskType']);            

            if(ctype_digit($post['word_count'])) {
                $task->setWordCount($post['word_count']);
            } else if($post['word_count'] != "") {
                $wordCountError = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_6);
            } else {
                $wordCountError = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_7);
            }

            if(isset($post['deadline'])) {
                if($validTime = TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);  
                    $task->setDeadline($date);
                } else {
                    $deadlineError = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_8);
                }
            }

            if(isset($post['published'])) {
                $task->setPublished(1);
            } else {
                $task->setPublished(0);
            }

            if(is_null($titleError) && is_null($wordCountError) && is_null($deadlineError)) {
                $newTask = $taskDao->createTask($task);
                $newTaskId = $newTask->getId();
                
                $upload_error = null;                
                try {
                    $upload_error = $taskDao->saveTaskFile($newTaskId, $user_id, $projectDao->getProjectFile($project_id));
                } catch (Exception  $e) {
                    $upload_error = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_5) . $e->getMessage();
                }
                
                if(isset($post['totalTaskPreReqs']) && $post['totalTaskPreReqs'] > 0) {
                    for($i=0; $i < $post['totalTaskPreReqs']; $i++) {
                        if(isset($post["preReq_$i"])) $taskDao->addTaskPreReq($newTaskId, $post["preReq_$i"]);
                    }
                }
                
                if(is_null($upload_error)) {
                    $app->redirect($app->urlFor("task-created", array("task_id" => $newTaskId)));
                } else  {
                    $taskDao->deleteTask($newTaskId);
                    $app->view()->appendData(array("upload_error" => $upload_error));
                }
            }
        }


        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();

        $taskTypes = array();
        $taskTypes[TaskTypeEnum::SEGMENTATION] = "Segmentation";
        $taskTypes[TaskTypeEnum::TRANSLATION] = "Translation";
        $taskTypes[TaskTypeEnum::PROOFREADING] = "Proofreading";
        $taskTypes[TaskTypeEnum::DESEGMENTATION] = "Desegmentation";
        
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        $extra_scripts = "
            <script type=\"text/javascript\">".file_get_contents(__DIR__."/../js/lib/jquery-ui-timepicker-addon.js")."</script>"
            .file_get_contents(__DIR__."/../js/datetime-picker.js");

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
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $task = $taskDao->getTask($taskId);
        $app->view()->appendData(array(
                "project_id" => $task->getProjectId(),
                "task_id"    => $task->getId()
        ));

        $app->render("task/task.created.tpl");
    }
    
    public function taskSegmentation($task_id)
    {  
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();

        $user_id = UserSession::getCurrentUserID();
        $taskTypeErr = null;        
        
        $task = $taskDao->getTask($task_id); 
        $project = $projectDao->getProject($task->getProjectId());
        $numTaskTypes = Settings::get("ui.task_types");
        $maxSegments = Settings::get("site.max_segmentation");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }
    
        $language_list = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
        
        if ($app->request()->isPost()) {
            $post = $app->request()->post(); 
            
            $fileInfo = $projectDao->getProjectFileInfo($project->getId());            
            $canonicalExtension = explode(".", $fileInfo->getFileName());
            $canonicalExtension = strtolower($canonicalExtension[count($canonicalExtension)-1]);    
            
            $errors = array();             
            $fileNames = array();
            $fileHashes = array();
            foreach($_FILES as $file) {
                $extension = explode(".", $file["name"]);
                $extension = strtolower($extension[count($extension)-1]);                
                if($extension != $canonicalExtension) {
                    $errors["incorrectExtension"] = "The extension (<strong>.$extension</strong>) of one of your files does not match the original project file extension (<strong>.$canonicalExtension</strong>).";
                    break;
                }
                
                if($file["error"] != UPLOAD_ERR_OK) {
                    $errors["missingFile"] = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_15);
                    break;
                }
                if(!in_array($file["name"],$fileNames)) {
                    $fileNames[] = $file["name"];
                } else {
                    $errors["uniqueFileName"] = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_16);
                    break;
                }
                    
                if(!in_array(($hash=md5_file($file["tmp_name"])), $fileHashes)) {
                    $fileHashes[] = $hash;
                } else {
                    $errors["duplicateFileContent"] = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_17);
                    break;
                }
            }          
            
            if(!isset($post["translation_0"]) && !isset($post["proofreading_0"])) {
                $errors["taskTypeSet"] = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_18);
            }
            
            if(empty($errors)) {
                $segmentationValue = $post["segmentationValue"];
                $upload_error = false;      
                $translationTaskIds = array();
                $proofreadTaskIds = array();
                for($i=0; $i < $segmentationValue; $i++) {                    
                    try {
                        TemplateHelper::validateFileHasBeenSuccessfullyUploaded("segmentationUpload_".$i);
                        $taskModel = new Task();
                        $this->setTaskModelData($taskModel, $project, $task, $i, $segmentationValue);
                        if(isset($post["translation_0"])) {
                            $taskModel->setTaskType(TaskTypeEnum::TRANSLATION);
                            $taskModel->setWordCount($post["wordCount_$i"]);
                            $createdTranslation = $taskDao->createTask($taskModel);
                            $translationTaskIds[] = $createdTranslation->getId();
                            try {                    
                                $filedata = file_get_contents($_FILES['segmentationUpload_'.$i]['tmp_name']);                    
                                $taskDao->saveTaskFile($createdTranslation->getId(), $user_id, $filedata);                                
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $errors["transTask$i"] = "<strong>File #$i:</strong> {$e->getMessage()}";                               
                            }                             
                            
                        }

                        if(isset($post["proofreading_0"])) {
                            $taskModel->setTaskType(TaskTypeEnum::PROOFREADING);                         
                            $taskModel->setWordCount($post["wordCount_$i"]);
                            $createdProofReading = $taskDao->createTask($taskModel);
                            $proofreadTaskIds[] = $createdProofReading->getId();
                            try {                    
                                $filedata = file_get_contents($_FILES['segmentationUpload_'.$i]['tmp_name']);
                                $taskDao->saveTaskFile($createdProofReading->getId(), $user_id, $filedata);                                
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $errors["proofTask$i"] = "<strong>File #$i:</strong> {$e->getMessage()}";
                                $taskDao->deleteTask($createdProofReading->getId());
                            }   
                                             
                        }
                    } catch (Exception $e) {
                        $upload_error = true;
                        $error_message = $e->getMessage();
                    }
                }  
                
                if(!$upload_error) {

                    $taskModel = new Task();
                    $this->setTaskModelData($taskModel, $project, $task);
                    $taskModel->setWordCount($task->getWordCount());
                    $taskModel->setTaskType(TaskTypeEnum::DESEGMENTATION);                         
                    $createdDesegmentation = $taskDao->createTask($taskModel);
                    $createdDesegmentationId = $createdDesegmentation->getId();

                    try {                    
                        $filedata = file_get_contents($_FILES["segmentationUpload_0"]["tmp_name"]);                    
                        $error_message = $taskDao->saveTaskFile($createdDesegmentation->getId(), $user_id, $filedata);
                    } catch (SolasMatchException  $e) {
                        $upload_error = true;
                        $error_message = "File error: " . $e->getMessage();
                    }  

                    $task->setTaskStatus(TaskStatusEnum::COMPLETE);
                    $taskDao->updateTask($task);
                    for($i=0; $i < $segmentationValue; $i++) {
                        if(isset($post["translation_0"]) && isset($post["proofreading_0"])) {   
                            $taskDao->addTaskPreReq($translationTaskIds[$i], $task_id);
                            $taskDao->addTaskPreReq($proofreadTaskIds[$i], $translationTaskIds[$i]);
                            $taskDao->addTaskPreReq($createdDesegmentationId, $proofreadTaskIds[$i]);
                        }
                        if(!isset($post["translation_0"]) && isset($post["proofreading_0"])) {
                            $taskDao->addTaskPreReq($proofreadTaskIds[$i], $task_id);
                            $taskDao->addTaskPreReq($createdDesegmentationId, $proofreadTaskIds[$i]);
                        }
                        if(isset($post["translation_0"]) && !isset($post["proofreading_0"])) {   
                            $taskDao->addTaskPreReq($translationTaskIds[$i], $task_id);
                            $taskDao->addTaskPreReq($createdDesegmentationId, $translationTaskIds[$i]);
                        }
                    }
                    $app->redirect($app->urlFor("task-review", array("task_id" => $task->getId())));
                } else {                    
                    if(!empty($translationTaskIds)) foreach($translationTaskIds as $taskId) $taskDao->deleteTask($taskId);
                    if(!empty($proofreadTaskIds)) foreach($proofreadTaskIds as $taskId) $taskDao->deleteTask($taskId);                    
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
        
        $extraScripts = file_get_contents("http://".$_SERVER["HTTP_HOST"]."{$app->urlFor("home")}ui/js/task-segmentation.js");
        
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
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();

        $user_id = UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);        
        $taskClaimedDate = $taskDao->getClaimedDate($task_id); 
        $project = $projectDao->getProject($task->getProjectId());
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if(isset($post['feedback'])) {

                if ($post['feedback'] != "") {
                    $taskDao->sendOrgFeedback($task_id, $user_id, $claimant->getId(), $post['feedback']);
    
                    $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_32), $app->urlFor("user-public-profile", array("user_id" => $claimant->getId())), $claimant->getDisplayName()));
                    if(isset($post['revokeTask']) && $post['revokeTask']) {
                        $task->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                        $taskDao->updateTask($task);
                        $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id);
                        if($taskRevoke) {
                            $app->flash("taskSuccess", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_19), $app->urlFor("task-view", array("task_id" => $task_id)), $task->getTitle(), $app->urlFor("user-public-profile", array("user_id" => $claimant->getId())), $claimant->getDisplayName()));
                            $app->redirect($app->urlFor("project-view", array("project_id" => $task->getProjectId())));
                        } else {
                            $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_20), $app->urlFor("task-view", array("task_id" => $task_id)), $task->getTitle(), $app->urlFor("user-public-profile", array("user_id" => $claimant->getId())), $claimant->getDisplayName()));
                        }
                    }
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_21));
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
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $user_id = UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);  
        $taskClaimedDate = $taskDao->getClaimedDate($task_id);
        $project = $projectDao->getProject($task->getProjectId());
        $organisation = $orgDao->getOrganisation($project->getOrganisationId());          
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if(isset($post['feedback'])) {
                if ($post['feedback'] != '') {
                    $taskDao->sendUserFeedback($task_id, $claimant->getId(), $post['feedback']);
                    if(isset($post['revokeTask']) && $post['revokeTask']) {
                        $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id);
                        if($taskRevoke) {
                            $app->flash("success", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_22), $app->urlFor("task-view", array("task_id" => $task_id)), $task->getTitle()));
                            $app->redirect($app->urlFor("home"));
                        } else {
                            $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_22), $app->urlFor("task-view", array("task_id" => $task_id)), $task->getTitle()));
                        }
                    }
                } else {
                    $app->flashNow('error', Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_24));
                }
            }
        }
        
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
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
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $userDao = new UserDao();
        $userId = UserSession::getCurrentUserID();

        $task = $taskDao->getTask($taskId);
        $action = "";
        switch ($task->getTaskType()) {
            case TaskTypeEnum::SEGMENTATION:
                $action = "segmented";
                break;
            case TaskTypeEnum::TRANSLATION:
                $action = "translated";
                break;
            case TaskTypeEnum::PROOFREADING:
                $action = "proofread";
                break;
            case TaskTypeEnum::DESEGMENTATION:
                $action = "merged";
                break;
        }

        $reviews = array();
        $preReqTasks = $taskDao->getTaskPreReqs($taskId);
        if ($preReqTasks == null || count($preReqTasks) == 0) {
            $projectDao = new ProjectDao();
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

            $dummyTask = new Task();        //Create a dummy task to hold the project info
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
            $app->flashNow("info", Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_25));
        }

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['submitReview'])) {
                $i = 0;
                $error = null;
                while ($i < count($preReqTasks) && $error == null) {
                    $pTask = $preReqTasks[$i++];
                    $review = new TaskReview();
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
                            $error = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_26);
                        }
                    }
                    if (isset($post["grammar_$id"]) && ctype_digit($post["grammar_$id"])) {
                        $value = intval($post["grammar_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setGrammar($value);
                        } else {
                            $error = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_27);
                        }
                    }
                    if (isset($post["spelling_$id"]) && ctype_digit($post["spelling_$id"])) {
                        $value = intval($post["spelling_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setSpelling($value);
                        } else {
                            $error = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_28);
                        }
                    }
                    if (isset($post["consistency_$id"]) && ctype_digit($post["consistency_$id"])) {
                        $value = intval($post["consistency_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setConsistency($value);
                        } else {
                            $error = Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_29);
                        }
                    }
                    if (isset($post["comment_$id"]) && $post["comment_$id"] != "") {
                        $review->setComment($post["comment_$id"]);
                    }

                    if ($review->getProjectId() != null && $review->getUserId() != null && $error == null) {
                        if (!$taskDao->submitReview($review)) {
                            $error = sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_30), $pTask->getTitle());
                        }
                    } else {
                        if ($error != null) {
                            $app->flashNow("error", $error);
                        }
                    }
                }
                if ($error == null) {
                    $app->flash("success", sprintf(Localisation::getTranslation(Strings::TASK_ROUTEHANDLER_31), $pTask->getTitle()));
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
    
    private function setTaskModelData($taskModel, $project, $task, $i=null, $segmentationValue=null) {
        
        if(is_null($i) && is_null($segmentationValue)) {
            $taskModel->setTitle($project->getTitle());
        } else {
            $taskModel->setTitle($project->getTitle()." (".($i+1)." of $segmentationValue)");
        }
        
        $taskModel->setSourceLocale($project->getSourceLocale());
        $taskModel->setTargetLocale($task->getTargetLocale());
        
        $taskModel->setProjectId($project->getId());
        $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
    }
}

$route_handler = new TaskRouteHandler();
$route_handler->init();
unset ($route_handler);
