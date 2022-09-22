<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TagRouteHandler
{
    public function init()
    {
        global $app;

        $app->map(['GET', 'POST'],
            '/all/tags[/]',
            '\SolasMatch\UI\RouteHandlers\TagRouteHandler:tagsList')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('tags-list');

        $app->map(['GET', 'POST'],
            '/tag/{id}/{subscribe}/{sesskey}[/]',
            '\SolasMatch\UI\RouteHandlers\TagRouteHandler:tagSubscribe')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('tag-subscribe');
        
        $app->map(['GET', 'POST'],
            '/tag/{id}[/]',
            '\SolasMatch\UI\RouteHandlers\TagRouteHandler:tagDetails')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('tag-details');
    }

    public function tagsList(Request $request, Response $response)
    {
        global $template_data;
        $userDao = new DAO\UserDao();
        $tagDao = new DAO\TagDao();

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $user_tags = $userDao->getUserTags($user_id);
        $foundTags = null;
        $name = "";
        $nameErr = null;

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();

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
            $template_data = array_merge($template_data, array(
                "user_tags" => $user_tags,
                "foundTags" => $foundTags,
                'searchedText'  => $name
            ));
        } else {
            $template_data = array_merge($template_data, array(
                    "user_tags" => $user_tags,
                    "foundTags" => $foundTags,
                    "nameErr"  => $nameErr
            ));
        }
        return UserRouteHandler::render("tag/tag-list.tpl", $response);
    }

    public function tagSubscribe(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $id = $args['id'];
        $subscribe = $args['subscribe'];
        $sesskey = $args['sesskey'];

        $tagDao = new DAO\TagDao();
        $userDao = new DAO\UserDao();

        if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($sesskey, 'tagSubscribe')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

        $tag = $tagDao->getTag($id);
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $current_user = $userDao->getUser($user_id);
        
        $displayName = $current_user->getDisplayName();
        
        if ($subscribe == "true") {
            $userLikeTag = $userDao->addUserTagById($user_id, $id);
            if ($userLikeTag) {
                UserRouteHandler::flash("success", sprintf(Lib\Localisation::getTranslation('tag_4'), $tag->getLabel()));
            } else {
                UserRouteHandler::flash(
                    "error",
                    sprintf(Lib\Localisation::getTranslation('tag_5'), $tag->getLabel(), $displayName)
                );
            }
        }
        
        if ($subscribe == "false") {
            $removedTag = $userDao->removeUserTag($user_id, $id);
            if ($removedTag) {
                UserRouteHandler::flash(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('tag_6'), $tag->getLabel(), $displayName)
                );
            } else {
                UserRouteHandler::flash(
                    "error",
                    sprintf(Lib\Localisation::getTranslation('tag_7'), $tag->getLabel(), $displayName)
                );
            }
        }

        return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('tag-details', ['id' => $id]));
    }

    public function tagDetails(Request $request, Response $response, $args)
    {
        global $template_data;
        $id = $args['id'];

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

        $template_data = array_merge($template_data, [
            'tasks' => $tasks,
            'taskTags' => $taskTags,
            'taskProjTitles' => $taskProjTitles,
            'taskOrgs' => $taskOrgs
        ]);

        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $template_data = array_merge($template_data, array(
            "user_id" => $user_id
        ));

        $user_tags = $userDao->getUserTags($user_id);
        if ($user_tags && count($user_tags) > 0) {
            $template_data = array_merge($template_data, array(
                'user_tags' => $user_tags
            ));
            foreach ($user_tags as $userTag) {
                if ($label == $userTag->getLabel()) {
                    $template_data = array_merge($template_data, array(
                        'subscribed' => true
                    ));
                }
            }
        }

        $top_tags= $tagDao->getTopTags(30);
        $sesskey = Common\Lib\UserSession::getCSRFKey();
        $template_data = array_merge($template_data, array(
            'sesskey' => $sesskey,
            "tag" => $tag,
            "top_tags" => $top_tags,
        ));
        return UserRouteHandler::render("tag/tag.tpl", $response);
    }
}

$route_handler = new TagRouteHandler();
$route_handler->init();
unset ($route_handler);
