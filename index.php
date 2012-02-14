<?php
require 'libs/Slim/Slim/Slim.php';
require 'app/Views/SmartyView.php';
require 'app/Settings.class.php';
require 'app/MySQLWrapper.class.php';
require 'app/UserDao.class.php';
require 'app/TaskStream.class.php';
require 'app/TaskDao.class.php';
require 'app/TagsDao.class.php';
require 'app/IO.class.php';
require 'app/Organisations.class.php';
require 'app/TaskFiles.class.php';
require 'app/TaskFile.class.php';
require 'app/lib/Languages.class.php';
require 'app/lib/URL.class.php';
require 'app/lib/Authentication.class.php';
require 'app/lib/UserSession.class.php';
require 'app/lib/Tags.class.php';
require 'app/lib/Upload.class.php';

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

$authenticateForRole = function ( $role = 'member' ) {
    return function () use ( $role ) {
        $app = Slim::getInstance();
        $user_dao = new UserDao();
        if (!is_object($user_dao->getCurrentUser())) {
            $app->redirect('/login');
        }
    };
};

$app->get('/', function () use ($app) {
    if ($tasks = TaskStream::getStream(10)) {
        $app->view()->setData('tasks', $tasks);
    }
    $tags_dao = new TagsDao();
    $app->view()->setData('top_tags', $tags_dao->getTopTags(30));
    $app->view()->appendData(array('url_task_upload' => $app->urlFor('task-upload')));
    $app->render('index.tpl');
})->name('home');

$app->get('/task/upload', $authenticateForRole('organisation'), function () use ($app) {
    $error = null;
    $form_file_field = 'new_task_file';

    if (Upload::hasFileBeenUploaded($form_file_field)) {
        echo "ok, we're submitting";die;
    
        // Post probably won't work here....
        $post = (object)$app->request()->post();
        $source_id = Languages::languageIdFromName($post->source);
        $target_id = Languages::languageIdFromName($post->target);

        if (!$source_id || !$target_id) {
            $error = "Sorry, a langauge you entered does not exist in our system. Functionality for adding a language still remains to be implemented. Please press back and enter a different language name.";
        }
        else {
            $task_dao = new TaskDao();
            $task = $task_dao->create(array(
                'title' => $post->title,
                'organisation_id' => $post->organisation_id,
                'source_id' => $source_id,
                'target_id' => $target_id,
                'word_count' => $post->word_count
            ));
            TaskTags::setTagsFromStr($task, $post->tags);
            if (!IO::saveUploadedFile('original_file', $post->organisation_id, $task->getTaskId())) {
                $error = "Failed to upload file :(";
            }
            if (is_null($error)) {
                $app->redirect('/task/' . $task_id);
            }
        }
    }

    if (!is_null($error)) {
        $app->view()->appendData(array('error' => $error));
    }
    $app->view()->appendData(array(
        'url_task_upload'       => $app->urlFor('task-upload'),
        'max_file_size_bytes'   => IO::maxFileSizeBytes(),
        'max_file_size_mb'      => IO::maxFileSizeMB(),
        'form_file_field'       => $form_file_field
    ));
    $app->render('task.upload.tpl');
})->via('GET','POST')->name('task-upload');

$app->get('/task/id/:task_id/', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->view()->setData('task', $task);

    if ($task_files = TaskFiles::getTaskFiles($task)) {
        $app->view()->setData('task_files', $task_files);
    }
    $app->view()->setData('max_file_size', IO::maxFileSizeMB());
    $app->view()->setData('body_class', 'task_page');
    $app->render('task.tpl');
});

$app->get('/tag/:label/', function ($label) use ($app) {
    $tags_dao = new TagsDao();
    $tag = $tags_dao->find(array('label' => $label));

    if (!is_object($tag)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    if ($tasks = TaskStream::getTaggedStream($tag->getTagId(), 10)) {
        $app->view()->setData('tasks', $tasks);
    }
    $app->view()->setData('tag', $tag);
    $app->render('tag.tpl');
});

$app->get('/login', function () use ($app) {
    $error = null;
    if (isValidPost($app)) {
        $post = (object)$app->request()->post();
        try {
            $user_dao = new UserDao();
            $user_dao->login($post->email, $post->password);
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
    $user_dao = new UserDao();
    $user_dao->logout();
    $app->redirect('/');
})->name('logout');

$app->get('/register', function () use ($app) {
    $error = null;
    $warning = null;
    if (isValidPost($app)) {
        $post = (object)$app->request()->post();
        $user_dao = new UserDao();
        if (is_object($user_dao->find(array('email' => $post->email)))) {
            $warning = 'You have already created an account. <a href="' . $app->urlFor('login') . '">Please log in.</a>';
        }
        else if (!User::isValidEmail($post->email)) {
            $error = 'The email address you entered was not valid. Please press back and try again.';
        }
        else if (!User::isValidPassword($post->password)) {
            $error = 'You didn\'t enter a password. Please press back and try again.';
        }

        if (is_null($error) && is_null($warning)) {
            if ($user = $user_dao->create($post->email, $post->password)) {
                if ($user_dao->login($user->getEmail(), $post->password)) {
                    $app->redirect('/');
                }
                else {
                    $error = 'Tried to log you in immediately, but was unable to.';
                }
            }
            else {
                $error = 'Unable to register.';
            }
        } 
    }
    if ($error !== null) {
        $app->view()->appendData(array('error' => $error));
    }
    if ($warning !== null) {
        $app->view()->appendData(array('warning' => $warning));
    }
    $app->render('register.tpl');
})->via('GET', 'POST')->name('register');

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
$user_dao = new UserDao();
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
$view->appendData(array('url_register' => $app->urlFor('register')));
$user = null;
if ($user = $user_dao->getCurrentUser()) {
    $view->appendData(array('user' => $user));
}

$app->run();