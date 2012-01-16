<?php
require 'libs/Slim/Slim/Slim.php';
require_once 'app/Views/SmartyView.php';

// Can we get away from the app's old system?
//require('app/includes/smarty.php');

$app = new Slim(array(
    'debug' => true,
    'view' => new SmartyView(),
    'mode' => 'development' // default is development. TODO get from config file, or set in environment...... $_ENV['SLIM_MODE'] = 'production';
));

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
    $app->render('index.tpl');
});

$app->run();