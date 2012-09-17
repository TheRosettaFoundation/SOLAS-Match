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

        $user_dao = new UserDao();
        $tags_dao = new TagsDao();
        
        $current_user = $user_dao->getCurrentUser();
        $user_id = $current_user->getUserId();
        
        $user_tags = $user_dao->getUserTags($user_id);
        $all_tags = $tags_dao->getAllTags();
        
        $app->view()->appendData(array(
            'user_tags' => $user_tags,
            'all_tags' => $all_tags
        )); 
        
        $app->render('tag-list.tpl');
    }

    public function tagSubscribe($label, $subscribe)
    {
        $app = Slim::getInstance();
        
        $tag_dao = new TagsDao();
        $tag = $tag_dao->find(array('label' => $label));
        
        $user_dao = new UserDao();
        $current_user = $user_dao->getCurrentUser();
        
        if (!is_object($current_user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }   
        
        $tag_id = $tag->getTagId();
        $user_id = $current_user->getUserId();
        
        if($subscribe == "true") {
            if(!($user_dao->likeTag($user_id, $tag_id))) {
                $displayName = $current_user->getDisplayName();
                $warning = "Unable to save tag, $label, for user $displayName";
                $app->view()->appendData(array("warning" => $warning));
            }   
        }   
        
        if($subscribe == "false") {
            if(!($user_dao->removeTag($user_id, $tag_id))) {
                $displayName = $current_user->getDisplayName();
                $warning = "Unable to remove tag $label for user $displayName";
                $app->view()->appendData(array('warning' => $warning));
            }   
        }   
        
        $app->response()->redirect($app->request()->getReferer());
    }

    public function tagDetails($label)
    {
        $app = Slim::getInstance();

        $task_dao = new TaskDao;
        $tag_id = $task_dao->getTagId($label);
        
        if (is_null($tag_id)) {
            header('HTTP/1.0 404 Not Found');
            die;
        }   
        
        if ($tasks = TaskStream::getTaggedStream($label, 10)) {
            $app->view()->setData('tasks', $tasks);
        }   
        
        if (UserDao::isLoggedIn()) {
            $user_dao = new UserDao();
            $current_user = $user_dao->getCurrentUser();
            $user_id = $current_user->getUserId();
        
            $app->view()->appendData(array(
                    'user_id' => $user_id
            )); 
            $user_tags = $user_dao->getUserTags($user_id);
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
        
        $app->view()->appendData(array(
                 'tag' => $label,
                 'top_tags' => TagsDao::getTopTags(30),
        )); 
        $app->render('tag.tpl');
    }
}
