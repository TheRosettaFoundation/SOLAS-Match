<?php

class TaskRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get("/tasks/archive/p/:page_no", array($this, "archivedTasks")
        )->name("archived-tasks");

        $app->get("/tasks/claimed/p/:page_no", array($this, "claimedTasks")
        )->name("claimed-tasks");        

        $app->get("/task/:task_id/download-task-latest-file", array($middleware, "authenticateUserForTask"),
        array($this, "downloadTaskLatestVersion"))->name("download-task-latest-version");
        
        $app->get("/task/:task_id/mark-archived", array($middleware, "authUserForOrgTask"),
        array($this, "archiveTask"))->name("archive-task");

        $app->get("/task/:task_id/download-file-user", array($middleware, "authUserIsLoggedIn"),
        array($this, "downloadTask"))->name("download-task");

        $app->get("/task/:task_id/claim", array($middleware, "authUserIsLoggedIn"),
        array($this, "taskClaim"))->via("POST")->name("task-claim-page");

        $app->get("/task/:task_id/claimed", array($middleware, "authenticateUserForTask"),
        array($this, "taskClaimed"))->name("task-claimed");

        $app->get("/task/:task_id/download-file/v/:version", array($middleware, "authUserIsLoggedIn"), 
        array($middleware, "authUserForTaskDownload"), 
        array($this, "downloadTaskVersion"))->name("download-task-version");

        $app->get("/task/:task_id/id", array($middleware, "authUserIsLoggedIn"),
        array($this, "task"))->via("POST")->name("task");

        $app->get("/task/:task_id/desegmentation", array($middleware, "authUserIsLoggedIn"),
        array($this, "desegmentationTask"))->via("POST")->name("task-desegmentation");

        $app->get("/task/:task_id/simple-upload", array($middleware, "authUserIsLoggedIn"),
        array($this, "taskSimpleUpload"))->via("POST")->name("task-simple-upload");

        $app->get("/task/:task_id/segmentation", array($middleware, "authUserIsLoggedIn"),
        array($this, "taskSegmentation"))->via("POST")->name("task-segmentation");

        $app->get("/task/:task_id/uploaded", array($middleware, "authenticateUserForTask"),
        array($this, "taskUploaded"))->name("task-uploaded");

        $app->get("/task/:task_id/alter", array($middleware, "authUserForOrgTask"), 
        array($this, "taskAlter"))->via("POST")->name("task-alter");

        $app->get("/task/:task_id/view", array($middleware, "authUserIsLoggedIn"),
        array($this, "taskView"))->via("POST")->name("task-view");

        $app->get("/project/:project_id/create-task", array($middleware, "authUserForOrgProject"), 
        array($this, "taskCreate"))->via("GET", "POST")->name("task-create");

        $app->get("/task/:task_id/created", array($middleware, "authenticateUserForTask"),
        array($this, "taskCreated"))->name("task-created");
        
        $app->get("/task/:task_id/org-feedback/", array($middleware, "authUserForOrgTask"), 
        array($this, "taskOrgFeedback"))->via("POST")->name("task-org-feedback");
        
        $app->get("/task/:task_id/user-feedback/", array($middleware, "authenticateUserForTask"), 
        array($this, "taskUserFeedback"))->via("POST")->name("task-user-feedback");   

        $app->get("/task/:task_id/review", array($middleware, "authenticateUserForTask"),
        array($this, "taskReview"))->via("POST")->name("task-review");
        
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
        $app->render("archived-tasks.tpl");
    }

    public function claimedTasks($page_no)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();

        $user_id = UserSession::getCurrentUserID();
        if (is_null($user_id)) {
            $app->flash("error", "Login required to access page.");
            $app->redirect($app->urlFor("login"));
        }

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
        
        $app->render("claimed-tasks.tpl");
    }

    public function downloadTaskLatestVersion($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

        $task = $taskDao->getTask($task_id);
        if (!is_object($task)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }

        $user_id = UserSession::getCurrentUserID();
        if (is_null($user_id)) {
            $app->flash("error", "Login required to access page");
            $app->redirect($app->urlFor("login"));
        }   
        
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
            $app->flash("success", "You have successfully archived the <b>$taskType Task {$task->getTitle()}</b>.");
        } else {
            $app->flash("error",  "There was an error archiving the <b>$taskType Task {$task->getTitle()}</b>.");
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
       
        $app->render("task.claim.tpl");
    }

    public function taskClaimed($task_id)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

        $task = $taskDao->getTask($task_id);
        $app->view()->setData("task", $task);
        $app->render("task.claimed.tpl");
    }

    public function downloadTaskVersion($task_id, $version, $convert = false)
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

            $app->render("task.view.tpl");
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
            $post = (object) $app->request()->post();

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
                    $errorMessage = $taskDao->saveTaskFile($taskId, $_FILES[$fieldName]['name'],
                            $userId, $filedata);
                } catch (Exception  $e) {
                    $uploadError = true;
                    $errorMessage = "File error: " . $e->getMessage();
                }
            }

            if (is_null($errorMessage)) {
                $app->redirect($app->urlFor("task-uploaded", array("task_id" => $taskId)));
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

        $app->render("task-desegmentation.tpl");
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
            $post = (object) $app->request()->post();///never again cast an array to an object.
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
                $projectFile = $projectDao->getProjectFileInfo($project->getId());
                $projectFileType = pathinfo($projectFile->getFilename(), PATHINFO_EXTENSION);
                $fileUploadType = pathinfo($_FILES[$fieldName]["name"], PATHINFO_EXTENSION);
                if($fileUploadType != $projectFileType) {
                    throw new Exception("The file extension differs from the originally downloaded file. Please upload as .$projectFileType!");
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        
            if (is_null($errorMessage)) {
                try {
                    $filedata = file_get_contents($_FILES["fileUpload"]["tmp_name"]);
                    
                    if ($post->submit == 'XLIFF') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata, true);
                    } else if ($post->submit == 'submit') {
                        $taskDao->uploadOutputFile($taskId, $userId, $filedata);
                    }
                
                } catch (Exception  $e) {
                    $errorMessage = "File error: " . $e->getMessage();
                }
            }

            if (is_null($errorMessage)) {
                $app->redirect($app->urlFor("task-uploaded", array("task_id" => $taskId)));
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

        $app->render("task-simple-upload.tpl");
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
        
        $app->render("task.uploaded.tpl");
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

        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/selectable.css\" />
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/jquery-ui-timepicker-addon.css\" />
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/jquery-ui-timepicker-addon.js\"></script>
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/datetime-picker.js\"></script>"
        .file_get_contents("http://".$_SERVER["HTTP_HOST"]."{$app->urlFor("home")}ui/js/task-alter.js");

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
            $post = (object) $app->request()->post();
            
            if ($post->title != "") {
                $task->setTitle($post->title);
            }
            
            if ($post->impact != "") {
                $task->setComment($post->impact);
            }

            if ($post->deadline != "") {
                if (TemplateHelper::isValidDateTime($post->deadline) == true) {
                    $unixTime = strtotime($post->deadline);
                    $date = date("Y-m-d H:i:s", $unixTime);  
                    $task->setDeadline($date);
                } else {
                    $deadlineError = "Invalid date/time format!";
                }
            }
            
            if(isset($post->publishTask) && $post->publishTask) {
                $task->setPublished(1);
            } else {
                $task->setPublished(0);
            }
            
            $targetLocale = new Locale();
            
            if ($post->target != "") {
                $targetLocale->setLanguageCode($post->target);
            }   
             
            if ($post->targetCountry != "") {
                $targetLocale->setCountryCode($post->targetCountry);
            }   
            
            $task->setTargetLocale($targetLocale);
              
            if (ctype_digit($post->word_count)) {
                $task->setWordCount($post->word_count);                
            } else if ($post->word_count != "") {
                $word_count_err = "Word Count must be numeric";
            } else {
                $word_count_err = "Word Count cannot be blank";
            }

            if ($word_count_err == "" && $deadlineError == "") {
                $selectedPreReqs = array();
                if(isset($post->totalTaskPreReqs) && $post->totalTaskPreReqs > 0) {
                    for($i=0; $i < $post->totalTaskPreReqs; $i++) {                        
                        if(isset($post->{"preReq_".$i})) $selectedPreReqs[] = $post->{"preReq_".$i};
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

                    $selectedList = array();
                    $nextLayer = array();
                    $currentLayer = $graph->getRootNodeList();
                    $found = false;
                    while (!$found && count($currentLayer) > 0) {
                        foreach ($currentLayer as $node) {
                            if ($node->getTaskId() == $task->getId()) {
                                $found = true;
                                foreach ($node->getPreviousList() as $preReqNode) {
                                    $selectedList[] = $preReqNode->getTaskId();
                                }
                            }
                            foreach ($node->getNextList() as $nextNode) {
                                $nextLayer[] = $nextNode;
                            }
                        }
                        $currentLayer = $nextLayer;
                        $nextLayer = array();
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
                    $deadlockError = "A deadlock has occured, please check your task prerequisites.";
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
                foreach ($currentRow as $nodeIndex) {
                    $node = $graphBuilder->getAllNodes($nodeIndex);
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
        } else {
            echo "<p>Graph building failed</p>";
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
        
        $app->render("task.alter.tpl");
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
            $post = (object) $app->request()->post();
            
            if(isset($post->published)) {
                if($post->published) {                     
                    $task->setPublished(1);                    
                } else {
                    $task->setPublished(0);                    
                }
                $taskDao->updateTask($task);                 
                
            }

            if (isset($post->track)) {
                if ($post->track == "Ignore") {
                    $response = $userDao->untrackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", 
                                "You are now tracking this task and will receive email notifications
                                when its status changes.");
                    } else {
                        $app->flashNow("error", "Unable to register for notifications for this task.");
                    }
                } else {
                    $response = $userDao->trackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", 
                                "You are no longer tracking this task and will receive no
                                further emails.");
                    } else {
                        $app->flashNow("error", "Unable to unregister for this notification.");
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

        $app->render("task.view.tpl");
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
                $titleError = "Title must not be blank";
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
                $wordCountError = "Word Count must be numeric";
            } else {
                $wordCountError = "Word Count cannot be blank";
            }

            if(isset($post['deadline'])) {
                if(TemplateHelper::isValidDateTime($post['deadline']) == true) {
                    $unixTime = strtotime($post['deadline']);
                    $date = date("Y-m-d H:i:s", $unixTime);  
                    $task->setDeadline($date);
                } else {
                    $deadlineError = "Invalid date/time format!";
                }
            }

            if(isset($post['published'])) $task->setPublished("1");

            if(is_null($titleError) && is_null($wordCountError) && is_null($deadlineError)) {
                $newTask = $taskDao->createTask($task);
                $newTaskId = $newTask->getId();
                
                $upload_error = null;                
                try {
                    $upload_error = $taskDao->saveTaskFile($newTaskId, $projectDao->getProjectFileInfo($project_id)->getFilename(),
                            $user_id, $projectDao->getProjectFile($project_id));
                } catch (Exception  $e) {
                    $upload_error = "File error: " . $e->getMessage();
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
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/jquery-ui-timepicker-addon.css\" />
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/jquery-ui-timepicker-addon.js\"></script>
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/datetime-picker.js\"></script>";

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

        $app->render("task.create.tpl");
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

        $app->render("task.created.tpl");
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
            
            $errors = array(); 
            
            $fileNames = array();
            $fileHashes = array();
            foreach($_FILES as $file) {
                if($file["error"] != UPLOAD_ERR_OK) {
                    $errors["missingFile"] = "You have not selected a <b>Segmented File</b> to upload.";
                    break;
                }
                if(!in_array($file["name"],$fileNames)) {
                    $fileNames[] = $file["name"];
                } else {
                    $errors["uniqueFileName"] = "Each <b>Segmented File</b> that you upload must have a <b>unique file name.</b>";
                    break;
                }
                    
                if(!in_array(($hash=md5_file($file["tmp_name"])), $fileHashes)) {
                    $fileHashes[] = $hash;
                } else {
                    $errors["duplicateFileContent"] = "You have selected <b>one or more</b> files with the exact same <b>file content</b>.";
                    break;
                }
            }          
            
            if(!isset($post["translation_0"]) && !isset($post["proofreading_0"])) {
                $errors["taskTypeSet"] = "At least one task type such as <b>Translation</b> and/or <b>Proofreading</b> must be set.";
            }
            
            if(empty($errors)) {
                $segmentationValue = $post["segmentationValue"];
                $upload_error = false;      
                $translationTaskIds = array();
                $proofreadTaskIds = array();
                for($i=0; $i < $segmentationValue && !$upload_error; $i++) {                    
                    try {
                        TemplateHelper::validateFileHasBeenSuccessfullyUploaded("segmentationUpload_".$i);
                        $taskModel = new Task();
                        $this->setTaskModelData($taskModel, $project, $task, $i);
                        if(isset($post["translation_0"])) {
                            $taskModel->setTaskType(TaskTypeEnum::TRANSLATION);
                            $taskModel->setWordCount($post["wordCount_$i"]);
                            $createdTranslation = $taskDao->createTask($taskModel);
                            try {                    
                                $filedata = file_get_contents($_FILES['segmentationUpload_'.$i]['tmp_name']);                    
                                $error_message = $taskDao->saveTaskFile($createdTranslation->getId(),
                                        urlencode($_FILES['segmentationUpload_'.$i]['name']), $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: {$e->getMessage()}";
                            }                             
                            $translationTaskIds[] = $createdTranslation->getId();                            
                        }

                        if(isset($post["proofreading_0"])) {
                            $taskModel->setTaskType(TaskTypeEnum::PROOFREADING);                         
                            $taskModel->setWordCount($post["wordCount_$i"]);
                            $createdProofReading = $taskDao->createTask($taskModel);
                            try {                    
                                $filedata = file_get_contents($_FILES['segmentationUpload_'.$i]['tmp_name']);
                                $error_message = $taskDao->saveTaskFile($createdProofReading->getId(),
                                        urlencode($_FILES['segmentationUpload_'.$i]['name']), $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: {$e->getMessage()}";
                            }   
                            $proofreadTaskIds[] = $createdProofReading->getId();                           
                        }
                    } catch (Exception $e) {
                        $upload_error = true;
                        $file_upload_err = $e->getMessage();
                    }
                }            

                $taskModel = new Task();
                $this->setTaskModelData($taskModel, $project, $task, 0);                       
                $taskModel->setWordCount($task->getWordCount());
                $taskModel->setTaskType(TaskTypeEnum::DESEGMENTATION);                         
                $createdDesegmentation = $taskDao->createTask($taskModel);
                $createdDesegmentationId = $createdDesegmentation->getId();

                try {                    
                    $filedata = file_get_contents($_FILES["segmentationUpload_0"]["tmp_name"]);                    
                    $error_message = $taskDao->saveTaskFile($createdDesegmentation->getId(),
                                    urlencode($_FILES['segmentationUpload_0']['name']), $user_id, $filedata);
                } catch (Exception  $e) {
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
                $app->redirect($app->urlFor("project-view", array("project_id" => $task->getProjectId())));
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
        
        $app->render("task-segmentation.tpl");
    }
    
    public function taskOrgFeedback($task_id)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();

        $user_id = UserSession::getCurrentUserID();
        $task = $taskDao->getTask($task_id);   
        $project = $projectDao->getProject($task->getProjectId());
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            if(isset($post->feedback)) {

                if ($post->feedback != "") {
                    $taskDao->sendFeedback($task_id, array($claimant->getId()), $post->feedback);
    
                    $app->flashNow("success", "Feedback sent to 
                            <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $claimant->getId()))}\">
                            {$claimant->getDisplayName()}</a>.");
                    if(isset($post->revokeTask) && $post->revokeTask) {
                        $task->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                        $taskDao->updateTask($task);
                        $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id);
                        if($taskRevoke) {
                            $app->flash("taskSuccess", "<b>Success</b> - The task 
                                <a href=\"{$app->urlFor("task-view", array("task_id" => $task_id))}\">{$task->getTitle()}</a>
                                has been revoked from 
                                <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $claimant->getId()))}\">
                                {$claimant->getDisplayName()}</a>. This user will be notified by e-mail and provided with your feedback.");
                            $app->redirect($app->urlFor("project-view", array("project_id" => $task->getProjectId())));
                        } else {
                            $app->flashNow("error", "<b>Error</b> - Unable to revoke the task ".
                                "<a href=\"{$app->urlFor("task-view", array("task_id" => $task_id))}\">{$task->getTitle()}\"</a>
                                from <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $claimant->getId()))}\">
                                {$claimant->getDisplayName()}</a>. Please try again later.");
                        }
                    }
                } else {
                    $app->flashNow("error", "The feedback field cannot be empty.");
                }
            }
        }
        
        $app->view()->appendData(array(
            "project" => $project,
            "task" => $task,
            "claimant" => $claimant,
            "taskTypeColours" => $taskTypeColours,
            "task_tags" => $task_tags
        ));
        
        $app->render("task.org-feedback.tpl");
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
        $project = $projectDao->getProject($task->getProjectId());
        $organisation = $orgDao->getOrganisation($project->getOrganisationId());          
        $claimant = $taskDao->getUserClaimedTask($task_id);
        $task_tags = $taskDao->getTaskTags($task_id);

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();

            if(isset($post->feedback)) {
                $taskDao->sendFeedback($task_id, array($claimant->getId()), $post->feedback);
                if(isset($post->revokeTask) && $post->revokeTask) {
                    $taskRevoke = $userDao->unclaimTask($claimant->getId(), $task_id);
                    if($taskRevoke) {
                        $app->flash("success", " The task ".
                              "<a href=\"{$app->urlFor("task-view", array("task_id" => $task_id))}\">{$task->getTitle()}</a>".
                              "has been successfully unclaimed. The organisation will be notified by e-mail and provided with your feedback.");
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->flashNow("error", " Unable to unclaim the task ".
                              "<a href=\"{$app->urlFor("task-view", array("task_id" => $task_id))}\">{$task->getTitle()}</a>".
                              ". Please try again later.");
                    }
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
            "claimant" => $claimant,
            "taskTypeColours" => $taskTypeColours,
            "task_tags" => $task_tags
        ));
        
        $app->render("task.user-feedback.tpl");
    }

    public function taskReview($taskId)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();

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

        $preReqTasks = $taskDao->getTaskPreReqs($taskId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            $userId = UserSession::getCurrentUserID();

            if (isset($post['submitReview'])) {
                foreach ($preReqTasks as $pTask) {
                    $review = new TaskReview();
                    $id = $pTask->getId();

                    $review->setUserId($userId);
                    $review->setTaskId($id);
                    $review->setProjectId($pTask->getProjectId());

                    if (isset($post["corrections_$id"]) && ctype_digit($post["corrections_$id"])) {
                        $value = intval($post["corrections_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setCorrections($value);
                        }
                    }
                    if (isset($post["grammar_$id"]) && ctype_digit($post["grammar_$id"])) {
                        $value = intval($post["grammar_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setGrammar($value);
                        }
                    }
                    if (isset($post["spelling_$id"]) && ctype_digit($post["spelling_$id"])) {
                        $value = intval($post["spelling_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setSpelling($value);
                        }
                    }
                    if (isset($post["consistency_$id"]) && ctype_digit($post["consistency_$id"])) {
                        $value = intval($post["consistency_$id"]);
                        if ($value > 0 && $value <= 5) {
                            $review->setConsistency($value);
                        }
                    }
                    if (isset($post["comment_$id"]) && $post["comment_$id"] != "") {
                        $review->setComment($post["comment_$id"]);
                    }

                    if ($review->getTaskId() != null && $review->getUserId() != null) {
                        if ($taskDao->submitReview($review)) {
                            $app->flash("success", 
                                    "Review of task {$pTask->getTitle()} has been submitted successfully");
                            $app->redirect($app->urlFor('task-uploaded', array("task_id" => $taskId)));
                        } else {
                            $app->flashNow("error", 
                                    "Unable to submit review for {$pTask->getTitle()}, please try again later");
                        }
                    }
                }
            }
        }

        $app->view()->appendData(array(
                    'taskId'    => $taskId,
                    'tasks'     => $preReqTasks,
                    'action'    => $action
        ));

        $app->render("task.review.tpl");
    }
    
    private function setTaskModelData($taskModel, $project, $task, $i) {
        $taskModel->setTitle($_FILES["segmentationUpload_$i"]["name"]);
        
        $taskModel->setSourceLocale($project->getSourceLocale());
        $taskModel->setTargetLocale($task->getTargetLocale());
        
        $taskModel->setProjectId($project->getId());
        $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
    }
}

$route_handler = new TaskRouteHandler();
$route_handler->init();
unset ($route_handler);
