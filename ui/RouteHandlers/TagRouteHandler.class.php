<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

class TagRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();

        $app->get(
            "/all/tags/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "tagsList")
        )->via("POST")->name("tags-list");

        $app->get(
            "/tag/:id/:subscribe/:sesskey/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "tagSubscribe")
        )->via("POST")->name("tag-subscribe");
        
        $app->get(
            "/tag/:id/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "tagDetails")
        )->via("POST")->name("tag-details");
    }

    public function tagsList()
    {
        $app = \Slim\Slim::getInstance();
        $userDao = new DAO\UserDao();
        $tagDao = new DAO\TagDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $user_tags = $userDao->getUserTags($user_id);
        $foundTags = null;
        $name = "";
        $nameErr = null;

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['search'])) {
                $name = $post['searchName'];
                if (Lib\Validator::filterSpecialChars($name) == true) {
                    $foundTags = $tagDao->searchForTag($name);
                } else {
                    $nameErr = Lib\Localisation::getTranslation('tag_list_invalid_search');
                }
            }

            if (isset($post['listAll'])) {
                $foundTags = $tagDao->getTags(null);
            }
        }
        
        if (is_null($nameErr)) {
            $app->view()->appendData(array(
                "user_tags" => $user_tags,
                "foundTags" => $foundTags,
                'searchedText'  => $name
            ));
        } else {
            $app->view()->appendData(array(
                    "user_tags" => $user_tags,
                    "foundTags" => $foundTags,
                    "nameErr"  => $nameErr
            ));
        }
        $app->render("tag/tag-list.tpl");
    }

    public function tagSubscribe($id, $subscribe, $sesskey)
    {
        $app = \Slim\Slim::getInstance();
        $tagDao = new DAO\TagDao();
        $userDao = new DAO\UserDao();

        Common\Lib\UserSession::checkCSRFKey($sesskey, 'tagSubscribe');

        $tag = $tagDao->getTag($id);
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $current_user = $userDao->getUser($user_id);
        
        $displayName = $current_user->getDisplayName();
        
        if ($subscribe == "true") {
            $userLikeTag = $userDao->addUserTagById($user_id, $id);
            if ($userLikeTag) {
                $app->flash("success", sprintf(Lib\Localisation::getTranslation('tag_4'), $tag->getLabel()));
            } else {
                $app->flash(
                    "error",
                    sprintf(Lib\Localisation::getTranslation('tag_5'), $tag->getLabel(), $displayName)
                );
            }
        }
        
        if ($subscribe == "false") {
            $removedTag = $userDao->removeUserTag($user_id, $id);
            if ($removedTag) {
                $app->flash(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('tag_6'), $tag->getLabel(), $displayName)
                );
            } else {
                $app->flash(
                    "error",
                    sprintf(Lib\Localisation::getTranslation('tag_7'), $tag->getLabel(), $displayName)
                );
            }
        }
        
        $app->response()->redirect($app->request()->getReferer());
    }

    public function tagDetails($id)
    {
        $app = \Slim\Slim::getInstance();
        $tagDao = new DAO\TagDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $userDao = new DAO\UserDao();

        $tag = $tagDao->getTag($id);
        $label = $tag->getLabel();
        $tag_id = $tag->getId();

        $tasks = $tagDao->getTasksWithTag($tag_id, 10);
        if (empty($tasks)) $tasks = array();
        $taskTags = array();
        $taskProjTitles = array();
        $taskOrgs = array();
        for ($i = 0; $i < count($tasks); $i++) {
            $currId = $tasks[$i]->getId();
            $currTaskProj = $projectDao->getProject($tasks[$i]->getProjectId());
            //Get the task's project's list of tags.
            $taskTags[$currId] = $currTaskProj->getTag();
            $taskProjTitles[$currId] = $currTaskProj->getTitle();
            $taskOrgs[$currId] = $orgDao->getOrganisation($currTaskProj->getOrganisationId());
        }

        $app->view()->setData('tasks', $tasks);
        $app->view()->setData('taskTags', $taskTags);
        $app->view()->setData('taskProjTitles', $taskProjTitles);
        $app->view()->setData('taskOrgs', $taskOrgs);

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $app->view()->appendData(array(
            "user_id" => $user_id
        ));

        $user_tags = $userDao->getUserTags($user_id);
        if ($user_tags && count($user_tags) > 0) {
            $app->view()->appendData(array(
                'user_tags' => $user_tags
            ));
            foreach ($user_tags as $userTag) {
                if ($label == $userTag->getLabel()) {
                    $app->view()->appendData(array(
                        'subscribed' => true
                    ));
                }
            }
        }

        $numTaskTypes = Common\Lib\Settings::get("ui.task_types");
        $taskTypeColours = array();
        
        for ($i=1; $i <= $numTaskTypes; $i++) {
            $taskTypeColours[$i] = Common\Lib\Settings::get("ui.task_{$i}_colour");
        }

        $top_tags= $tagDao->getTopTags(30);
        $sesskey = Common\Lib\UserSession::getCSRFKey();
        $app->view()->appendData(array(
            'sesskey' => $sesskey,
            "tag" => $tag,
            "top_tags" => $top_tags,
            "taskTypeColours" => $taskTypeColours
        ));
        $app->render("tag/tag.tpl");
    }
}

$route_handler = new TagRouteHandler();
$route_handler->init();
unset ($route_handler);
