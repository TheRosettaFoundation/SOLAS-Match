<?php

require_once 'Common/TaskTypeEnum.php';
require_once 'Common/TaskStatusEnum.php';

class ProjectRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();     
        
        $app->get('/project/view/:project_id/', array($middleware, 'authUserForOrgProject'),
        array($this, 'projectView'))->via("POST")->name('project-view');
        
        $app->get('/project/alter/:project_id/', array($middleware, 'authUserForOrgProject'), 
        array($this, 'projectAlter'))->via('POST')->name('project-alter');
        
        $app->get('/project/upload/:org_id/', array($middleware, 'authUserForOrg'),
        array($this, 'projectUpload'))->via('GET', 'POST')->name('project-upload');    
        
        $app->get('/project/id/:project_id/uploaded/', array($middleware, 'authUserForOrgProject'),
        array($this, 'projectUploaded'))->name('project-uploaded');
        
        $app->get('/project/id/:project_id/mark-archived/', array($middleware, 'authUserForOrgProject'),
        array($this, 'archiveProject'))->name('archive-project');
    }
  
    public function projectView($project_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID();

        $request = APIClient::API_VERSION."/projects/$project_id";
        $response = $client->call($request);     
        $project = $client->cast('Project', $response);        
        $app->view()->setData('project', $project);
         
        if ($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->publishedTask) && isset($post->task_id)) {
                
                $request = APIClient::API_VERSION."/tasks/{$post->task_id}";
                $response = $client->call($request);     
                $task = $client->cast('Task', $response);        
                
                if($post->publishedTask) {                     
                    $task->setPublished(1);                    
                    $request = APIClient::API_VERSION."/tasks/{$post->task_id}";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task);                 
                } else {
                    $task->setPublished(0);                    
                    $request = APIClient::API_VERSION."/tasks/{$post->task_id}";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT, $task);      
                }
            }
            
            if (isset($post->trackProject)) {
                if ($post->trackProject) {
                    $request = APIClient::API_VERSION."/users/$user_id/projects/{$project->getId()}";
                    $userTrackProject = $client->call($request, HTTP_Request2::METHOD_PUT);
                    
                    if ($userTrackProject) {
                        $app->flashNow("Success", 
                                "You are now tracking this Project and will receive email notifications
                                when its status changes.");
                    } else {
                        $app->flashNow("error", "Unable to register for notifications for this Project.");
                    }   
                } else {

                    $request = APIClient::API_VERSION."/users/$user_id/projects/{$project->getId()}";
                    $userUntrackProject = $client->call($request, HTTP_Request2::METHOD_DELETE);
                    
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
                $task_id = $post->task_id;
                $request = APIClient::API_VERSION."/tasks/$task_id";
                $response = $client->call($request);
                if($response) {
                    $task = $client->cast("Task", $response);
                }

                if($task && $task->getTitle() != '') {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task ".$task->getId();
                }

                if(!$post->trackTask) {
                    $request = APIClient::API_VERSION."/users/$user_id/tracked_tasks/$task_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_DELETE);                    
                    
                    if ($response) {
                        $app->flashNow('success', 'No longer receiving notifications from '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to unsubscribe from '.$task_title.'\'s notifications');
                    }
                } else {
                    $request = APIClient::API_VERSION."/users/$user_id/tracked_tasks/$task_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT);     
                    
                    if ($response) {
                        $app->flashNow('success', 'You will now receive notifications for '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to subscribe to '.$task_title.'.');
                    }
                }
            }
        }   

        $request = APIClient::API_VERSION."/orgs/{$project->getOrganisationId()}";
        $response = $client->call($request);     
        $org = $client->cast('Organisation', $response);
        
        $request = APIClient::API_VERSION."/users/subscribedToProject/$user_id/$project_id";
        $userSubscribedToProject = $client->call($request);
        
        $project_tasks = array();
        $taskMetaData = array();
        $request = APIClient::API_VERSION."/projects/{$project_id}/tasks";
        $response = $client->call($request);
        if($response) {
            foreach($response as $row) {
                $task = $client->cast("Task", $row);

                if(is_object($task)) {
                    $project_tasks[] = $task;
                    $task_id = $task->getId();

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
            }
        }
        
        $settings = new Settings();
        $numTaskTypes = $settings->get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = $settings->get("ui.task_{$i}_colour");
        }

        $app->view()->appendData(array(
                'org' => $org,
                'projectTasks' => $project_tasks,
                'taskMetaData' => $taskMetaData,
                'taskTypeColours' => $taskTypeColours,
                'userSubscribedToProject' => $userSubscribedToProject
        ));
        
        $app->render('project.view.tpl');
    }  
    
    public function projectAlter($project_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $wordCountError = '';
        $deadlineError = '';

        $request = APIClient::API_VERSION."/projects/$project_id";
        $response = $client->call($request);
        $project = $client->cast('Project', $response);

        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if ($post->title != '') {
                $project->setTitle($post->title);
            }

            if ($post->description != '') {
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

            if ($deadlineInMSecs != '' && $deadlineError == '') {
                $project->setDeadline(date("Y-m-d H:i:s", $deadlineInMSecs));
            }
            
            if ($post->sourceLanguage != '') {
                $project->setSourceLanguageCode($post->sourceLanguage);
            }
            
            if ($post->sourceCountry != '') {
                $project->setSourceCountryCode($post->sourceCountry);
            }   
             
            if ($post->reference != '' && $post->reference != 'http://') {
                $project->setReference($post->reference);
            }
            
            if ($post->tags != '') {
                $tags = TemplateHelper::separateTags($post->tags);
                foreach ($tags as $tag) {
                    $project->addTag($tag);
                }
            }

            if (ctype_digit($post->word_count)) {
                $project->setWordCount($post->word_count);                
            } else if ($post->word_count != '') {
                $wordCountError = "Word Count must be numeric";
            } else {
                $wordCountError = "Word Count cannot be blank";
            }
            
            if ($deadlineError == '' && $wordCountError == '') {
                $request = APIClient::API_VERSION."/projects/$project_id";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT, $project);
                $app->redirect($app->urlFor("project-view", array("project_id" => $project_id)));
            }
        }
         
        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
       
        $deadlineDate = date("F dS, Y", strtotime($project->getDeadline()));
        $deadlineTime = date("H:i", strtotime($project->getDeadline()));
        
        $tags = $project->getTagList();
        $tag_list = '';
        if ($tags != null) {
            foreach ($tags as $tag) {
                $tag_list .= $tag . ' ';
            }
        }

        $extra_scripts = "
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/datepickr.css\" />
        <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/datepickr.js\"></script>
        <script type=\"text/javascript\">
            window.onload = function() {
                new datepickr(\"deadlineDate\");
            };
        </script>";
        
        $app->view()->appendData(array(
                              'project'         => $project,
                              'deadlineDate'    => $deadlineDate,
                              'deadlineTime'    => $deadlineTime,
                              'languages'       => $languages,
                              'countries'       => $countries,
                              'tag_list'        => $tag_list,
                              'wordCountError'  => $wordCountError,
                              'deadlineError'   => $deadlineError,
                              'extra_scripts'   => $extra_scripts
        ));
        
        $app->render('project.alter.tpl');
    }
    
    
    public function projectUpload($org_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID(); 
        $settings = new Settings();

        $error          = null;
        $title_err      = null;
        $deadline_err   = null;
        $word_count_err = null;
        $targetLanguage_err = null;
        $project       = new Project();

        if ($app->request()->isPost()) {            
            $post = (object) $app->request()->post();
            
            if(($post->title != '')) {
                $project->setTitle($post->title);
            } else {
                $title_err = "Project <b>Title</b> must be set.";
            }            
            
            if($post->deadline != '') {
                $project->setDeadline($post->deadline);
            } else {
                $deadline_err = "Project <b>Deadline</b> must be set.";
            }
            
            if(($post->description != '')) {
                $project->setDescription($post->description);
            }
            if(($post->reference != '')) {
                $project->setReference($post->reference);
            }
            if(($post->word_count != '')) {
                $project->setWordCount($post->word_count);
            }
            if (isset($post->sourceLanguage) && $post->sourceLanguage != '') {
                $project->setSourceLanguageCode($post->sourceLanguage);
            }
            if ($post->sourceCountry != '') {
                $project->setSourceCountryCode($post->sourceCountry);
            }
            
            $tags = $post->tags;
            if (is_null($tags)) {
                $tags = '';
            }

            $tag_list = TemplateHelper::separateTags($tags);
            if($tag_list) {
                foreach ($tag_list as $tag) {
                    $project->addTag($tag);
                }
            } 
            
            for ($i=0; $i < $post->targetLanguageArraySize; $i++) {
                if(!isset($post->{'chunking_'.$i}) && !isset($post->{'translation_'.$i}) &&
                    !isset($post->{'proofreading_'.$i}) && !isset($post->{'postediting_'.$i})) {
                    $targetLanguage_err = "At least one <b>Task Type</b> must be set for each <b>Target Language</b>.";
                    break;
                }
            }
            
            if(is_null($title_err) && is_null($deadline_err) && is_null($targetLanguage_err)) { 
                $request = APIClient::API_VERSION."/projects";
                $project->setOrganisationId($org_id);
                if($response = $client->call($request, HTTP_Request2::METHOD_POST, $project)) {
                    $project = $client->cast('Project', $response);
                    
                    $taskModel = new Task();
                    $taskModel->setTitle($project->getTitle());
                    $taskModel->setSourceLanguageCode($project->getSourceLanguageCode());
                    $taskModel->setSourceCountryCode($project->getSourceCountryCode());
                    $taskModel->setProjectId($project->getId());


                    for ($i=0; $i < $post->targetLanguageArraySize; $i++) {

                        $taskModel->setTargetLanguageCode($post->{'targetLanguage_'.$i});
                        $taskModel->setTargetCountryCode($post->{'targetCountry_'.$i});

                        if(isset($post->{'chunking_'.$i})) { 
                            $taskModel->setTaskType(TaskTypeEnum::CHUNKING);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);
                        }
                        if(isset($post->{'translation_'.$i})) {
                            $taskModel->setTaskType(TaskTypeEnum::TRANSLATION);
                            $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);
                        }
                        if(isset($post->{'proofreading_'.$i})) {
                            $taskModel->setTaskType(TaskTypeEnum::PROOFREADING);      
                            
                            if($post->{'translation_'.$i}) {
                                $taskModel->setTaskStatus(TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                            } else {
                                $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            }

                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);
                        }                       
                        if(isset($post->{'postediting_'.$i})) {
                            $taskModel->setTaskType(TaskTypeEnum::POSTEDITING);
                            
                            if($post->{'proofreading_'.$i}) {
                                $taskModel->setTaskStatus(TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                            } else {
                                $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            }
                            
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);                         
                        }                       
                    } 
                    $app->redirect($app->urlFor("project-uploaded", array("project_id" => $project->getId())));
                }              
            } else {                 
                $app->view()->appendData(array(
                    'title_err'             => $title_err,
                    'deadline_err'          => $deadline_err,                   
                    'targetLanguage_err'    => $targetLanguage_err,
                    'project'               => $project
                ));               
            }
        }

        $extra_scripts = "
            <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/datepickr.css\" />
            <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/datepickr.js\"></script>
            <script type=\"text/javascript\">
                window.onload = function() {
                    new datepickr(\"deadline\");
                };
            </script>            
            <script language='javascript'>

                var fields = 0;
                var MAX_FIELDS =".$settings->get("site.max_target_languages")."; 
                var isRemoveButtonHidden = true;

                var isEnabledArray = new Array(false);

                function addNewTarget() {

                    if(isRemoveButtonHidden) {
                        document.getElementById('removeBottomTargetBtn').style.visibility = 'visible';
                        document.getElementById('removeBottomTargetBtn').disabled = false;
                        isRemoveButtonHidden = false;
                    }

                    if(fields < MAX_FIELDS) {

                        //var projectForm = document.getElementById('createProjectForm');
                        var table = document.getElementById('moreTargetLanguages');            
                        var newRow = document.createElement('tr');                        

                        newRow.setAttribute('id', \"newTargetLanguage\" + fields);
                        var newColumnLangCountry = document.createElement('td');
                        newColumnLangCountry.setAttribute('width', \"50%\");

                        var langs = document.getElementById('sourceLanguage').cloneNode(true);
                        langs.setAttribute('id', \"targetLanguage_\" + (fields + 1));
                        langs.setAttribute('name', \"targetLanguage_\" + (fields + 1));
                        newColumnLangCountry.appendChild(langs);

                        var countries = document.getElementById('sourceCountry').cloneNode(true);
                        countries.setAttribute('id', \"targetCountry_\" + (fields + 1));
                        countries.setAttribute('name', \"targetCountry_\" + (fields + 1));
                        newColumnLangCountry.appendChild(countries);
                        newRow.appendChild(newColumnLangCountry);

                        var tableColumnChunking = document.createElement('td');  
                        tableColumnChunking.setAttribute('align', 'middle');
                        tableColumnChunking.setAttribute('valign', \"top\");
                        var inputChunking = document.createElement('input');
                        inputChunking.setAttribute('type', \"checkbox\");
                        inputChunking.setAttribute('id', \"chunking_\" + (fields + 1));
                        inputChunking.setAttribute('name', \"chunking_\" + (fields + 1));
                        inputChunking.setAttribute('value', \"y\");
                        inputChunking.setAttribute('onchange', \"chunkingEnabled(\" + (fields + 1) +\")\");       
                        tableColumnChunking.appendChild(inputChunking);


                        var tableColumnTranslation = document.createElement('td');
                        tableColumnTranslation.setAttribute('align', 'middle'); 
                        tableColumnTranslation.setAttribute('valign', \"top\");
                        var inputTranslation = document.createElement('input');
                        inputTranslation.setAttribute('type', \"checkbox\");
                        inputTranslation.setAttribute('id', \"translation_\" + (fields + 1));
                        inputTranslation.setAttribute('checked', \"true\");
                        inputTranslation.setAttribute('name', \"translation_\" + (fields + 1))
                        inputTranslation.setAttribute('value', \"y\");
                        tableColumnTranslation.appendChild(inputTranslation);


                        var tableColumnReading = document.createElement('td');
                        tableColumnReading.setAttribute('align', 'middle');
                        tableColumnReading.setAttribute('valign', \"top\");
                        var inputProofReading = document.createElement('input');
                        inputProofReading.setAttribute('type', \"checkbox\");
                        inputProofReading.setAttribute('id', \"proofreading_\" + (fields + 1));
                        inputProofReading.setAttribute('name', \"proofreading_\" + (fields + 1));
                        inputProofReading.setAttribute('value', \"y\");
                        tableColumnReading.appendChild(inputProofReading);

                        var tableColumnPostEditing = document.createElement('td');
                        tableColumnPostEditing.setAttribute('align', 'middle');
                        tableColumnPostEditing.setAttribute('valign', \"top\");
                        var inputPostEditing = document.createElement('input');
                        inputPostEditing.setAttribute('type', \"checkbox\");
                        inputPostEditing.setAttribute('id', \"postediting_\" + (fields + 1));
                        inputPostEditing.setAttribute('name', \"postediting_\" + (fields + 1));
                        inputPostEditing.setAttribute('value', \"y\"); 
                        tableColumnPostEditing.appendChild(inputPostEditing);

                        newRow.appendChild(tableColumnChunking);
                        newRow.appendChild(tableColumnTranslation);
                        newRow.appendChild(tableColumnReading);
                        newRow.appendChild(tableColumnPostEditing);
                        table.appendChild(newRow);
                        isEnabledArray.push(false);

                        var size = document.getElementById('targetLanguageArraySize');
                        fields++;   
                        size.setAttribute('value', parseInt(size.getAttribute('value'))+1);        
                    }

                    if(fields == MAX_FIELDS) {
                        document.getElementById('alertinfo').style.display = 'block';
                        //document.getElementById('addMoreTargetsBtn').style.visibility = 'hidden';
                        document.getElementById('addMoreTargetsBtn').disabled = true;
                    }            
                } 


                function removeNewTarget() {    
                    var id = fields-1;  

                    var table = document.getElementById('moreTargetLanguages');
                    var tableRow = document.getElementById('newTargetLanguage' + id);
                    table.removeChild(tableRow);  
                    isEnabledArray.pop();

                    if(fields == MAX_FIELDS) {
                        //document.getElementById('addMoreTargetsBtn').style.visibility = 'visible';
                        document.getElementById('addMoreTargetsBtn').disabled = false;
                        document.getElementById('alertinfo').style.display = 'none';
                    }

                    var size = document.getElementById('targetLanguageArraySize');
                    fields--;
                    size.setAttribute('value', parseInt(size.getAttribute('value'))-1);

                    if(fields == 0) {
                        document.getElementById('removeBottomTargetBtn').style.visibility = 'hidden';
                        document.getElementById('removeBottomTargetBtn').disabled = true;
                        isRemoveButtonHidden = true;
                    }         
                }

                function chunkingEnabled(index)
                {
                    if(!isEnabledArray[index]) {
                        document.getElementById(\"translation_\" + index).checked = false;
                        document.getElementById(\"proofreading_\" + index).checked = false;
                        document.getElementById(\"postediting_\" + index).checked = false;        
                        document.getElementById(\"translation_\" + index).disabled = true;
                        document.getElementById(\"proofreading_\" + index).disabled = true;
                        document.getElementById(\"postediting_\" + index).disabled = true;    
                        isEnabledArray[index] = true;
                    } else {
                        document.getElementById(\"translation_\" + index).disabled = false;
                        document.getElementById(\"proofreading_\" + index).disabled = false;
                        document.getElementById(\"postediting_\" + index).disabled = false;
                        document.getElementById(\"translation_\" + index).checked = true;
                        isEnabledArray[index] = false;
                    }
                }    
            </script>";
        
        $language_list = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();

        $app->view()->appendData(array(
            'error'             => $error,
            'title_error'       => $title_err,
            'word_count_err'    => $word_count_err,
            'url_project_upload' => $app->urlFor('project-upload', array('org_id' => $org_id)),
            'languages'         => $language_list,
            'countries'         => $countries,
            'extra_scripts'     => $extra_scripts
        ));
        
        $app->render('project.upload.tpl');
    }    
    
    
    public function projectUploaded($project_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID();

        $request = APIClient::API_VERSION."/projects/$project_id";
        $response = $client->call($request);     
        $project = $client->cast('Project', $response);
       
        $request = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($request, HTTP_Request2::METHOD_GET);
        $user = $client->cast('User', $response);        
        
        if (!is_object($user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   
        
        $org_id = $project->getOrganisationId();

        $app->view()->appendData(array(
                'org_id' => $org_id,
                'project_id' => $project_id
        ));     
        
        $app->render('project.uploaded.tpl');
    }    
    
    public function archiveProject($project_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/projects/$project_id";
        $response = $client->call($request);
        $project = $client->cast('Project', $response);
        
        if (!is_object($project)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }   
        $user_id = UserSession::getCurrentUserID();
        
        if (is_null($user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   

        $request = APIClient::API_VERSION."/projects/archiveProject/$project_id/user/$user_id";
        $response = $client->call($request, HTTP_Request2::METHOD_PUT);        
        
        $app->redirect($ref = $app->request()->getReferrer());
    }    
    
}
