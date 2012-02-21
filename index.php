<?php
require 'libs/Slim/Slim/Slim.php';
require 'app/Views/SmartyView.php';
require 'app/Settings.class.php';
require 'app/MySQLWrapper.class.php';
require 'app/UserDao.class.php';
require 'app/TaskStream.class.php';
require 'app/TaskDao.class.php';
//require 'app/TagsDao.class.php';
require 'app/IO.class.php';
require 'app/Organisations.class.php';
//require 'app/TaskFiles.class.php';
//require 'app/TaskFile.class.php';
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
    $task_dao = new TaskDao;
    $app->view()->setData('top_tags', $task_dao->getTopTags(30));
    $app->render('index.tpl');
})->name('home');

$app->get('/task/upload', $authenticateForRole('organisation'), function () use ($app) {
    $error = null;
    $field_name = 'new_task_file';
    $organisation_id = 1; // TODO Implement organisation identification!

    if (Upload::hasFormBeenSubmitted($field_name)) {
        $task_dao = new TaskDao();
        $task = $task_dao->create(array(
            'organisation_id' => $organisation_id,
        ));
        try {
            Upload::saveUploadedFile($field_name, $organisation_id, $task->getTaskId());
            $app->redirect('/task/describe/' . $task->getTaskId() . '/');
        }
        catch (Exception  $e) {
            $error = 'File error: ' . $e->getMessage();
        }
    }

    if (!is_null($error)) {
        $app->view()->appendData(array('error' => $error));
    }
    $app->view()->appendData(array(
        'url_task_upload'       => $app->urlFor('task-upload'),
        'max_file_size_bytes'   => IO::maxFileSizeBytes(),
        'max_file_size_mb'      => IO::maxFileSizeMB(),
        'field_name'       => $field_name
    ));
    $app->render('task.upload.tpl');
})->via('GET','POST')->name('task-upload');

$app->get('/task/describe/:task_id/', $authenticateForRole('organisation'), function ($task_id) use ($app) {
    $error      = null;
    $task_dao   = new TaskDao();
    $task       = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        $app->notFound();
    }

    if (isValidPost($app)) {
        $post = (object)$app->request()->post();

        if (!is_null($post->source)) {
            $source_id = Languages::saveLanguage($post->source);
            $task->setSourceId($source_id);
        }
        if (!is_null($post->target)) {
            $target_id = Languages::saveLanguage($post->target);
            $task->setTargetId($target_id);
        }
        $task->setTitle($post->title);
        $task->setTags(Tags::separateTags($post->tags));
        $task->setWordCount($post->word_count);
        $task_dao->save($task);
        if (is_null($error)) {
            $app->redirect($app->urlFor('task', array('task_id' => $task_id)));
        }
    }

    if (!is_null($error)) {
        $app->view()->appendData(array('error' => $error));
    }
    $app->view()->appendData(array('url_task_describe' => $app->urlFor('task-describe', array('task_id' => $task_id))));
    $app->render('task.describe.tpl');
})->via('GET','POST')->name('task-describe');

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
})->name('task');

$app->get('/task/id/:task_id/download_file/:file_id/', $authenticateForRole('member'), function ($task_id, $file_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $version            = 0;
    $absolute_file_path = Upload::absoluteFilePathForUpload($task, $version);
    $file_content_type  = $task_dao->uploadedFileContentType($task, $file_id, $version);

    IO::downloadFile($absolute_file_path, $file_content_type);

    $task_file->logFileDownload($task, $file_id, $version);
})->name('download-task');

$app->get('/task/id/:task_id/download_file/:file_id/v/:version/', $authenticateForRole('member'), function ($task_id, $file_id, $version) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $absolute_path      = Upload::absoluteFolderPathForUpload($task, $version);
    $file_content_type  = $task_dao->uploadedFileContentType($task, $file_id, $version);

    IO::downloadFile($absolute_path, $file_content_type);

    $task_file->logFileDownload($task, $file_id, $version);
})->name('download-task-version');

$app->get('/tag/:label/', function ($label) use ($app) {
    $task_dao = new TaskDao;
    $tag_id = $task_dao->getTagId($label);

    if (is_null($tag_id)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    if ($tasks = TaskStream::getTaggedStream($label, 10)) {
        $app->view()->setData('tasks', $tasks);
    }
    $app->view()->setData('tag', $label);
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
$view->appendData(array('url_task_upload' => $app->urlFor('task-upload')));

$user = null;
if ($user = $user_dao->getCurrentUser()) {
    $view->appendData(array('user' => $user));
}

$app->run();