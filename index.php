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
 */
$view = $app->view();
$view->setData('url', $url);
$user = null;
if ($user_id = $users->currentUserID()) {
    $user = array(
        'id' => $users->currentUserID(),
        'email' => $users->userEmail($user_id)
    );
    $view->setData('user', $user);
}

/**
 * Set up application modes, depending on whether we're in development or production.
 */
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

/**
 * Application routing
 */

/**
 * Home page
 */
$app->get('/', function () use ($app) {
	/**
	 * Home page functionality
	 */
	/*
	if ($tasks = $s->stream->getStream(10) {
		$s->assign('tasks', $tasks);
	}
	*/
	//$s->display('index.tpl');
    $stream = new Stream();
    if ($tasks = $stream->getStream(10)) {
        $app->view()->setData('tasks', $tasks);
    }
    $tags = new Tags();
    $app->view()->setData('tags', $tags);
    $app->view()->setData('top_tags', $tags->topTags(30));
    $app->view()->setData('io', new IO());
    $app->render('index.tpl');
});

/**
 * Task page
 */
#RewriteRule ^task/([0-9]+)/$ pages/task.php?task_id=$1
$app->get('/task/:task_id/', function ($task_id) use ($app) {
    $task = new Task($task_id);
    
    if (!$task->isInit())
    {
        // Make sure that we've been passed a correct task.
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->view()->setData('task', $task);
    if ($task_files = $task->files())
    {
        $app->view()->setData('task_files', $task_files);
    }
    $app->view()->setData('max_file_size', IO::maxFileSizeMB());
    $app->view()->setData('body_class', 'task_page');
     $app->view()->setData('tags', new Tags());
    $app->render('task.tpl');
});

$app->run();