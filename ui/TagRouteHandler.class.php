<?php

class TagRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();

        $app->get('/all/tags', array($this, 'tagsList'))->name('tags-list');

        $app->get("/tag/:label/:subscribe", array($this, 'tagSubscribe')
        )->name('tag-subscribe');
        
        $app->get('/tag/:label/', array($this, 'tagDetails')
        )->via("POST")->name('tag-details');
    }

    public function tagsList()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $user_id = UserSession::getCurrentUserID();
        $user_tags = array();
        $request = APIClient::API_VERSION."/users/$user_id/tags";
        $response = $client->call($request);
        foreach($response as $stdObject) {
            $user_tags[] = $client->cast('Tag', $stdObject);
        }

        $all_tags = array();
        $request = APIClient::API_VERSION."/tags";
        $response = $client->call($request);
        foreach($response as $stdObject) {
            $all_tags[] = $client->cast('Tag', $stdObject);
        }
        
        $app->view()->appendData(array(
            'user_tags' => $user_tags,
            'all_tags' => $all_tags
        )); 
        
        $app->render('tag-list.tpl');
    }

    public function tagSubscribe($label, $subscribe)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        // /v0/tags/getByLable/:label/
        //$request = APIClient::API_VERSION."/tags/getByLabel/$label";
        //$response = $client->call($request);
        //$tag = $client->cast('Tag', $response);
        $tag_dao = new TagsDao();
        $tag = $tag_dao->find(array('label' => $label));        //wait for API support
        
        $user_id = UserSession::getCurrentUserID();
        $request = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($request);
        $current_user = $client->cast('User', $response);
        $user_dao = new UserDao();

        if (!is_object($current_user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   
        
        $tag_id = $tag->getTagId();
        $displayName = $current_user->getDisplayName();
        
        if($subscribe == "true") {
            // /v0/users/:id/tags/:tagId/
            //$request = APIClient::API_VERSION."/users/$user_id/tags/$tag_id";
            //$response = $client->call($request, HTTP_Request2::METHOD_POST);            
            
            if(($user_dao->likeTag($user_id, $tag_id))) {       //wait for API support
                // put /v0/users/{id}/tags/{tagId}
                $request = APIClient::API_VERSION."/users/$user_id/tags/$tag_id";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT);                
                
                $app->flash('success', "Successfully added tag, $label, to subscription list");
            } else {
                $app->flash('error', "Unable to save tag, $label, for user $displayName");
            }   
        }   
        
        if($subscribe == "false") {
            if(($user_dao->removeTag($user_id, $tag_id))) {     //wait or API support
                $app->flash('success', "Successfully removed tag $label for user $displayName");
            } else {
                $app->flash('error', "Unable to remove tag $label for user $displayName");
            }
        }   
        
        $app->response()->redirect($app->request()->getReferer());
    }

    public function tagDetails($label)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $task_dao = new TaskDao;
        $tag_id = $task_dao->getTagId($label);      //wait for API support
        
        if (is_null($tag_id)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        //wait for API POST data support
        /*$tasks = array();
        $request = APIClient::API_VERSION."/tags/$tag_id/tasks";
        $post_data = array('limit' => 10);
        $response = $client->call($request, HTTP_Request2::METHDO_POST, $post_data);
        foreach($response as $stdObject) {
            $tasks[] = $client->cast('Task', $stdObject);
        }*/
        
        if ($tasks = TaskStream::getTaggedStream($label, 10)) {
            $app->view()->setData('tasks', $tasks);
        }   
        
        if (UserDao::isLoggedIn()) {        //wait for API support
            $user_dao = new UserDao();
            $user_id = UserSession::getCurrentUserID();
        
            $app->view()->appendData(array(
                    'user_id' => $user_id
            ));

            $user_tags = array();
            $request = APIClient::API_VERSION."/users/$user_id/tags";
            $response = $client->call($request);
            
            if($response) {
                foreach($response as $stdObject) {
                    $user_tags[] = $client->cast('Tag', $stdObject);
                }
                if(count($user_tags) > 0) {
                    $app->view()->appendData(array(
                            'user_tags' => $user_tags

                    )); 
                    foreach($user_tags as $tag) {
                        if($label == $tag->getLabel()) {
                            $app->view()->appendData(array(
                               'subscribed' => true
                            )); 
                        }
                    }
                }
            }
        }

        //wait for POST support in API
        /*$top_tags = array();
        $request = APIClient::API_VERSION."/tags";
        $post_data = array('limit' => 30);
        $response = $client->call($request, HTTP_Request2::METHOD_POST, $post_data);
        foreach($response as $stdObject) {
            $top_tags[] = $client->cast('Tag', $stdObject);
        }*/

        
        $app->view()->appendData(array(
                 'tag' => $label,
                 'top_tags' => TagsDao::getTopTags(30),
        )); 
        $app->render('tag.tpl');
    }
}
