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
            
            if (isset($post->notify)) {
                if ($post->notify == "true") {
                    $request = APIClient::API_VERSION."/users/$user_id/tracked_projects/{$project->getId()}";
                    $userTrackProject = $client->call($request, HTTP_Request2::METHOD_PUT);
                    
                    if ($userTrackProject) {
                        $app->flashNow("success", 
                                "You are now tracking this Project and will receive email notifications
                                when its status changes.");
                    } else {
                        $app->flashNow("error", "Unable to register for notifications for this Project.");
                    }   
                } else {

                    $request = APIClient::API_VERSION."/users/$user_id/tracked_projects/{$project->getId()}";
                    $userIgnoreProject = $client->call($request, HTTP_Request2::METHOD_DELETE);
                    
                    if ($response) {
                        $app->flashNow("success", 
                                "You are no longer tracking this Project and will receive no
                                further emails."
                        );
                    } else {
                        $app->flashNow("error", "Unable to unregister for this notification.");
                    }   
                }
            } elseif(isset($post->track)) {
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

                if($post->track == "Ignore") {
                    $request = APIClient::API_VERSION."/users/$user_id/tracked_tasks/$task_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_DELETE);                    
                    
                    if ($response) {
                        $app->flashNow('success', 'No longer receiving notifications from '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to unsubscribe from '.$task_title.'\'s notifications');
                    }
                } elseif($post->track == "Track") {
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

        //Not Implemented
        $request = APIClient::API_VERSION."/users/subscribedToProject/{$user_id}/{$project_id}";
        $registered = $client->call($request);

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

        $app->view()->appendData(array(
                     'org' => $org,
                     'registered' => $registered,
                     'projectTasks' => $project_tasks,
                     'taskMetaData' => $taskMetaData
        ));
        
        $app->render('project.view.tpl');
    }  
    
    public function projectAlter($project_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $word_count_err = null;

        $request = APIClient::API_VERSION."/projects/$project_id";
        $response = $client->call($request);
        $project = $client->cast('Project', $response);
        
        $app->view()->setData('project', $project);
        
        if (isValidPost($app)) {
            $post = (object) $app->request()->post();
            
            if ($post->title != '') {
                $project->setTitle($post->title);
            }
            
            if ($post->reference != '' && $post->reference != 'http://') {
                $project->setReference($post->reference);
            }
            
            //Not in template yet
            /*if ($post->source != '') {
                $project->setSourceLangId($post->source);
            }
            
            if ($post->target != '') {
                $project->setTargetLangId($post->target);
            }   
             
            if ($post->sourceCountry != '') {
                $project->setSourceRegionId($post->sourceCountry);
            }   
             
            if ($post->targetCountry != '') {
                $project->setTargetRegionId($post->targetCountry);
            }   
              
            if ($post->tags != '') {
                $tags = TemplateHelper::separateTags($post->tags);
                foreach ($tags as $tag) {
                    $project->addTags($tag);
                }
            }*/
            

            if (ctype_digit($post->word_count)) {
                
                $project->setWordCount($post->word_count);                
                $request = APIClient::API_VERSION."/projects/$project_id";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT, $project);
                $app->redirect($app->urlFor("project-view", array("project_id" => $project_id)));
            } else if ($post->word_count != '') {
                $word_count_err = "Word Count must be numeric";
            } else {
                $word_count_err = "Word Count cannot be blank";
            }
            

        }
         
        $languages = TemplateHelper::getLanguageList();
        $countries = TemplateHelper::getCountryList();
       
        /*
        $tags = $project->getTags();
        $tag_list = '';
        if ($tags != null) {
            foreach ($tags as $tag) {
                $tag_list .= $tag . ' ';
            }
        }
        */
        $app->view()->appendData(array(
                              'languages'       => $languages,
                              'countries'       => $countries,
                              //'tag_list'        => $tag_list,
                              'word_count_err'  => $word_count_err,
        ));
        
        $app->render('project.alter.tpl');
    }
    
    
    public function projectUpload($org_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID(); 

        $error          = null;
        $title_err      = null;
        $deadline_err   = null;
        $word_count_err = null;
        $targetLanguage_err = null;
        $project            = null;
        $projectModel       = null;

        if ($app->request()->isPost()) {            
            $post = (object) $app->request()->post();
            
            $project = null;
            $projectModel = new Project();             
            
            if(($post->title != '')) {
                $projectModel->setTitle($post->title);
            } else {
                $title_err = "Project <b>Title</b> must be set.";
            }            
            
            if($post->deadline != '') {
                $projectModel->setDeadline($post->deadline);
            } else {
                $deadline_err = "Project <b>Deadline</b> must be set.";
            }
            
            if(($post->description != '')) {
                $projectModel->setDescription($post->description);
            }
            if(($post->reference != '')) {
                $projectModel->setReference($post->reference);
            }
            if(($post->word_count != '')) {
                $projectModel->setWordCount($post->word_count);
            }
            
            $tags = $post->tags;
            if (is_null($tags)) {
                $tags = '';
            }

            $tag_list = TemplateHelper::separateTags($tags);
            if($tag_list) {
                foreach ($tag_list as $tag) {
                    $projectModel->addTags($tag);
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
                $projectModel->setOrganisationId($org_id);
                if($response = $client->call($request, HTTP_Request2::METHOD_POST, $projectModel)) {
                    $project = $client->cast('Project', $response);
                    
                    $taskModel = new Task();
                    $taskModel->setTitle($projectModel->getTitle());
                    $taskModel->setSourceLanguageCode($post->sourceLanguage);
                    $taskModel->setSourceCountryCode($post->sourceCountry);
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
                            
                            if($post->{'proofreading'.$i}) {
                                $taskModel->setTaskStatus(TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                            } else {
                                $taskModel->setTaskStatus(TaskStatusEnum::PENDING_CLAIM);
                            }
                            
                            $request = APIClient::API_VERSION."/tasks";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST, $taskModel);                         
                        }                       
                    }           
                }              
            } else {                 
                $app->view()->appendData(array(
                    'title_err'             => $title_err,
                    'deadline_err'          => $deadline_err,                   
                    'targetLanguage_err'    => $targetLanguage_err,
                    'projectModel'          => $projectModel
                ));               
            }
        }
            
            
            /*             
            $upload_error = false;
            try {
                TemplateHelper::validateFileHasBeenSuccessfullyUploaded($field_name);
            } catch (Exception $e) {
                $upload_error = true;
                $error_message = $e->getMessage();
            }
            
            if (!$upload_error) {
                
                $projectData = array();
                $taskData['organisation_id'] = $org_id;
                $taskData['title'] = $_FILES[$field_name]['name'];
                $task = ModelFactory::buildModel("Task", $taskData);
                
                $request = APIClient::API_VERSION."/tasks";
                $response = $client->call($request, HTTP_Request2::METHOD_POST, $task);
                
                $task = $client->cast('Task', $response);

                try {                    
                    $filedata =file_get_contents($_FILES[$field_name]['tmp_name']);                    
                    $error_message=$client->call(APIClient::API_VERSION."/tasks/{$task->getId()}/file/".
                            urlencode($_FILES[$field_name]['name'])."/$user_id",
                            HTTP_Request2::METHOD_PUT, null, null, "", $filedata);
                } catch (Exception  $e) {
                    $upload_error = true;
                    $error_message = 'File error: ' . $e->getMessage();
                }
            }
            
            if (!$upload_error) {
                $app->redirect($app->urlFor('task-describe', array('task_id' => $task->getId())));
            }
        } 
        */

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
                var MAX_FIELDS = 10; 
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
                'org_id' => $org_id
        ));     
        
        $app->render('project.uploaded.tpl');
    }    
}
