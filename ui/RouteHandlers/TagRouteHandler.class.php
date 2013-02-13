<?php

class TagRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();

        $app->get("/all/tags", array($this, "tagsList"))->name("tags-list");

        $app->get("/tag/:label/:subscribe", array($this, "tagSubscribe")
        )->name("tag-subscribe");
        
        $app->get("/tag/:label/", array($this, "tagDetails")
        )->via("POST")->name("tag-details");
    }

    public function tagsList()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $user_id = UserSession::getCurrentUserID();
        $user_tags = array();
        $request = APIClient::API_VERSION."/users/$user_id/tags";
        $response = $client->call($request);
        foreach ($response as $stdObject) {
            $user_tags[] = $client->cast("Tag", $stdObject);
        }

        $all_tags = array();
        $request = APIClient::API_VERSION."/tags";
        $response = $client->call($request);
        foreach ($response as $stdObject) {
            $all_tags[] = $client->cast("Tag", $stdObject);
        }
        
        $app->view()->appendData(array(
            "user_tags" => $user_tags,
            "all_tags" => $all_tags
        )); 
        
        $app->render("tag-list.tpl");
    }

    public function tagSubscribe($label, $subscribe)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tags/getByLabel/$label";
        $response = $client->call($request);
        $tag = $client->cast("Tag", $response);
        
        $user_id = UserSession::getCurrentUserID();
        $request = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($request);
        $current_user = $client->cast("User", $response);

        if (!is_object($current_user)) {
            $app->flash("error", "Login required to access page");
            $app->redirect($app->urlFor("login"));
        }   
        
        $tag_id = $tag->getId();
        $displayName = $current_user->getDisplayName();
        
        if ($subscribe == "true") {
            $request = APIClient::API_VERSION."/users/$user_id/tags/$tag_id";
            $userLikeTag = $client->call($request, HTTP_Request2::METHOD_PUT);            
            
            if ($userLikeTag) {
                $request = APIClient::API_VERSION."/users/$user_id/tags/$tag_id";
                $response = $client->call($request, HTTP_Request2::METHOD_PUT);
                $app->flash("success", "Successfully added tag, $label, to subscription list");
            } else {
                $app->flash("error", "Unable to save tag, $label, for user $displayName");
            }   
        }   
        
        if ($subscribe == "false") {
            $request = APIClient::API_VERSION."/users/$user_id/tags/$tag_id";
            $removedTag = $client->call($request, HTTP_Request2::METHOD_DELETE);
            if ($removedTag) {
                $app->flash("success", "Successfully removed tag $label for user $displayName");
            } else {
                $app->flash("error", "Unable to remove tag $label for user $displayName");
            }
        }   
        
        $app->response()->redirect($app->request()->getReferer());
    }

    public function tagDetails($label)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $request = APIClient::API_VERSION."/tags/getByLabel/$label";
        $response = $client->call($request);
        $tag = $client->cast("Tag", $response);
        
        $tag_id = $tag->getId();
        
        if (is_null($tag_id)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }

        $tasks = array();
        $request = APIClient::API_VERSION."/tags/$tag_id/tasks";
        $data = array("limit" => 10);
        $response = $client->call($request, HTTP_Request2::METHOD_GET, $data);
        if ($response) {
            foreach ($response as $stdObject) {
                $tasks[] = $client->cast("Task", $stdObject);
            }         
            $app->view()->setData("tasks", $tasks);
        }  
        
        if (UserRouteHandler::isLoggedIn()) {

            $user_id = UserSession::getCurrentUserID();        
            $app->view()->appendData(array(
                    "user_id" => $user_id
            ));

            $user_tags = array();
            $request = APIClient::API_VERSION."/users/$user_id/tags";
            $response = $client->call($request);
            
            if ($response) {
                foreach ($response as $stdObject) {
                    $user_tags[] = $client->cast("Tag", $stdObject);
                }
                if (count($user_tags) > 0) {
                    $app->view()->appendData(array(
                            "user_tags" => $user_tags

                    )); 
                    foreach ($user_tags as $tag) {
                        if ($label == $tag->getLabel()) {
                            $app->view()->appendData(array(
                               "subscribed" => true
                            )); 
                        }
                    }
                }
            }
        }

        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        $top_tags = array();
        $request = APIClient::API_VERSION."/tags/topTags";
        $top_tags= $client->castCall(array("Tag"), $request, HTTP_Request2::METHOD_GET, null, array("limit" => 30));
        $app->view()->appendData(array(
                 "tag" => $label,
                 "top_tags" => $top_tags,
                 "taskTypeColours" => $taskTypeColours
        )); 
        $app->render("tag.tpl");
    }
}
