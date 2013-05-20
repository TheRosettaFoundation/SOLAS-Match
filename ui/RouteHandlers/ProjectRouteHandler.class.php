<?php

require_once __DIR__."/../../Common/TaskTypeEnum.php";
require_once __DIR__."/../../Common/TaskStatusEnum.php";

class ProjectRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();     
        
        $app->get("/project/:project_id/view", array($middleware, "authUserIsLoggedIn")
        , array($this, "projectView"))->via("POST")->name("project-view");
        
        $app->get("/project/:project_id/alter", array($middleware, "authUserForOrgProject")
        , array($this, "projectAlter"))->via("POST")->name("project-alter");
        
        $app->get("/project/:org_id/create", array($middleware, "authUserForOrg")
        , array($this, "projectCreate"))->via("GET", "POST")->name("project-create");    
        
        $app->get("/project/id/:project_id/created", array($middleware, "authUserForOrgProject")
        , array($this, "projectCreated"))->name("project-created");
        
        $app->get("/project/id/:project_id/mark-archived", array($middleware, "authUserForOrgProject")
        , array($this, "archiveProject"))->name("archive-project");

        $app->get("/project/:project_id/file", array($middleware, "authUserIsLoggedIn")
        , array($this, "downloadProjectFile"))->name("download-project-file");

        $app->get("/project/:project_id/test", array($this, "test"));
    }

    public function test($projectId)
    {
        $app = Slim::getInstance();
        $extra_scripts = "";

        $time = microtime();
        $time = explode(" ", $time);
        $time = $time[1] + $time[0];
        $time1 = $time; 

        $projectDao = new ProjectDao();
        $graph = $projectDao->getProjectGraph($projectId);
        $viewer = new GraphViewer($graph);
        $body = $viewer->constructView();

        $extra_scripts .= $viewer->generateDataScript();
        $extra_scripts .= "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}ui/js/GraphHelper.js\"></script>";
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
        $app = Slim::getInstance();
        $user_id = UserSession::getCurrentUserID();
        $projectDao = new ProjectDao();
        $taskDao = new TaskDao();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $project = $projectDao->getProject($project_id);        
        $app->view()->setData("project", $project);
         
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
           
            $task = null;
            if(isset($post->task_id)) {
                $task = $taskDao->getTask($post->task_id);
            } elseif (isset($post->revokeTaskId)) {
                $task = $taskDao->getTask($post->revokeTaskId);
            }
            
            if(isset($post->publishedTask) && isset($post->task_id)) { 
                if($post->publishedTask) {                     
                    $task->setPublished(true);
                } else {
                    $task->setPublished(false);                    
                }
                $taskDao->updateTask($task);
            }
            
            if (isset($post->trackProject)) {
                if ($post->trackProject) {
                    $userTrackProject = $userDao->trackProject($user_id, $project->getId());
                    if ($userTrackProject) {
                        $app->flashNow("success", 
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
                    $response = $userDao->trackTask($user_id, $post->task_id);
                    if ($response) {
                        $app->flashNow("success", "You will now receive notifications for $task_title.");
                    } else {
                        $app->flashNow("error", "Unable to subscribe to $task_title.");
                    }
                }
            }

            if (isset($post->deleteTask)) {
                $taskDao->deleteTask($post->task_id);
                $app->flashNow("success", "The task \"{$task->getTitle()}\" has been deleted");
            }

            if (isset($post->archiveTask)) {
                $taskDao->archiveTask($post->task_id, $user_id);
                $app->flashNow("success", "The task \"{$task->getTitle()}\" has been archived");
            }
        }   

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $project_tags = $projectDao->getProjectTags($project_id);
        $isOrgMember = $orgDao->isMember($project->getOrganisationId(), $user_id);
        
        $adminDao = new AdminDao();
        $isAdmin = $adminDao->isOrgAdmin($user_id, $project->getOrganisationId()) || $adminDao->isSiteAdmin($user_id);
        
        if($isOrgMember || $isAdmin) {
            $userSubscribedToProject = $userDao->isSubscribedToProject($user_id, $project_id);
            $taskMetaData = array();
            $project_tasks = $projectDao->getProjectTasks($project_id);
            $taskLanguageMap = array();
            if($project_tasks) {
                foreach($project_tasks as $task) {      
                    $targetLocale = $task->getTargetLocale();
                    $taskTargetLanguage = $targetLocale->getLanguageCode();
                    $taskTargetCountry = $targetLocale->getCountryCode();
                    $taskLanguageMap["$taskTargetLanguage,$taskTargetCountry"][] = $task;
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
            }

            $graph = $projectDao->getProjectGraph($project_id);
            $viewer = new GraphViewer($graph);
            $graphView = $viewer->constructView();

            $extra_scripts = "";
            $extra_scripts .= $viewer->generateDataScript();
            $extra_scripts .= file_get_contents(__DIR__."/../js/GraphHelper.js");
            $extra_scripts .= file_get_contents(__DIR__."/../js/project-view.js");

            $numTaskTypes = Settings::get("ui.task_types");
            $taskTypeColours = array();

            for($i=1; $i <= $numTaskTypes; $i++) {
                $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
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
                    "isOrgMember"   => $isOrgMember,
                    "taskLanguageMap" => $taskLanguageMap
            ));
            
        } else {   

            $app->view()->appendData(array(
                    "org" => $org,
                    "project_tags" => $project_tags
            ));
        }
        
        $app->render("project/project.view.tpl");
    }  
    
    public function projectAlter($project_id)
    {
        $app = Slim::getInstance();
        $deadlineError = '';
        $projectDao = new ProjectDao();

        $project = $projectDao->getProject($project_id);
        if(isValidPost($app)) {
            $post = $app->request()->post();
            
            if(isset($post['title'])) $project->setTitle($post['title']);
            if(isset($post['description'])) $project->setDescription($post['description']);            
            if(isset($post['impact'])) $project->setImpact($post['impact']);           

            if(isset($post['deadline'])) {                
                if ($validTime = TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);  
                    $project->setDeadline($date);
                } else {
                    $deadlineError = "Invalid date/time format!";
                }
            }
            
            $sourceLocale = new Locale();
            if(isset($post['sourceLanguage'])) $sourceLocale->setLanguageCode($post['sourceLanguage']); 
            if(isset($post['sourceCountry'])) $sourceLocale->setCountryCode($post['sourceCountry']);              
            if(isset($post['reference']) && $post['reference'] != "http://") $project->setReference($post['reference']);
            $project->setSourceLocale($sourceLocale);
                        
            if(isset($post['tags'])) {
                $tagLabels = TemplateHelper::separateTags($post['tags']);
                if($tagLabels) {
                    foreach ($tagLabels as $tagLabel) {
                        $newTag = new Tag();
                        $newTag->setLabel($tagLabel);
                        $project->addTag($newTag);
                    }                   
                }
            }
            
            if($deadlineError == '') {
                $projectDao->updateProject($project);
                $app->redirect($app->urlFor("project-view", array("project_id" => $project_id)));
            }
        }
         
        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
        
        $tags = $project->getTagList();
        $tag_list = "";
        if ($tags != null) {
            foreach ($tags as $tag) {
                $tag_list .= $tag->getLabel() . " ";
            }
        }

        $tagList = "[";
        $tagDao = new TagDao();
        $tags = $tagDao->getTags(null);
        if ($tags) {
            foreach ($tags as $tag) {
                $tagList .= "\"{$tag->getLabel()}\", ";
            }
        }
        $tagList = substr($tagList, 0, strlen($tagList) - 2);
        $tagList .= "]";

        $extra_scripts = "
            <script type=\"text/javascript\">".file_get_contents(__DIR__."/../js/lib/jquery-ui-timepicker-addon.js")."</script>"
            .file_get_contents(__DIR__."/../js/datetime-picker.js")."
            <script type=\"text/javascript\">
                var tagList = $tagList;
            </script>"
            .file_get_contents(__DIR__."/../js/tags-autocomplete.js");
        
        $app->view()->appendData(array(
                              "project"         => $project,
                              "languages"       => $languages,
                              "countries"       => $countries,
                              "tag_list"        => $tag_list,
                              "deadlineError"   => $deadlineError,
                              "extra_scripts"   => $extra_scripts
        ));
        
        $app->render("project/project.alter.tpl");
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
        $description_err= null;
        $impact_err     = null;
        $targetLanguage_err = null;
        $wordcount_err = null;
        $uniqueLanguageCountry_err = null;
        $project = new Project();

        if($post = $app->request()->post()) {   
            
            $tagDao = new TagDao();
            
            if(isset($post['title']) && $post['title'] != '') {
                $project->setTitle($post['title']);
            } else {
                $title_err = "Project <b>Title</b> must be set.";
            }            
            
            if(isset($post['deadline'])) {
                if($validTime = TemplateHelper::isValidDateTime($post['deadline'])) {
                    $date = date("Y-m-d H:i:s", $validTime);  
                    $project->setDeadline($date);
                } else {
                    $deadline_err = "Invalid date/time format!";
                }                
            } else {
                $deadline_err = "Project <b>Deadline</b> must be set.";
            }
            
            if(isset($post['description']) && $post['description'] != '') {
                $project->setDescription($post['description']);
            } else {
                $description_err = "Project <b>Description</b> must be set.";
            }
            
            if(isset($post['impact']) && $post['impact'] != '') {
                $project->setImpact($post['impact']);
            } else {
                $impact_err = "Project <b>Impact</b> must be set.";
            }
            
            if(isset($post['reference']) && $post['reference'] != '') $project->setReference($post['reference']);
            
            $cleansedWordCount = str_replace(",", "", $post['word_count']);
            if(!is_null($cleansedWordCount) && ctype_digit($cleansedWordCount) && $cleansedWordCount > 0) {                
                $project->setWordCount($cleansedWordCount);
            } else {
                $wordcount_err = "Project <b>Word Count</b> must be set and be a valid <b>natural</b> number.";
            }
            
            $sourceLocale = new Locale();
            if(isset($post['sourceLanguage'])) $sourceLocale->setLanguageCode($post['sourceLanguage']);
            if(isset($post['sourceCountry'])) $sourceLocale->setCountryCode($post['sourceCountry']);            
            if(isset($post['sourceLanguage']) && isset($post['sourceCountry'])) $project->setSourceLocale($sourceLocale);
            
            $tags = $post['tags'];
            if (is_null($tags)) {
                $tags = "";
            }

            $tagLabels = TemplateHelper::separateTags($tags);
            if($tagLabels) {
                foreach ($tagLabels as $tagLabel) {
                    $newTag = new Tag();
                    $newTag->setLabel($tagLabel);
                    $project->addTag($newTag);
                }                   
            }
                        
            $targetLanguageCountryArray = array();
            for ($i=0; $i < $post['targetLanguageArraySize']; $i++) {                  
                $key = $post["targetLanguage_$i"];
                if(!array_key_exists($key, $targetLanguageCountryArray)) {
                        $targetLanguageCountryArray[$key] = $post["targetCountry_$i"];
                } else {
                    $uniqueLanguageCountry_err = "Each new <b>Target Language pair</b> added must be a <b>unique pair</b>.";
                    break;
                }
            }
            
            for ($i=0; $i < $post['targetLanguageArraySize']; $i++) {  
                if(!isset($post["segmentation_$i"]) && !isset($post["translation_$i"]) &&
                    !isset($post["proofreading_$i"])) {
                    $targetLanguage_err = "At least one <b>Task Type</b> must be set for each <b>Target Language</b>.";
                    break;
                }
            }
            
            $upload_error = null;
            $file_upload_err = null;
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($field_name);
            } catch (Exception $e) {
                $upload_error = true;
                $file_upload_err = $e->getMessage();
            }
            

            if(is_null($title_err) && is_null($deadline_err) && is_null($targetLanguage_err) && is_null($upload_error)
                && is_null($impact_err)&& is_null($uniqueLanguageCountry_err) && is_null(($wordcount_err))) { 
                
                $project->setOrganisationId($org_id);
                if($project = $projectDao->createProject($project)) {
                    $filedata = file_get_contents($_FILES[$field_name]['tmp_name']);
                    $filename = $_FILES[$field_name]["name"];
                    $projectDao->saveProjectFile($project->getId(), $filedata, $filename, $user_id);
                    
                    $taskModel = new Task(); 
                    $taskModel->setTitle($project->getTitle());
                    $taskModel->setProjectId($project->getId());
                    $taskModel->setDeadline($project->getDeadline());
                    $taskModel->setWordCount($project->getWordCount());
                    
                    $projectSourceLocale = $project->getSourceLocale();                    
                    $taskSourceLocale = new Locale();
                    $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
                    $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
                    $taskModel->setSourceLocale($taskSourceLocale);
                    
                    if(isset($post['publishTasks']) && $post['publishTasks']) {
                        $taskModel->setPublished(true);
                    } else {
                        $taskModel->setPublished(false);
                    }
                    
                    $translationTaskId = 0;
                    $proofreadingTaskId = 0;
                    
                    for ($i=0; $i < $post['targetLanguageArraySize']; $i++) {
                        
                        $targetLocale = new Locale();
                        $targetLocale->setLanguageCode($post["targetLanguage_$i"]);
                        $targetLocale->setCountryCode($post["targetCountry_$i"]);
                        $taskModel->setTargetLocale($targetLocale);

                        if(isset($post["segmentation_$i"])) { 
                            $taskModel->setTaskType(TaskTypeEnum::SEGMENTATION);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $createdSegmentationTask = $taskDao->createTask($taskModel);
                            try {
                                $error_message = $taskDao->saveTaskFile($createdSegmentationTask->getId(), urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            }
                        }
                        if(isset($post["translation_$i"])) {
                            $taskModel->setTaskType(TaskTypeEnum::TRANSLATION);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $newTask = $taskDao->createTask($taskModel);
                            $translationTaskId = $newTask->getId();
                            
                            try {
                                $error_message = $taskDao->saveTaskFile($translationTaskId, urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            } 
                        }
                        if(isset($post["proofreading_$i"])) {
                            $taskModel->setTaskType(TaskTypeEnum::PROOFREADING);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $newTask = $taskDao->createTask($taskModel);
                            $proofreadingTaskId = $newTask->getId();
                            if(isset($post["translation_$i"])) {
                                $taskDao->addTaskPreReq($proofreadingTaskId, $translationTaskId);
                            } 
                            
                            try {
                                $error_message = $taskDao->saveTaskFile($proofreadingTaskId, urlencode($_FILES[$field_name]['name']),
                                        $user_id, $filedata);
                            } catch (Exception  $e) {
                                $upload_error = true;
                                $error_message = "File error: " . $e->getMessage();
                            } 
                        }
                    } 
                    $projectDao->calculateProjectDeadlines($project->getId());
                    $app->redirect($app->urlFor("project-created", array("project_id" => $project->getId())));
                }              
            } else {     
                $project->setWordCount($post["word_count"]);
                $project->setDeadline($post["deadline"]);
                $app->view()->appendData(array(
                    "title_err"             => $title_err,
                    "deadline_err"          => $deadline_err,      
                    "wordcount_err"         => $wordcount_err,
                    "targetLanguage_err"    => $targetLanguage_err,
                    "project"               => $project,
                    "file_upload_err"       => $file_upload_err,
                    "uniqueLanguageCountry_err" => $uniqueLanguageCountry_err
                ));               
            }
        }

        $tagString = "[";
        $tagDao = new TagDao();
        $allTags = $tagDao->getTags(null);
        if ($allTags) {
            foreach ($allTags as $tag) {
                $tagString .= "\"{$tag->getLabel()}\", ";
            }
        }
        $tagString = substr($tagString, 0, strlen($tagString) - 2);
        $tagString .= "]";
        // todo
        $extra_scripts = "
            <script type=\"text/javascript\">".file_get_contents(__DIR__."/../js/lib/jquery-ui-timepicker-addon.js")."</script>
            ".file_get_contents(__DIR__."/../js/project-create.js")
            .file_get_contents(__DIR__."/../js/datetime-picker.js")
            ."
            <script type=\"text/javascript\">
                var tagList = $tagString;
            </script>"
            .file_get_contents(__DIR__."/../js/tags-autocomplete.js");

        $language_list = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();

        $app->view()->appendData(array(
            "tagList"           => $tags,
            "max_file_size_bytes"   => TemplateHelper::maxFileSizeBytes(),
            "field_name"        => $field_name,
            "error"             => $error,
            "title_error"       => $title_err,
            "word_count_err"    => $word_count_err,
            "description_err"   => $description_err,
            "impact_err"        => $impact_err,
            "url_project_upload" => $app->urlFor("project-create", array("org_id" => $org_id)),
            "languages"         => $language_list,
            "countries"         => $countries,
            "extra_scripts"     => $extra_scripts
        ));
        
        $app->render("project/project.create.tpl");
    }    
    
    public function projectCreated($project_id)
    {
        $app = Slim::getInstance();
        $projectDao = new ProjectDao();
        $userDao = new UserDao();

        $user_id = UserSession::getCurrentUserID();
        $project = $projectDao->getProject($project_id);
        $user = $userDao->getUser($user_id);        
        
        if (!is_object($user)) {
            $app->flash("error", "Login required to access page.");
            $app->redirect($app->urlFor("login"));
        }   
        
        $org_id = $project->getOrganisationId();

        $app->view()->appendData(array(
                "org_id" => $org_id,
                "project_id" => $project_id
        ));     
        
        $app->render("project/project.created.tpl");
    }    
    
    public function archiveProject($project_id)
    {
        $app = Slim::getInstance();
        $projectDao = new ProjectDao();

        $project = $projectDao->getProject($project_id);
        if (!is_object($project)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }   
        $user_id = UserSession::getCurrentUserID();
        
        if (is_null($user_id)) {
            $app->flash("error", "Login required to access page.");
            $app->redirect($app->urlFor("login"));
        }   

        $archivedProject = $projectDao->archiveProject($project_id, $user_id);     
        
        if($archivedProject) {            
            $app->flash("success", "You have successfully archived the project <b>{$project->getTitle()}</b>.");
        } else {
            $app->flash("error",  "There was an error archiving the project <b>{$project->getTitle()}</b>.");
        }       
        
        $app->redirect($ref = $app->request()->getReferrer());
    }    
    
    public function downloadProjectFile($projectId)
    {
        $app = Slim::getInstance();
        $siteApi = Settings::get("site.api");
        $app->redirect("{$siteApi}v0/projects/$projectId/file/");
    }
}

$route_handler = new ProjectRouteHandler();
$route_handler->init();
unset ($route_handler);
