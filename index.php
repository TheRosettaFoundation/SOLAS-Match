<?php
require 'libs/Slim/Slim/Slim.php';
require 'libs/SlimExtras/Views/SmartyView.php';

SmartyView::$smartyDirectory = '/home/eoin/sites/smarty/libs';
SmartyView::$smartyCompileDirectory = '/home/eoin/sites/solasmatch/app/templating/templates_compiled';
SmartyView::$smartyTemplatesDirectory = '/home/eoin/sites/solasmatch/app/templating/templates';
SmartyView::$smartyExtensions = array(
    dirname('libs/SlimExtras/Views/SmartyView.php') . '/Extension/Smarty'
);
require 'app/Settings.class.php';
require 'app/MySQLWrapper.class.php';
require 'app/UserDao.class.php';
require 'app/TaskStream.class.php';
require 'app/TaskDao.class.php';
require 'app/IO.class.php';
require 'app/Organisations.class.php';
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
            Upload::saveSubmittedFile($field_name, $task);
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

$app->get('/task/:task_id/upload-edited/', $authenticateForRole('organisation'), function ($task_id) use ($app) {
    // !!!!!!!!!!!!!!!! Old code that needs updating
    /*
     * Process submitted form data to create a new task. 
     * Simple mockup functionality. Therefore, not much error checking happening. 
    */

    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        $app->notFound();
    }

    try {
        Upload::saveSubmittedFile('edited_file', $task);
    }
    catch (Exception $e) {
        echo $e->getMessage();
        die;
    }
    
    $app->redirect($app->urlFor('task', array('task_id' => $task->getTaskId())));
})->via('POST')->name('task-upload-edited');

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

    if ($task_file_info = $task_dao->getTaskFileInfo($task)) {
        $app->view()->setData('task_file_info', $task_file_info);
        $app->view()->setData('latest_version', $task_dao->getLatestFileVersion($task));
    }
    $app->view()->setData('max_file_size', IO::maxFileSizeMB());
    $app->view()->setData('body_class', 'task_page');
    $app->render('task.tpl');
})->name('task');

$app->get('/task/id/:task_id/download-file/v/:version/', $authenticateForRole('member'), function ($task_id, $version) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $absolute_path      = Upload::absoluteFolderPathForUpload($task, $version);
    $task_file_info = $task_dao->getTaskFileInfo($task, $version);
    $file_content_type  = $task_file_info['content_type'];

    IO::downloadFile($absolute_path, $file_content_type);

    $task_file->logFileDownload($task, $version);
})->name('download-task-version');

$app->get('/task/id/:task_id/download-file/', $authenticateForRole('member'), function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->redirect($app->urlFor('download-task-version', array(
        'task_id' => $task_id,
        'version' => 0
    )));

})->name('download-task');

$app->get('/task/id/:task_id/download-task-latest-file/', $authenticateForRole('member'), function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $latest_version = $task_dao->nextFileVersionNumber($task);
    $app->redirect($app->urlFor('download-task-version', array(
        'task_id' => $task_id,
        'version' => $latest_version
    )));
})->name('download-task-latest-version');

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
 * Set up application objects
 * 
 * Given that we don't have object factories implemented, we'll initialise them directly here.
 */
$app->hook('slim.before', function () use ($app) {
    // Replace with: {urlFor name="task-upload"}
    $app->view()->appendData(array('url_login' => $app->urlFor('login')));
    $app->view()->appendData(array('url_logout' => $app->urlFor('logout')));
    $app->view()->appendData(array('url_register' => $app->urlFor('register')));
    $user_dao = new UserDao();
    $user = null;
    if ($user = $user_dao->getCurrentUser()) {
        $app->view()->appendData(array('user' => $user));
    }
});

$app->run();