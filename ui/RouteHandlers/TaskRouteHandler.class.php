<?php

class TaskRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get('/tasks/archive/p/:page_no', array($this, 'archivedTasks')
        )->name('archived-tasks');

        $app->get('/tasks/active/p/:page_no', array($this, 'activeTasks')
        )->name('active-tasks');        

        $app->get('/task/id/:task_id/download-task-latest-file/', array($middleware, 'authenticateUserForTask'),
        array($this, 'downloadTaskLatestVersion'))->name('download-task-latest-version');
        
        $app->get('/task/id/:task_id/mark-archived/', array($middleware, 'authUserForOrgTask'),
        array($this, 'archiveTask'))->name('archive-task');

        $app->get('/task/id/:task_id/download-file-user/', array($middleware, 'authUserIsLoggedIn'),
        array($this, 'downloadTask'))->name('download-task');

        $app->get('/task/claim/:task_id', array($middleware, 'authUserIsLoggedIn'),
        array($this, 'taskClaim'))->via("POST")->name('task-claim-page');

        $app->get('/task/id/:task_id/claimed', array($middleware, 'authenticateUserForTask'),
        array($this, 'taskClaimed'))->name('task-claimed');

        $app->get('/task/id/:task_id/download-file/v/:version/', array($middleware, 'authUserIsLoggedIn'), 
        array($middleware, 'authUserForTaskDownload'), 
        array($this, 'downloadTaskVersion'))->name('download-task-version');

        $app->get('/task/id/:task_id/', array($middleware, 'authUserIsLoggedIn'),
        array($this, 'task'))->via("POST")->name('task');

        $app->get('/task/id/:task_id/uploaded/', array($middleware, 'authenticateUserForTask'),
        array($this, 'taskUploaded'))->name('task-uploaded');

        $app->get('/task/alter/:task_id/', array($middleware, 'authUserForOrgTask'), 
        array($this, 'taskAlter'))->via('POST')->name('task-alter');

        $app->get('/task/view/:task_id/', array($middleware, 'authUserIsLoggedIn'),
        array($this, 'taskView'))->via("POST")->name('task-view');

        $app->get('/task/create/:project_id/', array($middleware, 'authUserForOrgProject'), 
        array($this, 'taskCreate'))->via('GET', 'POST')->name('task-create');

        $app->get("/task/:task_id/created/", array($middleware, 'authenticateUserForTask'),
        array($this, "taskCreated"))->name("task-created");
        
        $app->get('/task/:task_id/feedback/', array($middleware, 'authUserForOrgTask'), 
        array($this, 'taskFeedback'))->via('POST')->name('task-feedback');
        
        $app->get(Settings::get("site.api"), array($middleware, 'authUserForOrgTask'))->name('api');
    }

    public function archivedTasks($page_no)
    {
        $app = Slim::getInstance();
        $client = new APIClient();      
        $user_id = UserSession::getCurrentUserID();
        
        $request = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($request, HTTP_Request2::METHOD_GET);
        $user = $client->cast('User', $response);

        if (!is_object($user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        } 
        
        $archived_tasks = array();
        $request = APIClient::API_VERSION."/users/$user_id/archived_tasks";
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, array('limit' => 10 )); 
        
        if ($response) {
            foreach ($response as $stdObject) {
                $archived_tasks[] = $client->cast('ArchivedTask', $stdObject);
            }
        }        

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
        
        $app->view()->setData('archived_tasks', $archived_tasks);
        $app->view()->appendData(array(
                                    'page_no' => $page_no,
                                    'last' => $total_pages,
                                    'top' => $top,
                                    'bottom' => $bottom
        ));
        $app->render('archived-tasks.tpl');
    }

    public function activeTasks($page_no)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $user_id = UserSession::getCurrentUserID();
        if (is_null($user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }

        $activeTasks = array();
        $request = APIClient::API_VERSION."/users/$user_id/tasks";
        $response = $client->call($request);
        
        if ($response) {
            foreach ($response as $stdObject) {
                $activeTasks[] = $client->cast('Task', $stdObject);
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
        
        $app->view()->setData('active_tasks', $activeTasks);
        $app->view()->appendData(array(
                        'page_no' => $page_no,
                        'last' => $total_pages,
                        'top' => $top,
                        'bottom' => $bottom,
                        'current_page' => 'active-tasks',
                        'taskTypeColours' => $taskTypeColours
        ));
        
        $app->render('active-tasks.tpl');
    }

    public function downloadTaskLatestVersion($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);
        $task = $client->cast('Task', $response);
    
        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        $user_id = UserSession::getCurrentUserID();
        
        if (is_null($user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   
        
        $latest_version = $client->call(APIClient::API_VERSION."/tasks/$task_id/version");
        $this->downloadTaskVersion($task_id, $latest_version);
    }

    public function archiveTask($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);
        $task = $client->cast('Task', $response);
        
        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }   
        $user_id = UserSession::getCurrentUserID();
        
        if (is_null($user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   
        
        $request = APIClient::API_VERSION."/tasks/archiveTask/$task_id/user/$user_id";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT);        
        
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
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$taskId";
        $response = $client->call($request);
        $task = $client->cast('Task', $response);

        if ($app->request()->isPost()) {
            $user_id = UserSession::getCurrentUserID();

            $request = APIClient::API_VERSION."/users/$user_id/tasks";
            $response = $client->call($request, HTTP_Request2::METHOD_POST, $task);
            
            $app->redirect($app->urlFor('task-claimed', array(
                        'task_id' => $taskId
            )));
        }

        $convert = $app->request()->get("convertToXliff");
        if (!is_null($convert)) {
            $app->view()->setData('convert', $convert);
        } else {
            $app->view()->setData('convert', "false");
        }

        $request = APIClient::API_VERSION."/languages/getByCode/{$task->getSourceLanguageCode()}";
        $response = $client->call($request);
        $sourceLanguage = $client->cast("Language", $response);

        $request = APIClient::API_VERSION."/languages/getByCode/{$task->getTargetLanguageCode()}";
        $response = $client->call($request);
        $targetLanguage = $client->cast("Language", $response);

        $request = APIClient::API_VERSION."/tasks/$taskId/info";
        $response = $client->call($request);
        $taskMetaData = $client->cast("TaskMetadata", $response);
        
        $app->view()->appendData(array(
                    'task'          => $task,
                    'sourceLanguage'=> $sourceLanguage,
                    'targetLanguage'=> $targetLanguage,
                    'taskMetadata'  => $taskMetaData
        ));
       
        $app->render('task.claim.tpl');
    }

    public function taskClaimed($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);

        $task = $client->cast('Task', $response);
        if (!is_object($task)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }   
        $user_id = UserSession::getCurrentUserID();

        if (is_null($user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   
        $app->view()->setData('task', $task);
        $app->render('task.claimed.tpl');
    }

    public function downloadTaskVersion($task_id, $version, $convert = false)
    {
        $app = Slim::getInstance();
        $app->redirect(Settings::get("site.api").APIClient::API_VERSION.
                                                "/tasks/$task_id/file/?version=$version&convertToXliff=$convert");   
    }

    public function task($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);     
        $task = $client->cast('Task', $response);

        $request = APIClient::API_VERSION."/tasks/$task_id/claimed";
        $taskClaimed = $client->call($request, HTTP_Request2::METHOD_GET);   
        
        $request = APIClient::API_VERSION."/projects/".$task->getProjectId();
        $response = $client->call($request);
        $project = $client->cast("Project", $response);
     
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }
        
        $converter = Settings::get('converter.converter_enabled');
        
        $app->view()->appendData(array(
                    'taskTypeColours' => $taskTypeColours,
                    'project' => $project,
                    'converter'     => $converter,
        ));         

        if ($taskClaimed) {
            switch ($task->getTaskType()) {
                case TaskTypeEnum::POSTEDITING:
                    $this->posteditingTask($task_id);
                    break;
                case TaskTypeEnum::TRANSLATION:
                case TaskTypeEnum::PROOFREADING:
                    $this->taskSimpleUpload($task_id);
                    break;
                case TaskTypeEnum::CHUNKING:
                    $this->taskChunking($task_id);
                    break;
            }
        }else{
     
            if ($task_file_info = $client->castCall("TaskMetadata", APIClient::API_VERSION."/tasks/$task_id/info")) {
                $app->view()->appendData(array(
                    'task_file_info' => $task_file_info,
                    'latest_version' => $client->call(APIClient::API_VERSION."/tasks/$task_id/version")
                ));
            }
            $task_file_info = $client->castCall("TaskMetadata",
                    APIClient::API_VERSION."/tasks/$task_id/info",
                    HTTP_Request2::METHOD_GET, null, array("version" => 0));
            //        $file_path = dirname(Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']));
            //        $appPos = strrpos($file_path, "app");
            //        $file_path = "http://".$_SERVER["HTTP_HOST"].$app->urlFor('home').
            //        substr($file_path, $appPos).'/'.$task_file_info['filename'];
            $file_path= Settings::get("site.api").APIClient::API_VERSION."/tasks/$task_id/file";

            $app->view()->appendData(array(
                        'task' => $task,
                        'taskTypeColours' => $taskTypeColours,
                        'project' => $project,
                        'file_preview_path' => $file_path,
                        'filename' => $task_file_info->getFilename()
            ));

            $app->render('task.view.tpl');
        }
    }

    public function posteditingTask($taskId)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $userId = UserSession::getCurrentUserID();

        $fieldName = "mergedFile";
        $errorMessage = null;

        $request = APIClient::API_VERSION."/tasks/$taskId";
        $response = $client->call($request);
        $task = $client->cast("Task", $response);

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
                    $request = APIClient::API_VERSION."/tasks/$taskId/file/".
                        urlencode($_FILES[$fieldName]['name'])."/$userId";
                    $errorMessage = $client->call($request,
                        HTTP_Request2::METHOD_PUT, null, null, "", $filedata);
                } catch (Exception  $e) {
                    $uploadError = true;
                    $errorMessage = 'File error: ' . $e->getMessage();
                }
            }

            if (is_null($errorMessage)) {
                $app->redirect($app->urlFor("task-uploaded", array("task_id" => $taskId)));
            } else {
                $app->flashNow("error", $errorMessage);
            }
        }

        $graphBuilder = new UIWorkflowBuilder();
        $graph = $graphBuilder->buildProjectGraph($task->getProjectId());

        $found = false;
        $preReqTasks = array();
        $currentLayer = $graph->getRootNodeList();
        $nextLayer = array();
        while (!$found && count($currentLayer) > 0) {
            foreach ($currentLayer as $node) {
                if ($node->getTaskId() == $task->getId()) {
                    $found = true;
                    foreach ($node->getPreviousList() as $pNode) {
                        $request = APIClient::API_VERSION."/tasks/{$pNode->getTaskId()}";
                        $response = $client->call($request);
                        $pTask = $client->cast("Task", $response);
                        if (is_object($pTask)) {
                            $preReqTasks[] = $pTask;
                        }
                    }
                }
                foreach ($node->getNextList() as $nNode) {
                    if (!in_array($nNode, $nextLayer)) {
                        $nextLayer[] = $nNode;
                    }
                }
            }
            $currentLayer = $nextLayer;
            $nextLayer = array();
        }

        $app->view()->appendData(array(
                    'task'          => $task,
                    'preReqTasks'   => $preReqTasks,
                    'fieldName'     => $fieldName,
                    'errorMessage'  => $errorMessage
        ));

        $app->render('task-postediting.tpl');
    }

    public function taskSimpleUpload($taskId)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $fieldName = "fileUpload";
        $errorMessage = null;
        $userId = UserSession::getCurrentUserID();

        $request = APIClient::API_VERSION."/tasks/$taskId";
        $response = $client->call($request);
        $task = $client->cast("Task", $response);

        if ($app->request()->isPost()) {
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($fieldName);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        
            if (is_null($errorMessage)) {
                try {
                    $post = (object) $app->request()->post();
                    $filedata = file_get_contents($_FILES[$fieldName]['tmp_name']);
                    
                    if ($post->submit == 'XLIFF') {
                        $request = APIClient::API_VERSION."/tasks/$taskId/file/?convertFileXliff=true";
                        $response = $client->call($request, HTTP_Request2::METHOD_PUT, null, null, null, $filedata);
                    } else if ($post->submit == 'submit') {
                        $errorMessage = $client->call(APIClient::API_VERSION.
                            "/tasks/uploadOutputFile/$taskId/".urlencode($_FILES[$fieldName]['name'])."/$userId",
                            HTTP_Request2::METHOD_PUT, null, null, null, $filedata);
                    }
                
                } catch (Exception  $e) {
                    $errorMessage = 'File error: ' . $e->getMessage();
                }
            }

            if (is_null($errorMessage)) {
                $app->redirect($app->urlFor("task-uploaded", array("task_id" => $taskId)));
            } else {
                $app->flashNow("error", $errorMessage);
            }
        }

        $request = APIClient::API_VERSION."/projects/{$task->getProjectId()}";
        $response = $client->call($request);
        $project = $client->cast("Project", $response);

        $request = APIClient::API_VERSION."/orgs/{$project->getOrganisationId()}";
        $response = $client->call($request);
        $org = $client->cast("Organisation", $response);

        $request = APIClient::API_VERSION."/tasks/{$task->getId()}/version";
        $taskVersion = $client->call($request, HTTP_Request2::METHOD_GET);

        $file_previously_uploaded = false;
        if ($taskVersion > 0) {
            $file_previously_uploaded = true;
        }

        $request = APIClient::API_VERSION."/tasks/$taskId/info";
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, array("version" => 0));
        $taskFileInfo = $client->cast("TaskMetadata", $response);
        $filename = $taskFileInfo->getFilename();

        $converter = Settings::get('converter.converter_enabled');

        $app->view()->appendData(array(
                    'task'          => $task,
                    'project'       => $project,
                    'org'           => $org,
                    'filename'      => $filename,
                    'converter'     => $converter,
                    'fieldName'     => $fieldName,
                    'max_file_size' => TemplateHelper::maxFileSizeMB(),
                    'file_previously_uploaded' => $file_previously_uploaded
        ));

        $app->render('task-simple-upload.tpl');
    }

    public function taskUploaded($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);     
        $task = $client->cast('Task', $response);

        $request = APIClient::API_VERSION."/projects/".$task->getProjectId();
        $response = $client->call($request);
        $project = $client->cast("Project", $response);

        $request = APIClient::API_VERSION."/orgs/{$project->getOrganisationId()}";
        $response = $client->call($request);
        $org = $client->cast("Organisation", $response);
       
        $tip_selector = new TipSelector();
        $tip = $tip_selector->selectTip();
        
        $org_id = $project->getOrganisationId();
        $app->view()->appendData(array(
                'org_name' => $org->getName(),
                'tip'      => $tip
        ));     
        
        $app->render('task.uploaded.tpl');
    }

    public function taskAlter($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $word_count_err = null;
        $deadlockError = null;
        $deadlineError = "";

        $extra_scripts = "

        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/selectable.css\" />
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/datepickr.css\" />
        <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/datepickr.js\"></script>
        <script type=\"text/javascript\">
            window.onload = function() {
                new datepickr(\"deadline_date\");
            };
        </script>".file_get_contents("http://".$_SERVER["HTTP_HOST"].$app->urlFor("home").'ui/js/task-alter.js');

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);     
        $task = $client->cast('Task', $response);

        $preReqTaskIds = array();
        $hiddenPreReqList = "";
        $request = APIClient::API_VERSION."/tasks/$task_id/prerequisites";
        $response = $client->call($request);
        if($response) {
            foreach($response as $taskId) {
                $request = APIClient::API_VERSION."/tasks/$taskId";
                $stdObject = $client->call($request);
                if($stdObject) {
                    $preReq = $client->cast("Task", $stdObject);
                    if ($preReq->getId() != $task->getId()) {
                        $preReqTaskIds[] = $preReq->getId();
                        $hiddenPreReqList = $preReq->getId().",";
                    }
                }
            }
        }

        $project = null;
        $request = APIClient::API_VERSION."/projects/".$task->getProjectId();
        $response = $client->call($request);
        if($response) {
            $project = $client->cast("Project", $response);
        }

        $projectTasks = array();
        $request = APIClient::API_VERSION."/projects/".$task->getProjectId()."/tasks";
        $response = $client->call($request);
        if ($response) {
            foreach ($response as $row) {
                $projectTask = $client->cast("Project", $row);
                if ($projectTask->getId() != $task->getId()) {
                    $projectTasks[] = $client->cast("Project", $row);
                }
            }
        }
        
        $deadlineDate = date("F dS, Y", strtotime($task->getDeadline()));
        $deadlineTime = date("H:i", strtotime($task->getDeadline()));

        $app->view()->setData('task', $task);
        
        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if ($post->title != '') {
                $task->setTitle($post->title);
            }
            
            if ($post->impact != '') {
                $task->setComment($post->impact);
            }

            $deadline = "";
            if ($post->deadline_date != '') {
                $deadline = strtotime($post->deadline_date);

                if($deadline) {
                    if ($post->deadline_time != '') {
                        if (TemplateHelper::isValidTime($post->deadline_time) == true) {
                            $deadline = TemplateHelper::addTimeToUnixTime($deadline, $post->deadline_time);
                        } else {
                            $deadlineError = "Invalid time format. Please enter time in a 24-hour format like ";
                            $deadlineError .= "this 16:30";
                        }
                    }
                } else {
                    $deadline = "";
                    $deadlineError = "Invalid date format";
                }
            }

            if($deadline != "" && $deadlineError == "") {
                $task->setDeadline(date("Y-m-d H:i:s", $deadline));
            }
            
            if ($post->target != '') {
                $task->setTargetLanguageCode($post->target);
            }   
             
            if ($post->targetCountry != '') {
                $task->setTargetCountryCode($post->targetCountry);
            }   
              
            if (ctype_digit($post->word_count)) {
                $task->setWordCount($post->word_count);                
            } else if ($post->word_count != '') {
                $word_count_err = "Word Count must be numeric";
            } else {
                $word_count_err = "Word Count cannot be blank";
            }

            if ($word_count_err == '' && $deadlineError == '') {
    
                $taskPreReqIds = array();
                foreach ($projectTasks as $projectTask) {
                    $request = APIClient::API_VERSION."/tasks/{$projectTask->getId()}/prerequisites";
                    $taskPreReqIds[$projectTask->getId()] = $client->call($request);
                }
                $selectedPreReqs = explode(",", $post->selectedList);
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

                    $request = APIClient::API_VERSION."/tasks/$task_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task);
    
                    foreach ($preReqTaskIds as $preReqId) {
                        if(!in_array($preReqId, $selectedList)) {
                            $request = APIClient::API_VERSION.
                                "/tasks/".$task->getId()."/prerequisites/$preReqId";
                            $client->call($request, HTTP_Request2::METHOD_DELETE);
                        }
                    }

                    foreach($selectedList as $taskId) {
                        if (is_numeric($taskId)) {
                            $request = APIClient::API_VERSION."/tasks/".
                                $task->getId()."/prerequisites/$taskId";
                            $client->call($request, HTTP_Request2::METHOD_PUT);
                        }
                    }   

                    $app->redirect($app->urlFor("task-view", array("task_id" => $task_id)));
                } else {
                    //A deadlock occured
                    $deadlockError = "A deadlock has occured, please check your".
                        " task pre-requisites.";
                }
            }
        }
         
        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
       
        $app->view()->appendData(array(
                              'project'         => $project,
                              'extra_scripts'   => $extra_scripts,
                              'languages'       => $languages,
                              'countries'       => $countries,
                              'projectTasks'    => $projectTasks,
                              'taskPreReqIds'   => $preReqTaskIds,
                              'hiddenPreReqList'=> $hiddenPreReqList,
                              'word_count_err'  => $word_count_err,
                              'deadlockError'   => $deadlockError,
                              'deadlineDate'    => $deadlineDate,
                              'deadlineTime'    => $deadlineTime,
                              'deadline_error'  => $deadlineError
        ));
        
        $app->render('task.alter.tpl');
    }

    public function taskView($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID();

        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);     
        $task = $client->cast('Task', $response);        
        $app->view()->setData('task', $task);

        $request = APIClient::API_VERSION."/projects/".$task->getProjectId();
        $response = $client->call($request);
        $project = $client->cast("Project", $response);

        $request = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($request);
        $user = $client->cast('User', $response);      
        
        if ($task_file_info = $client->castCall("TaskMetadata", APIClient::API_VERSION."/tasks/$task_id/info")) {
            $app->view()->appendData(array(
                'task_file_info' => $task_file_info,
                'latest_version' => $client->call(APIClient::API_VERSION."/tasks/$task_id/version")
            ));
        }
        $task_file_info = $client->castCall("TaskMetadata",
                APIClient::API_VERSION."/tasks/$task_id/info",
                HTTP_Request2::METHOD_GET, null, array("version" => 0));

        $file_path= Settings::get("site.api").APIClient::API_VERSION."/tasks/$task_id/file";
       
        $app->view()->appendData(array(
            'file_preview_path' => $file_path,
            'filename' => $task_file_info->getFilename()
        ));      
        
         
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->published) && isset($post->task_id)) {
                
                $request = APIClient::API_VERSION."/tasks/{$post->task_id}";
                $response = $client->call($request);     
                $task = $client->cast('Task', $response);        
                
                if($post->published) {                     
                    $task->setPublished(1);                    
                    $request = APIClient::API_VERSION."/tasks/{$post->task_id}";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task);                 
                } else {
                    $task->setPublished(0);                    
                    $request = APIClient::API_VERSION."/tasks/{$post->task_id}";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task);      
                }
                
                $app->view()->appendData(array(
                             'task' => $task
                ));
            }  
            
            if (isset($post->notify) && $post->notify == "true") {
                $request = APIClient::API_VERSION."/users/$user_id/tracked_tasks/{$task->getId()}";
                $userTrackTask = $client->call($request, HTTP_Request2::METHOD_PUT);
                
                if ($userTrackTask) {
                    $app->flashNow("success", 
                            "You are now tracking this task and will receive email notifications
                            when its status changes.");
                } else {
                    $app->flashNow("error", "Unable to register for notifications for this task.");
                }   
            } else if(isset($post->notify) && $post->notify == "false") {

                $request = APIClient::API_VERSION."/users/$user_id/tracked_tasks/{$task->getId()}";
                $userIgnoreTask = $client->call($request, HTTP_Request2::METHOD_DELETE);
                
                if ($response) {
                    $app->flashNow("success", 
                            "You are no longer tracking this task and will receive no
                            further emails."
                    );
                } else {
                    $app->flashNow("error", "Unable to unregister for this notification.");
                }   
            } 
        } 
        
        $taskMetaData = array();
        if(is_object($task)) {
            $metaData = array();
            $request = APIClient::API_VERSION."/users/subscribedToTask/$user_id/$task_id";
            $response = $client->call($request);
            if($response == 1) {
                $metaData['tracking'] = true;
            } else {
                $metaData['tracking'] = false;
            }
            $taskMetaData[$task_id] = $metaData;
        }

        $app->view()->appendData(array(
                     'taskMetaData' => $taskMetaData
        ));        
        
        $request = APIClient::API_VERSION."/users/subscribedToTask/{$user->getUserId()}/$task_id";
        $registered = $client->call($request);         

        $request = APIClient::API_VERSION."/orgs/{$project->getOrganisationId()}";
        $response = $client->call($request);     
        $org = $client->cast('Organisation', $response);
        
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }
        
        $request = APIClient::API_VERSION."/orgs/isMember/{$project->getOrganisationId()}/$user_id";
        $isOrgMember = $client->call($request);
        
        if($isOrgMember) {     
            $app->view()->appendData(array('isOrgMember' => $isOrgMember));
        }

        $app->view()->appendData(array(
                'org' => $org,
                'project' => $project,
                'registered' => $registered,
                'taskTypeColours' => $taskTypeColours
        ));

        $app->render('task.view.tpl');
    }

    public function taskCreate($project_id)
    {
        $app = Slim::getInstance();
        $titleError = null;
        $wordCountError = null;
        $deadlineError = null;
        $taskPreReqs = array();
        $client = new APIClient();
        $task = new Task();

        $request = APIClient::API_VERSION."/projects/$project_id";
        $response = $client->call($request);
        $project = $client->cast("Project", $response);

        $projectTasks = array();
        $request = APIClient::API_VERSION."/projects/$project_id/tasks";
        $response = $client->call($request);
        if ($response != null) {
            foreach ($response as $row) {
                $projectTask = $client->cast("Task", $row);

                if(is_object($projectTask)) {
                    $projectTasks[] = $projectTask;
                }
            }
        }

        $task->setProjectId($project_id);

        //task inherits souce details from project
        $task->setSourceLanguageCode($project->getSourceLanguageCode());
        $task->setSourceCountryCode($project->getSourceCountryCode());

        //default status, change when prereqs are working
        $task->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);

        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();

            if ($post->title != '') {
                $task->setTitle($post->title);
            } else {
                $titleError = "Title must not be blank";
            }

            if ($post->comment != '') {
                $task->setComment($post->comment);
            }

            if ($post->targetCountry != '') {
                $task->setTargetCountryCode($post->targetCountry);
            }

            if ($post->targetLanguage != '') {
                $task->setTargetLanguageCode($post->targetLanguage);
            }

            if ($post->taskType != '') {
                $task->setTaskType($post->taskType);
            }

            if (ctype_digit($post->word_count)) {
                $task->setWordCount($post->word_count);
            } else if ($post->word_count != '') {
                $wordCountError = "Word Count must be numeric";
            } else {
                $wordCountError = "Word Count cannot be blank";
            }

            $deadline = "";
            if ($post->deadline_date != '') {
                $deadline = strtotime($post->deadline_date);
                
                if($deadline) {
                    if ($post->deadline_time != '') {
                        if (TemplateHelper::isValidTime($post->deadline_time) == true) {
                            $deadline = TemplateHelper::addTimeToUnixTime($deadline, $post->deadline_time);
                        } else {
                            $deadlineError = "Invalid time format. Please enter time in a 24-hour format like ";
                            $deadlineError .= "this 16:30";
                        }
                    }
                } else {
                    $deadline = "";
                    $deadlineError = "Invalid date format";
                }
            }
            
            if ($deadline != "" && $deadlineError == "") {
                $task->setDeadline(date("Y-m-d H:i:s", $deadline));
            }

            if (isset($post->published)) {
                $task->setPublished("1");
            }

            if(is_null($titleError) && is_null($wordCountError) && is_null($deadlineError)) {
                $request = APIClient::API_VERSION."/tasks";
                $response = $client->call($request, HTTP_Request2::METHOD_POST, $task);
                $task = $client->cast("Task", $response);

                if (isset($post->selectedList) && $post->selectedList != "") {
                    $selectedList = explode(",", $post->selectedList);
                    foreach($selectedList as $taskId) {
                        if (is_numeric($taskId)) {
                            $request = APIClient::API_VERSION."/tasks/".
                                $task->getId()."/prerequisites/$taskId";
                            $client->call($request, HTTP_Request2::METHOD_PUT);
                        }
                    }
                }
            
                $app->redirect($app->urlFor("task-created", array("task_id" => $task->getId())));
            }
        }

        $deadlineDate = date("F dS, Y", strtotime($task->getDeadline()));
        $deadlineTime = date("H:i", strtotime($task->getDeadline()));

        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();

        $taskTypes = array();
        $taskTypes[TaskTypeEnum::CHUNKING] = "Chunking";
        $taskTypes[TaskTypeEnum::TRANSLATION] = "Translation";
        $taskTypes[TaskTypeEnum::PROOFREADING] = "Proofreading";
        $taskTypes[TaskTypeEnum::POSTEDITING] = "Postediting";

        $extra_scripts = "
        <link rel=\"stylesheet\" href=\"".$app->urlFor("home")."resources/css/jquery-ui.css\" />
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/selectable.css\" />
        <script type=\"text/javascript\" src=\"".$app->urlFor("home")."ui/js/jquery-ui.js\"></script>
        <script>
        $(function() {
            $( \"#selectable\" ).selectable({
                stop: function() {
                    var result = $( \"#select-result\" ).empty();
                    var selectedList = $(\"#selectedList\").val(\"\");
                    $( \".ui-selected\", this ).each(function() {
                        var index = $( \"#selectable li\" ).index( this );
                        var taskId = $( \"#selectable li:nth-child(\" + (index + 1) + \")\").val();
                        result.append( \" #\" + ( index + 1 ) );
                        selectedList.val(selectedList.val() + taskId + \",\");
                    });
                }
            });
        });
        </script>

        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/datepickr.css\" />
        <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/datepickr.js\"></script>
        <script type=\"text/javascript\">
            window.onload = function() {
                new datepickr(\"deadline_date\");
            };
        </script>
        ";

        $app->view()->appendData(array(
                'project'       => $project,
                'task'          => $task,
                'projectTasks'  => $projectTasks,
                'taskPreReqs'   => $taskPreReqs,
                'deadlineDate'  => $deadlineDate,
                'deadlineTime'  => $deadlineTime,
                'languages'     => $languages,
                'countries'     => $countries,
                'taskTypes'     => $taskTypes,
                'extra_scripts' => $extra_scripts,
                'titleError'    => $titleError,
                'wordCountError'=> $wordCountError,
                'deadlineError' => $deadlineError
        ));

        $app->render('task.create.tpl');
    }

    public function taskCreated($taskId)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tasks/$taskId";
        $response = $client->call($request);
        $task = $client->cast("Task", $response);

        $app->view()->appendData(array(
                "project_id" => $task->getProjectId(),
                "task_id"    => $task->getId()
        ));

        $app->render("task.created.tpl");
    }
    
    public function taskChunking($task_id)
    {  
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID();
        $taskTypeErr = null;        
        
        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);     
        $task = $client->cast('Task', $response); 
        
        $request = APIClient::API_VERSION."/projects/{$task->getProjectId()}";
        $response = $client->call($request);
        $project = $client->cast("Project", $response);
        
        $numTaskTypes = Settings::get("ui.task_types");
        $maxChunks = Settings::get("site.max_chunking");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }
    
        $language_list = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
        
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post(); 
            
            if(!isset($post->translation_0) && !isset($post->postediting_0) && !isset($post->proofreading_0)) {
                $app->flashNow('Warning', 'Task <b>Type</b> must be set for all chunks.');
            } else {
                $chunkValue = $post->chunkValue;
                $upload_error = false;                
                for($i=0; $i < $chunkValue && !$upload_error; $i++) {                    
                    try {
                        TemplateHelper::validateFileHasBeenSuccessfullyUploaded('chunkUpload_'.$i);
                        $taskModel = new Task();
                        if(isset($post->translation_0)) {
                            $this->setTaskModelData($taskModel, $project, $task, $i);
                            $taskModel->setTaskType(TaskTypeEnum::TRANSLATION);
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);
                            $createdTranslation =  $client->cast('Task', $response);
                            try {                    
                                $filedata = file_get_contents($_FILES['chunkUpload_'.$i]['tmp_name']);                    
                                $error_message = $client->call(APIClient::API_VERSION."/tasks/{$createdTranslation->getId()}/file/".
                                        urlencode($_FILES['chunkUpload_'.$i]['name'])."/$user_id",
                                        HTTP_Request2::METHOD_PUT, null, null, "", $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = 'File error: ' . $e->getMessage();
                            } 
                            
                        } else if(isset($post->proofreading_0)) {
                            $this->setTaskModelData($taskModel, $project, $task, $i);
                            $taskModel->setTaskType(TaskTypeEnum::PROOFREADING);                         
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);
                            $createdProofReading = $client->cast('Task', $response);
                            
                            try {                    
                                $filedata = file_get_contents($_FILES['chunkUpload_'.$i]['tmp_name']);                    
                                $error_message = $client->call(APIClient::API_VERSION."/tasks/{$createdProofReading->getId()}/file/".
                                        urlencode($_FILES['chunkUpload_'.$i]['name'])."/$user_id",
                                        HTTP_Request2::METHOD_PUT, null, null, "", $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = 'File error: ' . $e->getMessage();
                            }   
                        } else if(isset($post->postediting_0)) {
                            $this->setTaskModelData($taskModel, $project, $task, $i);                       
                            $taskModel->setTaskType(TaskTypeEnum::POSTEDITING);                         
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);
                            $createdPostEditing = $client->cast('Task', $response);
                            
                            try {                    
                                $filedata = file_get_contents($_FILES['chunkUpload_'.$i]['tmp_name']);                    
                                $error_message = $client->call(APIClient::API_VERSION."/tasks/{$createdPostEditing->getId()}/file/".
                                        urlencode($_FILES['chunkUpload_'.$i]['name'])."/$user_id",
                                        HTTP_Request2::METHOD_PUT, null, null, "", $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = 'File error: ' . $e->getMessage();
                            }    
                        }
                    } catch (Exception $e) {
                        $upload_error = true;
                        $file_upload_err = $e->getMessage();
                    } 
                }
                $task->setTaskStatus(TaskStatusEnum::COMPLETE);
                $request = APIClient::API_VERSION."/tasks/$task_id";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task); 
                $app->redirect($app->urlFor("project-view", array("project_id" => $task->getProjectId())));
            }
            
                
        }
        
        $extraScripts = file_get_contents("http://".$_SERVER['HTTP_HOST'].$app->urlFor("home").'ui/js/task-chunking.js');
        
        $app->view()->appendData(array(
            'project'           => $project,
            'task'              => $task,
            'taskTypeColours'   => $taskTypeColours,
            'maxChunks'         => $maxChunks,
            'languages'         => $language_list,
            'countries'         => $countries,
            'extra_scripts'      => $extraScripts
        ));
        
        $app->render('task-chunking.tpl');
    }
    
    public function taskFeedback($task_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();  
        $user_id = UserSession::getCurrentUserID();
        
        $request = APIClient::API_VERSION."/tasks/$task_id";
        $response = $client->call($request);     
        $task = $client->cast('Task', $response);   
        
        $request = APIClient::API_VERSION."/projects/{$task->getProjectId()}";
        $response = $client->call($request);     
        $project = $client->cast('Project', $response);

        $claimant = null;
        $request = APIClient::API_VERSION."/tasks/{$task->getId()}/user";
        $response = $client->call($request);
        if ($response) {
            $claimant = $client->cast("User", $response);
        }
        
        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }
        
        $task_tags = null;
        $request = APIClient::API_VERSION."/tasks/$task_id/tags";
        $taskTags = $client->call($request);
        if($taskTags) {
            foreach ($taskTags as $tag) {
                $task_tags[] = $client->cast('Tag', $tag);
            }
        }
        
        
        $app->view()->appendData(array(
            'project' => $project,
            'task' => $task,
            'claimant' => $claimant,
            'taskTypeColours' => $taskTypeColours,
            'task_tags' => $task_tags
        ));
        
        $app->render('task.feedback.tpl');
    }
    
    private function setTaskModelData($taskModel, $project, $task, $i) {
        $taskModel->setTitle($_FILES['chunkUpload_'.$i]['name']);
        $taskModel->setSourceLanguageCode($project->getSourceLanguageCode());
        $taskModel->setSourceCountryCode($project->getSourceCountryCode());
        $taskModel->setTargetLanguageCode($task->getTargetLanguageCode());
        $taskModel->setTargetCountryCode($task->getTargetCountryCode());
        $taskModel->setProjectId($project->getId());
        $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
    }
}
