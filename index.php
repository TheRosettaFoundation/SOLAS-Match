<?php
require 'libs/SlimEoin/Slim/Slim.php';
require 'libs/SlimExtras/Views/SmartyView.php';

SmartyView::$smartyDirectory = 'libs/smarty/libs';
SmartyView::$smartyCompileDirectory = 'app/templating/templates_compiled';
SmartyView::$smartyTemplatesDirectory = 'app/templating/templates';
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
require 'app/lib/Email.class.php';
require 'app/lib/Notify.class.php';

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

$authenticateForRole = function ( $role = 'translator' ) {

    /* Sample:

        return function () use ( $role ) { 
            $user = User::fetchFromDatabaseSomehow();
            if ( $user->belongsToRole($role) === false ) { 
                Slim::flash('error', 'Login required');
                Slim::redirect('/login');
            }   
        }   
    */

    return function () use ( $role ) {
        $app = Slim::getInstance();
        $user_dao = new UserDao();
        $current_user = $user_dao->getCurrentUser();
    
        if (!is_object($current_user)) {
            $app->redirect($app->urlFor('login'));
        }
        else if ($user_dao->belongsToRole($current_user, $role) === false) { 
            $app->flash('error', 'Login required');
            $app->redirect($app->urlFor('login'));
        }   
    };
};

$app->get('/', function () use ($app) {
    if ($tasks = TaskStream::getStream(10)) {
        $app->view()->setData('tasks', $tasks);
    }
    $task_dao = new TaskDao;
    $app->view()->appendData(array(
        'top_tags' => $task_dao->getTopTags(30),
        'current_page' => 'home'
    ));
    $app->render('index.tpl');
})->name('home');

$app->get('/task/upload', $authenticateForRole('organisation_member'), function () use ($app) {
    $error_message = null;
    $field_name = 'new_task_file';

    $user_dao = new UserDao();
    $current_user = $user_dao->getCurrentUser();
    $my_organisations = $user_dao->findOrganisationsUserBelongsTo($current_user);
    $organisation_id = $my_organisations[0]; // Not perfect, but it will do until someone appears in multiple organisations
    
    if ($app->request()->isPost()) {

        $upload_error = false;
        try {
            Upload::validateFileHasBeenSuccessfullyUploaded($field_name);
        } catch (Exception $e) {
            $upload_error = true;
            $error_message = $e->getMessage();
        }

        if (!$upload_error) {
            $task_dao = new TaskDao();
            $task = $task_dao->create(array(
                'organisation_id'   => $organisation_id,
                'title'             => $_FILES[$field_name]['name']
            ));
            
            try {
                Upload::saveSubmittedFile($field_name, $task);
            }
            catch (Exception  $e) {
                $upload_error = true;
                $error_message = 'File error: ' . $e->getMessage();
            }
        }

        if (!$upload_error) {
            $app->redirect($app->urlFor('task-describe', array('task_id' => $task->getTaskId())));
        }
    }

    if (!is_null($error_message)) {
        $app->view()->appendData(array('error' => $error_message));
    }
    $app->view()->appendData(array(
        'url_task_upload'       => $app->urlFor('task-upload'),
        'max_file_size_bytes'   => Upload::maxFileSizeBytes(),
        'max_file_size_mb'      => Upload::maxFileSizeMB(),
        'field_name'            => $field_name
    ));
    $app->render('task.upload.tpl');
})->via('GET','POST')->name('task-upload');

$app->get('/task/:task_id/upload-edited/', $authenticateForRole('translator'), function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        $app->notFound();
    }

    $field_name = 'edited_file';
    $error_message = null;

    try {
        Upload::validateFileHasBeenSuccessfullyUploaded($field_name);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }

    if (is_null($error_message)) {
        try {
            Upload::saveSubmittedFile($field_name, $task);
        } catch (Exception  $e) {
            $error_message = 'File error: ' . $e->getMessage();
        }
    }

    if (is_null($error_message)) {
        $app->redirect($app->urlFor('task-uploaded-edited'));
    }
    else {
        $app->view()->setData('task', $task);
        if ($task_file_info = $task_dao->getTaskFileInfo($task)) {
            $app->view()->setData('task_file_info', $task_file_info);
            $app->view()->setData('latest_version', $task_dao->getLatestFileVersion($task));
        }
        $app->view()->appendData(array(
            'upload_error'                 => $error_message,
            'max_file_size'         => Upload::maxFileSizeMB(),
            'body_class'            => 'task_page'
        ));
        $app->render('task.tpl');
    }
})->via('POST')->name('task-upload-edited');

$app->get('/task/uploaded-edit/', function () use ($app) {
    $app->render('task.uploaded-edit.tpl');
})->name('task-uploaded-edit');

$app->get('/task/describe/:task_id/', $authenticateForRole('organisation_member'), function ($task_id) use ($app) {
    $error      = null;
    $task_dao   = new TaskDao();
    $task       = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        $app->notFound();
    }

    if (isValidPost($app)) {
        $post = (object)$app->request()->post();

        if (!empty($post->source)) {
            $source_id = Languages::saveLanguage($post->source);
            $task->setSourceId($source_id);
        }
        if (!empty($post->target)) {
            $target_id = Languages::saveLanguage($post->target);
            $task->setTargetId($target_id);
        }
        $task->setTitle($post->title);
        $task->setTags(Tags::separateTags($post->tags));
        $task->setWordCount($post->word_count);
        $task_dao->save($task);
        if (is_null($error)) {
            $app->redirect($app->urlFor('task-uploaded', array('task_id' => $task_id)));
        }
    }

    if (!is_null($error)) {
        $app->view()->appendData(array('error' => $error));
    }
    $app->view()->appendData(array(
        'url_task_describe' => $app->urlFor('task-describe', array('task_id' => $task_id)),
        'task'              => $task,
    ));
    
    $app->render('task.describe.tpl');
})->via('GET','POST')->name('task-describe');

$app->get('/task/id/:task_id/uploaded/', $authenticateForRole('organisation_member'), function ($task_id) use ($app) {
    $app->render('task.uploaded.tpl');
})->name('task-uploaded');

$app->get('/task/id/:task_id/', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->view()->setData('task', $task);

    if ($task_file_info = $task_dao->getTaskFileInfo($task)) {
        $app->view()->appendData(array(
            'task_file_info' => $task_file_info,
            'latest_version' => $task_dao->getLatestFileVersion($task)
        ));
    }

    if ($task_dao->taskIsClaimed($task)) {
        $app->view()->appendData(array(
            'task_is_claimed' => true
        ));
        $user_dao = new UserDao();
        if ($current_user = $user_dao->getCurrentUser()) {
            if ($task_dao->hasUserClaimedTask($current_user, $task)) {
                $app->view()->appendData(array(
                    'this_user_has_claimed_this_task' => true
                ));
            }
        }
    }

    $app->view()->appendData(array(
        'max_file_size' => Upload::maxFileSizeMB(),
        'body_class'    => 'task_page'
    ));

    $app->render('task.tpl');
})->name('task');

$app->get('/task/id/:task_id/download-preview/', $authenticateForRole('translator'), function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->view()->setData('task', $task);
    $app->render('task.download-preview.tpl');
})->name('download-task-preview');

$app->get('/task/id/:task_id/download-file/v/:version/', $authenticateForRole('translator'), function ($task_id, $version) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $task_file_info         = $task_dao->getTaskFileInfo($task, $version);

    if (empty($task_file_info)) {
        throw new Exception("Task file info not set for.");
    }

    $absolute_file_path     = Upload::absoluteFilePathForUpload($task, $version, $task_file_info['filename']);
    $file_content_type      = $task_file_info['content_type'];
    $task_dao->logFileDownload($task, $version);
    IO::downloadFile($absolute_file_path, $file_content_type);
    //die;
})->name('download-task-version');

$app->post('/claim-task', $authenticateForRole('translator'), function () use ($app) {
    // get task id
    $task_id = $app->request()->post('task_id');

    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $user_dao           = new UserDao();
    $current_user       = $user_dao->getCurrentUser();
    $task_dao->claimTask($task, $current_user);
    Notify::notifyUserClaimedTask($current_user, $task);   

    $app->redirect($app->urlFor('task-claimed', array(
        'task_id' => $task_id
    )));

})->name('claim-task');

$app->get('/task/id/:task_id/claimed/', $authenticateForRole('translator'), function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }
    $app->view()->setData('task', $task);
    $app->render('task.claimed.tpl');
})->name('task-claimed');

$app->get('/task/id/:task_id/download-file/', $authenticateForRole('translator'), function ($task_id) use ($app) {
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

$app->get('/task/id/:task_id/mark-archived/', $authenticateForRole('organisation_member'), function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $task_dao->moveToArchive($task);

    $app->redirect($ref = $app->request()->getReferrer());
})->name('archive-task');

$app->get('/task/id/:task_id/download-task-latest-file/', $authenticateForRole('translator'), function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $latest_version = $task_dao->getLatestFileVersion($task);
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
    $app->view()->appendData(array(
        'tag' => $label,
        'top_tags' => $task_dao->getTopTags(30),
    ));
    $app->render('tag.tpl');
});

$app->get('/login', function () use ($app) {
    $error = null;
    if (isValidPost($app)) {
        $post = (object)$app->request()->post();
        try {
            $user_dao = new UserDao();
            $user_dao->login($post->email, $post->password);
            $app->redirect($app->urlFor("home"));
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
    $app->redirect($app->urlFor('home'));
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
                    $app->redirect($app->urlFor('home'));
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

$app->get('/client/dashboard', $authenticateForRole('organisation_member'), function () use ($app) {
    $user_dao           = new UserDao();
    $task_dao           = new TaskDao;
    $current_user       = $user_dao->getCurrentUser();
    $my_organisations   = $user_dao->findOrganisationsUserBelongsTo($current_user);
    $my_tasks           = $task_dao->findTasks(array(
        'organisation_ids'  => $my_organisations
    ), 'created_time', 'DESC');

    if (!is_null($my_tasks)) {
        $app->view()->appendData(array(
            'my_tasks' => $my_tasks
        ));
    }

    $app->view()->appendData(array(
        'current_page'  => 'client-dashboard',
        'task_dao'      => $task_dao
    ));
    $app->render('client.dashboard.tpl');
})->name('client-dashboard');

$app->get('/profile', function () use ($app) {
    if($app->request()->isPost()) {
        $user_dao = new UserDao();
        $currentUser = $user_dao->getCurrentUser();

        $displayName = $app->request()->post('name');
 	if($displayName != NULL) {
	    $currentUser->setDisplayName($displayName);
	}

	$userBio = $app->request()->post('bio');
	if($userBio != NULL) {
	    $currentUser->setBiography($userBio);
	}

	$nativeLang = $app->request()->post('nLanguage');
	if($nativeLang != NULL) {
	    $currentUser->setNativeLanguage($nativeLang);
	}
	$user_dao->save($currentUser);

	$app->redirect($app->urlFor('home'));
    }
    $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $language = substr($language, 0, 5);
  
    $app->view()->setData('current_page',  'user-profile');
    $app->view()->appendData(array('language' => $language));

    $app->render('user-profile.tpl');
})->via('POST')->name('user-profile');

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
    $app->view()->appendData(array(
        'url_login' => $app->urlFor('login'),
        'url_logout' => $app->urlFor('logout'),
        'url_register' => $app->urlFor('register')
    ));
    $user_dao = new UserDao();
    if ($current_user = $user_dao->getCurrentUser()) {
        $app->view()->appendData(array('user' => $current_user));
        if ($user_dao->belongsToRole($current_user, 'organisation_member')) {
            $app->view()->appendData(array(
                'user_is_organisation_member' => true,
                'user_organisations' => $user_dao->findOrganisationsUserBelongsTo($current_user)
            ));
        }
    }
});

$app->run();
