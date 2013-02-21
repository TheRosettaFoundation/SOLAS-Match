<?php

require_once "Common/TaskTypeEnum.php";
require_once "Common/TaskStatusEnum.php";

class ProjectRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();     
        
        $app->get("/project/view/:project_id/", array($middleware, "authUserIsLoggedIn"),
        array($this, "projectView"))->via("POST")->name("project-view");
        
        $app->get("/project/alter/:project_id/", array($middleware, "authUserForOrgProject"), 
        array($this, "projectAlter"))->via("POST")->name("project-alter");
        
        $app->get("/project/create/:org_id/", array($middleware, "authUserForOrg"),
        array($this, "projectCreate"))->via("GET", "POST")->name("project-create");    
        
        $app->get("/project/id/:project_id/created/", array($middleware, "authUserForOrgProject"),
        array($this, "projectCreated"))->name("project-created");
        
        $app->get("/project/id/:project_id/mark-archived/", array($middleware, "authUserForOrgProject"),
        array($this, "archiveProject"))->name("archive-project");

        $app->get("/project/test/:project_id", array($this, "test"));
    }

    public function test($projectId)
    {
        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $time1 = $time; 

        $builder = new UIWorkflowBuilder();
        $graph = $builder->buildProjectGraph($projectId);
        $builder->printGraph($graph);

        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $time2 = $time;

        $totaltime = ($time2 - $time1);
        echo "<BR>Running Time: $totaltime seconds.";
    }
  
    public function projectView($project_id)
    {
        $app = Slim::getInstance();
        $user_id = UserSession::getCurrentUserID();
        $projectDao = new ProjectDao();
        $taskDao = new TaskDao();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $project = $projectDao->getProject(array('id' => $project_id));        
        $app->view()->setData("project", $project);
         
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
           
            $task = null;
            if(isset($post->task_id)) {
                $task = $taskDao->getTask(array('id' => $post->task_id));
            } elseif (isset($post->revokeTaskId)) {
                $task = $taskDao->getTask(array('id' => $post->revokeTaskId));
            }
            
            if(isset($post->publishedTask) && isset($post->task_id)) { 
                if($post->publishedTask) {                     
                    $task->setPublished(1);
                } else {
                    $task->setPublished(0);                    
                }
                $taskDao->updateTask($task);
            }
            
            if (isset($post->trackProject)) {
                if ($post->trackProject) {
                    $userTrackProject = $userDao->trackProject($user_id, $project->getId());
                    if ($userTrackProject) {
                        $app->flashNow("Success", 
                                "You are now tracking this Project and will receive email notifications
                                when its status changes.");
                    } else {
                        $app->flashNow("error", "Unable to register for notifications for this Project.");
                    }   
                } else {
                    $userUntrackProject = $userDao->untrackProject($user_id, $project->getId());
                    if ($userUntrackProject) {
                        $app->flashNow("success", 
                                "You are no longer tracking this Project and will receive no
                                further emails."
                        );
                    } else {
                        $app->flashNow("error", "Unable to unregister for this notification.");
                    }   
                }
            } elseif(isset($post->trackTask)) {
                if($task && $task->getTitle() != "") {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task {$task->getId()}";
                }

                if(!$post->trackTask) {
                    $response = $userDao->untrackTask($user_id, $task->getId());
                    if ($response) {
                        $app->flashNow("success", "No longer receiving notifications from $task_title.");
                    } else {
                        $app->flashNow("error", "Unable to unsubscribe from $task_title's notifications");
                    }
                } else {
                    $response = $userDao->trackTask($user_id, $task_id);
                    if ($response) {
                        $app->flashNow("success", "You will now receive notifications for $task_title.");
                    } else {
                        $app->flashNow("error", "Unable to subscribe to $task_title.");
                    }
                }
            }
            
            //This should not be here!!!!!!!!!
            if(isset($post->feedback)) {
                $feedback = new FeedbackEmail();               
    
                $feedback->setTaskId($task_id);
                $feedback->addUserId($post->revokeUserId);
                $feedback->setFeedback($post->feedback);
                $taskDao->sendFeedback($feedback);

                if(isset($post->revokeTask) && $post->revokeTask) {
                    $taskRevoke = $userDao->unclaimTask($post->revokeUserId, $post->revokeTaskId);
                    $claimant = $userDao->getUser(array('id' => $post->revokeUserId));
                    if(!$taskRevoke) {
                        $app->flashNow("taskSuccess", "<b>Success</b> - The task 
                            <a href=\"{$app->urlFor("task-view", array("task_id" => $task_id))}\">{$task->getTitle()}</a>
                            has been successfully revoked from <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $user_id))}\">{$claimant->getDisplayName()}</a> 
                            This user will be notified by e-mail and provided with your feedback.");
                    } else {
                        $app->flashNow("taskError", "<b>Error</b> - Unable to revoke the task ".
                            "<a href=\"{$app->urlFor("task-view", array("task_id" => $task_id))}\">{$task->getTitle()}\"</a>
                             from <a href=\"{$app->urlFor("user-public-profile", array("user_id" => $user_id))}\">{$claimant->getDisplayName()}</a>. Please try again later.");                                
                    }
                }
            }
        }   

        $org = $orgDao->getOrganisation(array('id' => $project->getOrganisationId()));
        $project_tags = $projectDao->getProjectTags($project_id);
        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        if($isOrgMember) {
            $userSubscribedToProject = $userDao->isSubscribedToProject($user_id, $project_id);
            $taskMetaData = array();
            $project_tasks = $projectDao->getProjectTasks($project_id);
            foreach($project_tasks as $task) {
                $task_id = $task->getId();

                $metaData = array();
                $response = $userDao->isSubscribedToTask($user_id, $task_id);
                if($response == 1) {
                    $metaData['tracking'] = true;
                } else {
                    $metaData['tracking'] = false;
                }
                $taskMetaData[$task_id] = $metaData;
            }

            $numTaskTypes = Settings::get("ui.task_types");
            $taskTypeColours = array();

            for($i=1; $i <= $numTaskTypes; $i++) {
                $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
            }

            $app->view()->appendData(array(
                    "org" => $org,
                    "projectTasks" => $project_tasks,
                    "taskMetaData" => $taskMetaData,
                    "taskTypeColours" => $taskTypeColours,
                    "userSubscribedToProject" => $userSubscribedToProject,
                    "project_tags" => $project_tags
            ));
            
        } else {   

            $app->view()->appendData(array(
                    "org" => $org,
                    "project_tags" => $project_tags
            ));
        }
        
        $app->render("project.view.tpl");
    }  
    
    public function projectAlter($project_id)
    {
        $app = Slim::getInstance();
        $deadlineError = '';
        $projectDao = new ProjectDao();

        $project = $projectDao->getProject(array('id' => $project_id));
        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if ($post->title != "") {
                $project->setTitle($post->title);
            }

            if ($post->description != "") {
                $project->setDescription($post->description);
            }

            $deadlineInMSecs = "";
            if ($post->deadlineDate != "") {
                $deadlineInMSecs = strtotime($post->deadlineDate);

                if ($deadlineInMSecs) {
                    if ($post->deadlineTime != "") {
                        if (TemplateHelper::isValidTime($post->deadlineTime) == true) {
                            $deadlineInMSecs = TemplateHelper::addTimeToUnixTime($deadlineInMSecs,
                                    $post->deadlineTime);
                        } else {
                            $deadlineError = "Invalid time format. Please enter time in a 24-hour format like ";
                            $deadlineError .= "this 16:30";
                        }
                    }
                } else {
                    $deadlineInMSecs = "";
                    $deadlineError = "Invalid date format";
                }
            }

            if ($deadlineInMSecs != "" && $deadlineError == "") {
                $project->setDeadline(date("Y-m-d H:i:s", $deadlineInMSecs));
            }
            
            if ($post->sourceLanguage != "") {
                $project->setSourceLanguageCode($post->sourceLanguage);
            }
            
            if ($post->sourceCountry != "") {
                $project->setSourceCountryCode($post->sourceCountry);
            }   
             
            if ($post->reference != "" && $post->reference != "http://") {
                $project->setReference($post->reference);
            }
            
            if ($post->tags != "") {
                $tags = TemplateHelper::separateTags($post->tags);
                foreach ($tags as $tag) {
                    $project->addTag($tag);
                }
            }
            
            if ($deadlineError == '') {
                $projectDao->updateProject($project);
                $app->redirect($app->urlFor("project-view", array("project_id" => $project_id)));
            }
        }
        $langDao = new LanguageDao();
        $countryDao = new CountryDao();
        $languages = $langDao->getLanguage(null);
        $countries = $countryDao->getCountry(null);
       
        $deadlineDate = date("F dS, Y", strtotime($project->getDeadline()));
        $deadlineTime = date("H:i", strtotime($project->getDeadline()));
        
        $tags = $project->getTagList();
        $tag_list = "";
        if ($tags != null) {
            foreach ($tags as $tag) {
                $tag_list .= $tag . " ";
            }
        }

        $extra_scripts = "
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/datepickr.css\" />
        <script type=\"text/javascript\" src=\"{$app->urlFor("home")}resources/bootstrap/js/datepickr.js\"></script>
        <script type=\"text/javascript\">
            window.onload = function() {
                new datepickr(\"deadlineDate\");
            };
        </script>";
        
        $app->view()->appendData(array(
                              "project"         => $project,
                              "deadlineDate"    => $deadlineDate,
                              "deadlineTime"    => $deadlineTime,
                              "languages"       => $languages,
                              "countries"       => $countries,
                              "tag_list"        => $tag_list,
                              "deadlineError"   => $deadlineError,
                              "extra_scripts"   => $extra_scripts
        ));
        
        $app->render("project.alter.tpl");
    }
    
    public function projectCreate($org_id)
    {
        $app = Slim::getInstance();
        $projectDao = new ProjectDao();
        $taskDao = new TaskDao();

        $user_id = UserSession::getCurrentUserID(); 
        $field_name = "new_task_file";
        $tags = null;

        $error          = null;
        $title_err      = null;
        $deadline_err   = null;
        $word_count_err = null;
        $targetLanguage_err = null;
        $project       = new Project();

        if ($app->request()->isPost()) {            
            $post = (object) $app->request()->post();
            
            if(($post->title != "")) {
                $project->setTitle($post->title);
            } else {
                $title_err = "Project <b>Title</b> must be set.";
            }            
            
            if($post->deadline != "") {
                $project->setDeadline($post->deadline);
            } else {
                $deadline_err = "Project <b>Deadline</b> must be set.";
            }
            
            if(($post->description != "")) {
                $project->setDescription($post->description);
            }
            if(($post->reference != "")) {
                $project->setReference($post->reference);
            }
            if(($post->word_count != "")) {
                $project->setWordCount($post->word_count);
            }
            if (isset($post->sourceLanguage) && $post->sourceLanguage != "") {
                $project->setSourceLanguageCode($post->sourceLanguage);
            }
            if ($post->sourceCountry != "") {
                $project->setSourceCountryCode($post->sourceCountry);
            }
            
            $tags = $post->tags;
            if (is_null($tags)) {
                $tags = "";
            }

            $tag_list = TemplateHelper::separateTags($tags);
            if($tag_list) {
                foreach ($tag_list as $tag) {
                    $project->addTag($tag);
                }
            } 
            
            for ($i=0; $i < $post->targetLanguageArraySize; $i++) {
                if(!isset($post->{"chunking_".$i}) && !isset($post->{"translation_".$i}) &&
                    !isset($post->{"proofreading_".$i}) && !isset($post->{"postediting_".$i})) {
                    $targetLanguage_err = "At least one <b>Task Type</b> must be set for each <b>Target Language</b>.";
                    break;
                }
            }
            
            $upload_error = false;
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($field_name);
            } catch (Exception $e) {
                $upload_error = true;
                $file_upload_err = $e->getMessage();
            }            

            
            if(is_null($title_err) && is_null($deadline_err) && is_null($targetLanguage_err) && !$upload_error) { 
                $project->setOrganisationId($org_id);
                if($project = $projectDao->createProject($project)) {
                    $taskModel = new Task();
                    //$taskModel->setTitle($project->getTitle());
                    $taskModel->setTitle($_FILES[$field_name]["name"]);
                    $taskModel->setSourceLanguageCode($project->getSourceLanguageCode());
                    $taskModel->setSourceCountryCode($project->getSourceCountryCode());
                    $taskModel->setProjectId($project->getId());
                    
                    $translationTaskId = 0;
                    $proofreadingTaskId = 0;
                    $posteditingTaskId = 0;
                    
                    for ($i=0; $i < $post->targetLanguageArraySize; $i++) {

                        $taskModel->setTargetLanguageCode($post->{"targetLanguage_".$i});
                        $taskModel->setTargetCountryCode($post->{"targetCountry_".$i});

                        if(isset($post->{"chunking_".$i})) { 
                            $taskModel->setTaskType(TaskTypeEnum::CHUNKING);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $createdChunkTask = $taskDao->createTask($taskModel);
                            try {                    
                                $filedata = file_get_contents($_FILES[$field_name]['tmp_name']);
                                $error_message = $taskDao->saveTaskFile($createdChunkTask->getId(), urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            } 
                        }
                        if(isset($post->{"translation_".$i})) {
                            $taskModel->setTaskType(TaskTypeEnum::TRANSLATION);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $newTask = $taskDao->createTask($taskModel);
                            $translationTaskId = $newTask->getId();
                            
                            try {                    
                                $filedata = file_get_contents($_FILES[$field_name]['tmp_name']);
                                $error_message = $taskDao->saveTaskFile($translationTaskId, urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            } 
                        }
                        if(isset($post->{"proofreading_".$i})) {
                            $taskModel->setTaskType(TaskTypeEnum::PROOFREADING);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $newTask = $taskDao->createTask($taskModel);
                            $proofreadingTaskId = $newTask->getId();
                            if(isset($post->{'translation_'.$i})) {
                                $taskDao->addTaskPreReq($proofreadingTaskId, $translationTaskId);
                            } 
                            
                            try {                    
                                $filedata = file_get_contents($_FILES[$field_name]['tmp_name']);                    
                                $error_message = $taskDao->saveTaskFile($proofreadingTaskId, urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            } 
                        }                       
                        if(isset($post->{"postediting_".$i})) {
                            $taskModel->setTaskType(TaskTypeEnum::POSTEDITING);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $newTask = $taskDao->createTask($taskModel);
                            $posteditingTaskId = $newTask->getId();
                            if(isset($post->{'translation_'.$i}) && isset($post->{'proofreading_'.$i})) {
                                $taskDao->addTaskPreReq($posteditingTaskId, $proofreadingTaskId);
                            } else if(isset($post->{'translation_'.$i})) {
                                $taskDao->addTaskPreReq($posteditingTaskId, $translationTaskId);
                            }
                            
                            try {                    
                                $filedata = file_get_contents($_FILES[$field_name]['tmp_name']);
                                $error_message = $taskDao->saveTaskFile($posteditingTaskId, urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            } 
                        }                       
                    } 
                    $app->redirect($app->urlFor("project-created", array("project_id" => $project->getId())));
                }              
            } else {                 
                $app->view()->appendData(array(
                    "title_err"             => $title_err,
                    "deadline_err"          => $deadline_err,                   
                    "targetLanguage_err"    => $targetLanguage_err,
                    "project"               => $project,
                    "file_upload_err"       => $file_upload_err
                ));               
            }
        }

        $extra_scripts = "
            <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$app->urlFor("home")}resources/css/datepickr.css\" />
            <script type=\"text/javascript\" src=\"{$app->urlFor("home")}resources/bootstrap/js/datepickr.js\"></script>
            <script type=\"text/javascript\">
                window.onload = function() {
                    new datepickr(\"deadline\");
                };
            </script>".file_get_contents("http://".$_SERVER["HTTP_HOST"]."{$app->urlFor("home")}ui/js/project-create.js");
  
        $langDao = new LanguageDao();
        $countryDao = new CountryDao();
        $language_list = $langDao->getLanguage(null);
        $countries = $countryDao->getCountry(null);

        $app->view()->appendData(array(
            "tagList"           => $tags,
            "max_file_size_bytes"   => TemplateHelper::maxFileSizeBytes(),
            "field_name"        => $field_name,
            "error"             => $error,
            "title_error"       => $title_err,
            "word_count_err"    => $word_count_err,
            "url_project_upload" => $app->urlFor("project-create", array("org_id" => $org_id)),
            "languages"         => $language_list,
            "countries"         => $countries,
            "extra_scripts"     => $extra_scripts
        ));
        
        $app->render("project.create.tpl");
    }    
    
    public function projectCreated($project_id)
    {
        $app = Slim::getInstance();
        $projectDao = new ProjectDao();
        $userDao = new UserDao();

        $user_id = UserSession::getCurrentUserID();
        $project = $projectDao->getProject(array('id' => $project_id));
        $user = $userDao->getUser(array('id' => $user_id));        
        
        if (!is_object($user)) {
            $app->flash("error", "Login required to access page.");
            $app->redirect($app->urlFor("login"));
        }   
        
        $org_id = $project->getOrganisationId();

        $app->view()->appendData(array(
                "org_id" => $org_id,
                "project_id" => $project_id
        ));     
        
        $app->render("project.created.tpl");
    }    
    
    public function archiveProject($project_id)
    {
        $app = Slim::getInstance();
        $projectDao = new ProjectDao();

        $project = $projectDao->getProject(array('id' => $project_id));
        if (!is_object($project)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }   
        $user_id = UserSession::getCurrentUserID();
        
        if (is_null($user_id)) {
            $app->flash("error", "Login required to access page.");
            $app->redirect($app->urlFor("login"));
        }   

        $projectDao->archiveProject($project_id, $user_id);
        $app->redirect($ref = $app->request()->getReferrer());
    }    
    
}
