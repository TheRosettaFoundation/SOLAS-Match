<?php

class ProjectRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();     
        
        $app->get('/project/view/:project_id/', array($middleware, 'authUserForOrgProject'),
        array($this, 'projectView'))->via("POST")->name('project-view');
        
        //$app->get('/project/view/:project_id/', array($this, 'projectView'))->name('project-view');  
        $app->get('/project/alter/:project_id/', array($middleware, 'authUserForOrgProject'), 
        array($this, 'projectAlter'))->via('POST')->name('project-alter');
        // $app->redirect($app->urlFor("project-view", array("project_id" => $project_id)));
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
            
            if (isset($post->notify) && $post->notify == "true") {
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
        }   
        
        $request = APIClient::API_VERSION."/users/subscribedToProject/{$user_id}/$project_id";
        $registered = $client->call($request);         

        $request = APIClient::API_VERSION."/orgs/{$project->getOrgId()}";
        $response = $client->call($request);     
        $org = $client->cast('Organisation', $response);

        $app->view()->appendData(array(
                                 'org' => $org,
                                 'registered' => $registered
        ));
 
 

        // Starts
        $my_organisations = array();
        $url = APIClient::API_VERSION."/users/$user_id/orgs";
        $response = $client->call($url);
        if (is_array($response)) {
            foreach ($response as $stdObject) {
                $my_organisations[] = $client->cast('Organisation', $stdObject);
            }
        }elseif(is_string ($response)){
            $my_organisations = $client->cast('Organisation', $response);
        }
        
        $org_tasks = array();
        $orgs = array();

        foreach ($my_organisations as $org) {

            $url = APIClient::API_VERSION."/orgs/{$org->getId()}/tasks";
            $org_tasks_data = $client->call($url);        
            $my_org_tasks = array();
            if ($org_tasks_data) {
                foreach ($org_tasks_data as $stdObject) {
                    $my_org_tasks[] = $client->cast('Task', $stdObject);
                }
            } else {
                // If no org tasks, set to null
                $my_org_tasks = null;
            }   
            
            $request = APIClient::API_VERSION."/tags/topTags";
            $response = $client->call($request, HTTP_Request2::METHOD_GET, null,
                                        array('limit' => 30));        
            $top_tags = array();
            if ($response) {
                foreach ($response as $stdObject) {
                    $top_tags[] = $client->cast('Tag', $stdObject);
                }
            }            

            $org_tasks[$org->getId()] = $my_org_tasks;
            $orgs[$org->getId()] = $org;
        }        
        
        if (count($org_tasks) > 0) {
            
            $templateData = array();
            foreach ($org_tasks as $org => $taskArray) {
                $taskData = array();
                if ($taskArray) {
                    foreach ($taskArray as $task) {
                        $temp = array();
                        $temp['task']=$task;
                        $temp['translated']=$client->call(APIClient::API_VERSION.
                                "/tasks/{$task->getId()}/version") > 0;
                                
                        $temp['taskClaimed']=$client->call(APIClient::API_VERSION.
                                "/tasks/{$task->getId()}/claimed") == 1;
                                
                        $temp['userSubscribedToTask']=$client->call(APIClient::API_VERSION.
                                "/users/subscribedToTask/".UserSession::getCurrentUserID()."/{$task->getId()}") == 1;
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org] = $taskData;
            }
            
            $app->view()->appendData(array(
                'orgs' => $orgs,
                'templateData' => $templateData
            ));
        }        
        
        
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
            
            if ($post->impact != '') {
                $project->setImpact($post->impact);
            }
            
            if ($post->reference != '' && $post->reference != 'http://') {
                $project->setReferencePage($post->reference);
            }
            
            if ($post->source != '') {
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
            }
            

            if (ctype_digit($post->word_count)) {
                
                $project->setWordCount($post->word_count);                
                $request = APIClient::API_VERSION."/tasks/$task_id";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT, $project);
                $app->redirect($app->urlFor("task-view", array("task_id" => $task_id)));
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
    
    
}
?>
