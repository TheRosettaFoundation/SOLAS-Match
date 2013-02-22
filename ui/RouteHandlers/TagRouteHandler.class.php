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
        $userDao = new UserDao();
        $tagDao = new TagDao();

        $user_id = UserSession::getCurrentUserID();
        $user_tags = $userDao->getUserTags($user_id);
        $all_tags = $tagDao->getTag(null);
        
        $app->view()->appendData(array(
            "user_tags" => $user_tags,
            "all_tags" => $all_tags
        )); 
        
        $app->render("tag-list.tpl");
    }

    public function tagSubscribe($label, $subscribe)
    {
        $app = Slim::getInstance();
        $tagDao = new TagDao();
        $userDao = new UserDao();

        $tag = $tagDao->getTag(array('label' => $label));
        $user_id = UserSession::getCurrentUserID();
        $current_user = $userDao->getUser(array('id' => $user_id));

        if (!is_object($current_user)) {
            $app->flash("error", "Login required to access page");
            $app->redirect($app->urlFor("login"));
        }   
        
        $tag_id = $tag->getId();
        $displayName = $current_user->getDisplayName();
        
        if ($subscribe == "true") {
            $userLikeTag = $userDao->addUserTagById($user_id, $tag_id);            
            if ($userLikeTag) {
                $app->flash("success", "Successfully added tag, $label, to subscription list");
            } else {
                $app->flash("error", "Unable to save tag, $label, for user $displayName");
            }   
        }   
        
        if ($subscribe == "false") {
            $removedTag = $userDao->removeUserTag($user_id, $tag_id);
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
        $tagDao = new TagDao();
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        $userDao = new UserDao();

        $tag = $tagDao->getTag(array('label' => $label));
        $tag_id = $tag->getId();
        if (is_null($tag_id)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }

        $tasks = $tagDao->getTasksWithTag($tag_id, 10);
        for ($i = 0; $i < count($tasks); $i++) {
            $tasks[$i]['Project'] = $projectDao->getProject(array('id' => $tasks[$i]->getProjectId()));
            $tasks[$i]['Org'] = $orgDao->getOrganisation(array('id' => $tasks[$i]['Project']->getOrganisationId()));
        }

        $app->view()->setData('tasks', $tasks);
        
        if (UserRouteHandler::isLoggedIn()) {

            $user_id = UserSession::getCurrentUserID();        
            $app->view()->appendData(array(
                    "user_id" => $user_id
            ));

            $user_tags = $userDao->getUserTags($user_id);
            if ($user_tags && count($user_tags) > 0) {
                $app->view()->appendData(array(
                        'user_tags' => $user_tags
                )); 
                foreach ($user_tags as $tag) {
                    if ($label == $tag->getLabel()) {
                        $app->view()->appendData(array(
                           'subscribed' => true
                        )); 
                    }
                }
            }
        }

        $numTaskTypes = Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Settings::get("ui.task_{$i}_colour");
        }

        $top_tags= $tagDao->getTopTags(30);
        $app->view()->appendData(array(
                 "tag" => $label,
                 "top_tags" => $top_tags,
                 "taskTypeColours" => $taskTypeColours
        )); 
        $app->render("tag.tpl");
    }
}
