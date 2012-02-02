<?php
require 'libs/Slim/Slim/Slim.php';
require_once 'app/Views/SmartyView.php';
require_once 'app/Settings.class.php';
require_once 'app/MySQLWrapper.class.php';
require_once 'app/Users.class.php';
require_once 'app/URL.class.php';
require_once 'app/Stream.class.php';
require_once 'app/Tasks.class.php';
require_once 'app/Tags.class.php';
require_once 'app/IO.class.php';
require_once 'app/Organisations.class.php';

/**
 * Start the session
 */
session_start();
// Can we get away from the app's old system?
//require('app/includes/smarty.php');

/**
 * Initiate the app
 */
$app = new Slim(array(
    'debug' => true,
    'view' => new SmartyView(),
    'mode' => 'development' // default is development. TODO get from config file, or set in environment...... $_ENV['SLIM_MODE'] = 'production';
));

$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.path' => '../logs', // Need to set this...
        'debug' => false
    ));
});

$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

$app->get('/', function () use ($app) {
    $stream = new Stream();
    if ($tasks = $stream->getStream(10)) {
        $app->view()->setData('tasks', $tasks);
    }
    $tags = new Tags();
    $app->view()->setData('tags', $tags);
    $app->view()->setData('top_tags', $tags->topTags(30));
    $app->view()->setData('io', new IO());
    $app->render('index.tpl');
})->name('home');

$app->get('/task/create/', $authenticateForRole('organisation'), function () use ($app) {
    $error = null;
    if (isValidPost($app)) {
        if (!$source_id || !$target_id) {
            echo "Sorry, a langauge you entered does not exist in our system. Functionality for adding a language still remains to be implemented. Please press back and enter a different languag name."; die;
        }

        $task_id = $s->tasks->create($post->title, $post->organisation_id, $post->tags, $post->source_id, $post->target_id, $post->word_count);
        $task = new Task($task_id);

        // Save the file
        if (!IO::saveUploadedFile('original_file', $post->organisation_id, $task_id)) {
            echo "Failed to upload file :("; die;
        }      
    }
    else {
        $app->render('task.create.tpl');
    }
});

$app->get('/task/:task_id/', function ($task_id) use ($app) {
    $task = new Task($task_id);
    
    if (!$task->isInit()) {
        // Make sure that we've been passed a correct task.
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->view()->setData('task', $task);
    if ($task_files = $task->files()) {
        $app->view()->setData('task_files', $task_files);
    }
    $app->view()->setData('max_file_size', IO::maxFileSizeMB());
    $app->view()->setData('body_class', 'task_page');
    $app->view()->setData('tags', new Tags());
    $app->render('task.tpl');
});

$app->get('/login', function () use ($app) {
    // Test for Post & make a cheap security check, to get avoid from bots
    $error = null;
    if (isValidPost($app)) {
        // Don't forget to set the correct attributes in your form (name="user" + name="password")
        $post = (object)$app->request()->post();
        try {
            User::login($post->email, $post->password);
            $app->redirect('/');
        } catch (AuthenticationException $e) {
            $error = '<p>Unable to log in. Please check your email and password. <a href="' . $app->urlFor('login') . '">Try logging in again</a>.</p>';
            $error .= '<p>System error: <em>' . $e->getMessage() .'</em></p>';
            echo $error;
        }
    } else {
        $app->view()->appendData(array('url_login', $app->urlFor('login')));
        $app->render('login.tpl');
    }
})->via('GET','POST')->name('login');

$app->get('/logout', function () use ($app) {
    Users::logOut();
    $app->redirect('/');
})->name('logout');

$authenticateForRole = function ( $role = 'member' ) {
    return function () use ( $role ) {
        $app = Slim::getInstance();
        $users = new Users();
        if (!$users->currentUserID()) {
            $app->redirect('/login');    
        }
    };
};

function isValidPost(&$app) {
    return $app->request()->isPost() && sizeof($app->request()->post()) > 2;
}

/**
 * For login, and named routes, you can use the urlFor() method, in conjucuntion
 * with Named Routes http://www.slimframework.com/documentation/develop
 */

/**
 * Set up application objects
 * 
 * Given that we don't have object factories implemented, we'll initialise them directly here.
 */
$users = new Users();
$url = new URL();

/**
 * General variables
 * Set up general variables to be used across templates.
 * These configurations may be better done with a hook rule:
 *      $app->hook('slim.before', function () use ($app) {
 *          $app->view()->appendData(array('baseUrl' => '/base/url/here'));
 *      });
 *      // http://help.slimframework.com/discussions/questions/49-how-to-deal-with-base-path-and-different-routes
 */
$view = $app->view();
$view->appendData(array('url' => $url));
$view->appendData(array('url_login' => $app->urlFor('login')));
$view->appendData(array('url_logout' => $app->urlFor('logout')));
$user = null;
if ($user_id = $users->currentUserID()) {
    $user = array(
        'id' => $users->currentUserID(),
        'email' => $users->userEmail($user_id)
    );
    $view->appendData(array('user' => $user));
}


$app->run();