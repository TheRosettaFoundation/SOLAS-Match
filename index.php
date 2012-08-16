<?php
require "vendor/autoload.php";

SmartyView::$smartyDirectory = 'vendor/smarty/smarty/distribution/libs';
SmartyView::$smartyCompileDirectory = 'app/templating/templates_compiled';
SmartyView::$smartyTemplatesDirectory = 'app/templating/templates';
SmartyView::$smartyExtensions = array(
    'vendor/slim/extras/Views/Extension/Smarty'
);


require 'app/Settings.class.php';
require 'app/MySQLWrapper.class.php';
require 'app/PDOWrapper.class.php';
require 'app/BadgeDao.class.php';
require 'app/OrganisationDao.class.php';
require 'app/UserDao.class.php';
require 'app/TaskStream.class.php';
require 'app/TaskDao.class.php';
require 'app/TagsDao.class.php';
require 'app/IO.class.php';
require 'app/TipSelector.class.php';
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

/*
*
*   Middleware - Used to authenticat users when entering restricted pages
*
*/
$authenticateForRole = function ( $role = 'translator' ) {
    return function () use ( $role ) {
        $app = Slim::getInstance();
        $user_dao = new UserDao();
        $current_user = $user_dao->getCurrentUser();
    
        if (!is_object($current_user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }
        else if ($user_dao->belongsToRole($current_user, $role) === false) { 
            $app->flash('error', 'Login required');
            $app->redirect($app->urlFor('login'));
        }   
    };
};

function authenticateUserForTask($request, $response, $route) {
    $app = Slim::getInstance();
    $params = $route->getParams();
    if($params !== NULL) {
        $task_id = $params['task_id'];
        $task_dao = new TaskDao();
        if($task_dao->taskIsClaimed($task_id)) {
            $user_dao = new UserDao();
            $current_user = $user_dao->getCurrentUser();
            if(!is_object($current_user)) {
                $app->flash('error', 'Login required to access page');
                $app->redirect($app->urlFor('login'));
            }
            if(!$task_dao->hasUserClaimedTask($current_user->getUserId(), $task_id)) {
                $app->flash('error', 'This task has been claimed by another user');
                $app->redirect($app->urlFor('home'));
            }
        }
        return true;
    } else {
        $app->flash('error', 'Unable to find task');
        $app->redirect($app->urlFor('home'));
    }
}


function authUserForOrg($request, $response, $route) {
    $params = $route->getParams();
    if($params !== NULL) {
        $org_id = $params['org_id'];
        $user_dao = new UserDao();
        $user = $user_dao->getCurrentUser();
        if(is_object($user)) {
            $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
            if(!is_null($user_orgs)) {
                if(in_array($org_id, $user_dao->findOrganisationsUserBelongsTo($user->getUserId()))) {
                    return true;
                }
            }
        }
    }

    $app = Slim::getInstance();
    $org_name = 'this organisation';
    if(isset($org_id)) {
        $org_dao = new OrganisationDao();
        $org = $org_dao->find(array('id' => $org_id));
        $org_name = $org->getName();
    }
    $app->flash('error', "You are not authorised to view this profile. Only members of ".$org_name." may view this page.");
    $app->redirect($app->urlFor('home'));
}

/*
*
*   Routing options - List all URLs here
*
*/
$app->get('/', function () use ($app) {
    $task_dao = new TaskDao;
    $app->view()->appendData(array(
        'top_tags' => $task_dao->getTopTags(30),
        'current_page' => 'home'
    ));

    $user_dao = new UserDao();
    $current_user = $user_dao->getCurrentUser();
    if($current_user == null) {
        $_SESSION['previous_page'] = 'home';

        if($tasks = TaskStream::getStream(10)) {
            $app->view()->setData('tasks', $tasks);
        }
    } else {
        if($tasks = TaskStream::getUserStream($current_user->getUserId(), 10)) {
            $app->view()->setData('tasks', $tasks);
        }

        $user_tags = $user_dao->getUserTags($current_user->getUserId());

        $app->view()->appendData(array(
            'user_tags' => $user_tags
        ));
    }

    $app->render('index.tpl');
})->name('home');

$app->get('/task/upload/:org_id', $authenticateForRole('organisation_member'), function ($org_id) use ($app) {
    $error_message = null;
    $field_name = 'new_task_file';

    $user_dao = new UserDao();
    $current_user = $user_dao->getCurrentUser();

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }
    
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
                'organisation_id'   => $org_id,
                'title'             => $_FILES[$field_name]['name']
            ));
            
            try {
                Upload::saveSubmittedFile($field_name, $task, $current_user->getUserId());
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
        'url_task_upload'       => $app->urlFor('task-upload', array('org_id' => $org_id)),
        'max_file_size_bytes'   => Upload::maxFileSizeBytes(),
        'max_file_size_mb'      => Upload::maxFileSizeMB(),
        'field_name'            => $field_name
    ));
    $app->render('task.upload.tpl');
})->via('GET','POST')->name('task-upload');

$app->get('/task/:task_id/upload-edited/', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $userDao = new UserDao();
    $currentUser = $userDao->getCurrentUser();

    if (!is_object($currentUser)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

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
            Upload::saveSubmittedFile($field_name, $task, $currentUser->getUserId());
        } catch (Exception  $e) {
            $error_message = 'File error: ' . $e->getMessage();
        }
    }

    if (is_null($error_message)) {
        $app->redirect($app->urlFor('task-uploaded-edit', array('task_id' => $task_id)));
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

$app->get('/task/:task_id/uploaded-edit/', 'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    $org_id = $task->getOrganisationId();

    $user_dao = new UserDao();
    $user = $user_dao->getCurrentUser();

    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    $org_dao = new OrganisationDao();
    $org = $org_dao->find(array('id' => $org_id));
    $org_name = $org->getName();

    $tip_selector = new TipSelector();
    $tip = $tip_selector->selectTip();

    $app->view()->appendData(array(
                            'org_name' => $org_name,
                            'tip' => $tip
    ));

    $app->render('task.uploaded-edit.tpl');
})->name('task-uploaded-edit');

$app->get('/task/view/:task_id/', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    $app->view()->setData('task', $task);

    $org_dao = new OrganisationDao();
    $org = $org_dao->find(array('id' => $task->getOrganisationId()));

    $app->view()->appendData(array(
            'org' => $org
    ));

    $app->render('task.view.tpl');
})->name('task-view');

$app->get('/task/alter/:task_id/', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));

    $app->view()->setData('task', $task);

    if(isValidPost($app)) {
        $post = (object)$app->request()->post();

        if($post->title != '') {
            $task->setTitle($post->title);
        }

        if($post->impact != '') {
            $task->setImpact($post->impact);
        }

        if($post->reference != '' && $post->reference != 'http://') {
            $task->setReferencePage($post->reference);
        }

        if($post->source != '') {
            $task->setSourceId(Languages::saveLanguage($post->source));
        }

        if($post->target != '') {
            $task->setTargetId(Languages::saveLanguage($post->target));
        }

        if($post->tags != '') {
            $task->setTags(Tags::separateTags($post->tags));
        }

        if($post->word_count != '') {
            $task->setWordCount($post->word_count);
        }

        $task_dao->save($task);
    }

    $languages = Languages::getLanguageList();
    $source_lang = Languages::languageNameFromId($task->getSourceId());
    $target_lang = Languages::languageNameFromId($task->getTargetId());


    $tags = $task->getTags();
    $tag_list = '';
    foreach($tags as $tag) {
        $tag_list .= $tag . ' ';
    }

    $app->view()->appendData(array(
            'languages'     => $languages,
            'source_lang'   => $source_lang,
            'target_lang'   => $target_lang,
            'tag_list'      => $tag_list
    ));

    $app->render('task.alter.tpl');
})->via('POST')->name('task-alter');

$app->get('/task/describe/:task_id/', $authenticateForRole('organisation_member'),
            'authenticateUserForTask', function ($task_id) use ($app) {
    $error       = null;
    $title_err   = null;
    $word_count_err = null;
    $task_dao    = new TaskDao();
    $task        = $task_dao->find(array('task_id' => $task_id));
    $language_list = Languages::getLanguageList();

    if (!is_object($task)) {
        $app->notFound();
    }

    if (isValidPost($app)) {
        $post = (object)$app->request()->post();

        if (!empty($post->source)) {
            $source_id = Languages::saveLanguage($post->source);
            $task->setSourceId($source_id);
        }

        if($post->title != '') {
            $task->setTitle($post->title);
        } else {
            $title_err = "Title cannot be empty";
        }

        if($post->impact != '') {
            $task->setImpact($post->impact);
        }

        if($post->reference != '' && $post->reference != "http://") {
            $task->setReferencePage($post->reference);
        }

        $task->setTags(Tags::separateTags($post->tags));
        if($post->word_count != '') {
            $task->setWordCount($post->word_count);
        } else {
            $word_count_err = "Word Count cannot be blank";
        }

        $language_list = array();
        $target_count = 0;
        $target_val = $app->request()->post("target_$target_count");
        while(isset($target_val)) {
            if(!in_array($target_val, $language_list)) {
                $language_list[] = $target_val;
            }
            $target_count += 1;
            $target_val = $app->request()->post("target_$target_count");
        }


        if(count($language_list) > 1) {
            foreach($language_list as $language) {
                if($language == $language_list[0]) {   //if it is the first language add it to this task
                    $target_id = Languages::saveLanguage($language_list[0]);
                    $task->setTargetId($target_id);
                    $task_dao->save($task);
                } else {
                    $language_id = Languages::saveLanguage($language);
                    $task_dao->duplicateTaskForTarget($task, $language_id);
                }
            }
        } else {
            $target_id = Languages::saveLanguage($language_list[0]);
            $task->setTargetId($target_id);
            $task_dao->save($task);
        }

        if (is_null($error) && is_null($title_err) && is_null($word_count_err)) {
            $app->redirect($app->urlFor('task-uploaded', array('task_id' => $task_id)));
        }
    }

    $extra_scripts = "
    <script language='javascript'>
    fields = 0;
    function addInput() {
        if (fields < 10) {
            document.getElementById('text').innerHTML += '<label for=\"target_' + (fields + 1) + '\">To language</label>';
            document.getElementById('text').innerHTML += '<select name=\"target_' + (fields + 1) + '\" id=\"target_' + (fields + 1) + '\">';
            document.getElementById('text').innerHTML += '</select>';
            var sel = document.getElementById('target_' + (fields + 1));
            var options = sel.options;
            var langs = ".json_encode($language_list).";
            for (language in langs) {
                var option = document.createElement('OPTION');
                option.appendChild(document.createTextNode(langs[language]));
                option.setAttribute('value', langs[language]);
                sel.appendChild(option);
            }
            sel.options.selectedIndex=0;
            fields += 1;
        } else if (fields == 10) {
            document.getElementById('text').innerHTML += '<br /><div class=\"alert alert-error\">';
                document.getElementById('text').innerHTML += 'Only ' + fields + ' upload fields allowed.';
            document.getElementById('text').innerHTML += '</div>';
            fields++;
            document.form.add.disabled=true;
        }
    }
    </script>";

    $app->view()->appendData(array(
        'error'             => $error,
        'title_error'       => $title_err,
        'word_count_err'    => $word_count_err,
        'url_task_describe' => $app->urlFor('task-describe', array('task_id' => $task_id)),
        'task'              => $task,
        'languages'         => $language_list,
        'extra_scripts'     => $extra_scripts
    ));
    
    $app->render('task.describe.tpl');
})->via('GET','POST')->name('task-describe');

$app->get('/task/id/:task_id/uploaded/', $authenticateForRole('organisation_member'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));

    $user_dao = new UserDao();
    $user = $user_dao->getCurrentUser();

    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    $org_id = $task->getOrganisationId();
    $app->view()->appendData(array(
            'org_id' => $org_id
    ));

    $app->render('task.uploaded.tpl');
})->name('task-uploaded');

$app->get('/task/id/:task_id/', 'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $org_dao = new OrganisationDao();
    $org = $org_dao->find($task->getOrganisationId());

    $app->view()->setData('task', $task);
    $app->view()->appendData(array('org' => $org));

    if ($task_file_info = $task_dao->getTaskFileInfo($task)) {
        $app->view()->appendData(array(
            'task_file_info' => $task_file_info,
            'latest_version' => $task_dao->getLatestFileVersion($task)
        ));
    }
    
    $task_file_info = $task_dao->getTaskFileInfo($task, 0);
    $file_path = Upload::absoluteFilePathForUpload($task, 0, $task_file_info['filename']);
    $searchStart = strlen($file_path) - strrpos($file_path, $task_file_info['filename']);
    $appPos = strrpos($file_path, "app", $searchStart);
    $file_path = "http://".$_SERVER["HTTP_HOST"].$app->urlFor('home').substr($file_path, $appPos);
    $app->view()->appendData(array(
        'file_preview_path' => $file_path,
        'file_name' => $task_file_info['filename']
    ));

    if ($task_dao->taskIsClaimed($task->getTaskId())) {
        $app->view()->appendData(array(
            'task_is_claimed' => true
        ));
        $user_dao = new UserDao();
        if ($current_user = $user_dao->getCurrentUser()) {
            if ($task_dao->hasUserClaimedTask($current_user->getUserId(), $task->getTaskId())) {
                $app->view()->appendData(array(
                    'this_user_has_claimed_this_task' => true
                ));
                if($task_dao->hasBeenUploaded($task->getTaskId(), $current_user->getUserId())) {
                    $app->view()->appendData(array(
                        'file_previously_uploaded' => true
                    ));

                }
            }
        }
    }

    if(!UserDao::isLoggedIn()) {
        $_SESSION['previous_page'] = 'task';
        $_SESSION['old_page_vars'] = array("task_id" => $task_id);
    }

    $app->view()->appendData(array(
        'max_file_size' => Upload::maxFileSizeMB(),
        'body_class'    => 'task_page'
    ));

    $app->render('task.tpl');
})->name('task');

$app->get('/task/id/:task_id/download-preview/', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    $user_dao = new UserDao();
    $user = $user_dao->getCurrentUser();

    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $app->view()->setData('task', $task);
    $app->render('task.download-preview.tpl');
})->name('download-task-preview');

$app->get('/task/id/:task_id/download-file/v/:version/', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id, $version) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }

    $user_dao = new UserDao();
    $user = $user_dao->getCurrentUser();

    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
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

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    $task_dao->claimTask($task, $current_user);
    Notify::notifyUserClaimedTask($current_user, $task);   

    $app->redirect($app->urlFor('task-claimed', array(
        'task_id' => $task_id
    )));

})->name('claim-task');

$app->get('/task/id/:task_id/claimed', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao();

    $task = $task_dao->find(array('task_id' => $task_id));
    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }
    $user_dao           = new UserDao();
    $current_user       = $user_dao->getCurrentUser();

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }
    $app->view()->setData('task', $task);
    $app->render('task.claimed.tpl');
})->name('task-claimed');

/*
 * Claim a task after downloading it
 */
$app->get('/task/claim/:task_id', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao();
    $task = $task_dao->find(array('task_id' => $task_id));
    if(!is_object($task)) {
        header ('HTTP/1.0 404 Not Found');
        die;
    }
    $user_dao           = new UserDao();
    $current_user       = $user_dao->getCurrentUser();

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }
    $app->view()->setData('task', $task);

    $app->render('task.claim.tpl');
})->name('task-claim-page');

$app->get('/task/id/:task_id/download-file/', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }
    $user_dao           = new UserDao();
    $current_user       = $user_dao->getCurrentUser();

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    $app->redirect($app->urlFor('download-task-version', array(
        'task_id' => $task_id,
        'version' => 0
    )));

})->name('download-task');

$app->get('/task/id/:task_id/mark-archived/', $authenticateForRole('organisation_member'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }
    $user_dao           = new UserDao();
    $current_user       = $user_dao->getCurrentUser();

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    $task_dao->moveToArchive($task);

    $app->redirect($ref = $app->request()->getReferrer());
})->name('archive-task');

$app->get('/task/id/:task_id/download-task-latest-file/', $authenticateForRole('translator'), 
            'authenticateUserForTask', function ($task_id) use ($app) {
    $task_dao = new TaskDao;
    $task = $task_dao->find(array('task_id' => $task_id));

    if (!is_object($task)) {
        header('HTTP/1.0 404 Not Found');
        die;
    }
    $user_dao           = new UserDao();
    $current_user       = $user_dao->getCurrentUser();

    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
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
            if(in_array($label, $user_tags)) {
                $app->view()->appendData(array(
                    'subscribed' => true
                ));
            }
        }
    }

    $app->view()->appendData(array(
        'tag' => $label,
        'top_tags' => $task_dao->getTopTags(30),
    ));
    $app->render('tag.tpl');
})->via("POST")->name('tag-details');

$app->get("/tag/:label/:subscribe", function ($label, $subscribe) use ($app) {
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

})->name('tag-subscribe');

$app->get('/all/tags', function () use ($app) {
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
})->name('tags-list');

$app->get('/login', function () use ($app) {
    $error = null;
    $tempSettings=new Settings();
    $openid = new LightOpenID($tempSettings->get("site.url"));
    $use_openid = $tempSettings->get("site.openid");
    $app->view()->setData('openid', $use_openid);
    if(isset($use_openid)) {
        if($use_openid == 'y' || $use_openid == 'h') {
            $extra_scripts = "
                <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/jquery-1.2.6.min.js\"></script>
                <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-jquery.js\"></script>
                <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-en.js\"></script>
                <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/openid.css\" />";
            $app->view()->appendData(array('extra_scripts' => $extra_scripts));
        }
    }

    try {
        $user_dao = new UserDao();
        if (isValidPost($app)){
            $post = (object)$app->request()->post();
            $user_dao->login($post->email, $post->password);
        } elseif($app->request()->isPost()||$openid->mode){
            $user_dao->OpenIDLogin($openid,$app);
        } else{
             $app->render('login.tpl');
             return;
        }               
        $app->redirect($app->urlFor("home"));
    } catch (InvalidArgumentException $e) {
        $error = '<p>Unable to log in. Please check your email and password.';
        $error .= ' <a href="' . $app->urlFor('login') . '">Try logging in again</a>';
        $error .= ' or <a href="'.$app->urlFor('register').'">register</a> for an account.</p>';
        $error .= '<p>System error: <em>' . $e->getMessage() .'</em></p>';

        $app->flash('error', $error);
        $app->redirect($app->urlFor('login'));
        echo $error;
    }
    
})->via('GET','POST')->name('login');

$app->get('/logout', function () use ($app) {
    $user_dao = new UserDao();
    $user_dao->logout();
    $app->redirect($app->urlFor('home'));
})->name('logout');

$app->get('/register', function () use ($app) {
    $tempSettings=new Settings();
    $app->view()->setData('openid',$tempSettings->get("site.openid"));
    $error = null;
    $warning = null;
    if (isValidPost($app)) {
        $post = (object)$app->request()->post();
        $user_dao = new UserDao();
        if (!User::isValidEmail($post->email)) {
            $error = 'The email address you entered was not valid. Please cheak for typos and try again.';
        }
        else if (!User::isValidPassword($post->password)) {
            $error = 'You didn\'t enter a password. Please try again.';
        }
        else if (is_object($user_dao->find(array('email' => $post->email)))) {
            $warning = 'You have already created an account. <a href="' . $app->urlFor('login') . '">Please log in.</a>';
        }

        if (is_null($error) && is_null($warning)) {
            if ($user = $user_dao->create($post->email, $post->password)) {
                if ($user_dao->login($user->getEmail(), $post->password)) {

                    $badge_dao = new BadgeDao();
                    $badge = $badge_dao->find(array('badge_id' => Badge::REGISTERED));
                    $badge_dao->assignBadge($user, $badge);

                    if(isset($_SESSION['previous_page'])) {
                        if(isset($_SESSION['old_page_vars'])) {
                            $app->redirect($app->urlFor($_SESSION['previous_page'], $_SESSION['old_page_vars']));
                        } else {
                            $app->redirect($app->urlFor($_SESSION['previous_page']));
                        }
                    }
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
    $org_dao            = new OrganisationDao();
    $current_user       = $user_dao->getCurrentUser();
    if (!is_object($current_user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }
        $my_organisations   = $user_dao->findOrganisationsUserBelongsTo($current_user->getUserId());

    $org_tasks = array();
    $orgs = array();
    foreach($my_organisations as $org_id) {
        $org = $org_dao->find(array('id' => $org_id));
        $my_org_tasks = $task_dao->findTasks(array('organisation_ids' => $org_id));
        $org_tasks[$org->getId()] = $my_org_tasks;
        $orgs[$org->getId()] = $org;
    }
    
    if(count($org_tasks) > 0) {
        $app->view()->appendData(array(
                'org_tasks' => $org_tasks,
                'orgs' => $orgs,
                'task_dao' => $task_dao
        ));
    }

    $app->view()->appendData(array(
        'current_page'  => 'client-dashboard'
    ));
    $app->render('client.dashboard.tpl');
})->name('client-dashboard');

$app->get('/profile/:user_id', function ($user_id) use ($app) {
    $user_dao = new UserDao();
    $user = $user_dao->find(array('user_id' => $user_id));
    
    $task_dao = new TaskDao();
    $activeJobs = $task_dao->getUserTasks($user, 10);

    $archivedJobs = $task_dao->getUserArchivedTasks($user, 10);

    $user_tags = $user_dao->getUserTags($user->getUserId());

    $badge_dao = new BadgeDao();
    $org_dao = new OrganisationDao();
    
    $orgIds = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
    $orgList = array();
    
    if(count($orgIds) > 0) {
        foreach ($orgIds as $orgId) {
            $orgList[] = $org_dao->find(array('id' => $orgId));
        }
    }
    
    $badgeIds = $user_dao->getUserBadges($user);
    $badges = array();
    $i = 0;
    if(count($badgeIds) > 0) {
        foreach($badgeIds as $badge) {
            $badges[$i] = $badge_dao->find(array('badge_id' => $badge['badge_id']));
            $i++;
        }
    }
    
    $app->view()->setData('orgList',  $orgList);
    $app->view()->appendData(array('badges' => $badges,
                                    'current_page' => 'user-profile',
                                    'activeJobs' => $activeJobs,
                                    'archivedJobs' => $archivedJobs,
                                    'user_tags' => $user_tags,
                                    'this_user' => $user
    ));

    if($user_dao->getCurrentUser()->getUserId() === $user_id) {
        $app->view()->appendData(array('private_access' => true));
    }

    $app->render('user-public-profile.tpl');
})->name('user-public-profile');

$app->get('/profile', function () use ($app) {
    $user_dao = new UserDao();
    $user = $user_dao->getCurrentUser();
    $languages = Languages::getLanguageList();

    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }

    if($app->request()->isPost()) {
        $displayName = $app->request()->post('name');
        if($displayName != NULL) {
            $user->setDisplayName($displayName);
        }
 
        $userBio = $app->request()->post('bio');
        if($userBio != NULL) {
            $user->setBiography($userBio);
        }
 
        $nativeLang = $app->request()->post('nLanguage');
        if($nativeLang != NULL) {
            $user->setNativeLanguage($nativeLang);
            //assign a badge
            $badge_dao = new BadgeDao();
            $badge = $badge_dao->find(array('badge_id' => Badge::NATIVE_LANGUAGE));
            $badge_dao->assignBadge($user, $badge);
        }
        $user_dao->save($user);

        if($user->getDisplayName() != '' && $user->getBiography() != ''
                && $user->getNativeLanguage() != '') {
            $badge_dao = new BadgeDao();
            $badge = $badge_dao->find(array('badge_id' => Badge::PROFILE_FILLER));
            $badge_dao->assignBadge($user, $badge);
        }

        $app->redirect($app->urlFor('user-public-profile', array('user_id' => $user->getUserId())));
    }
    
    $app->view()->setData('languages',  $languages);

    $app->render('user-private-profile.tpl');
})->via('POST')->name('user-private-profile');

$app->get('/tasks/active/p/:page_no', function ($page_no) use ($app) {
    $task_dao = new TaskDao();
    $user_dao = new UserDao();

    $user = $user_dao->getCurrentUser();
    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }
    $activeTasks = $task_dao->getUserTasks($user);

    $tasks_per_page = 10;
    $total_pages = ceil(count($activeTasks) / $tasks_per_page);

    if($page_no < 1) {
        $page_no = 1;
    } elseif($page_no > $total_pages) {
        $page_no = $total_pages;
    }

    $top = (($page_no - 1) * $tasks_per_page);
    $bottom = $top + $tasks_per_page - 1;

    if($top < 0) {
        $top = 0;
    } elseif($top > count($activeTasks) - 1) {
        $top = count($activeTasks) - 1;
    }

    if($bottom < 0) {
        $bottom = 0;
    } elseif($bottom > count($activeTasks) - 1) {
        $bottom = count($activeTasks) - 1;
    }


    $app->view()->setData('active_tasks', $activeTasks);
    $app->view()->appendData(array(
                'page_no' => $page_no,
                'last' => $total_pages,
                'top' => $top,
                'bottom' => $bottom
    ));

    $app->render('active-tasks.tpl');
})->name('active-tasks');

$app->get('/tasks/archive/p/:page_no', function ($page_no) use ($app) {
    $user_dao = new UserDao();
    $task_dao = new TaskDao();

    $user = $user_dao->getCurrentUser();
    if (!is_object($user)) {
        $app->flash('error', 'Login required to access page');
        $app->redirect($app->urlFor('login'));
    }
    $archived_tasks = $task_dao->getUserArchivedTasks($user);

    $tasks_per_page = 10;
    $total_pages = ceil(count($archived_tasks) / $tasks_per_page);

    if($page_no < 1) {
        $page_no = 1;
    } elseif($page_no > $total_pages) {
        $page_no = $total_pages;
    }

    $top = (($page_no - 1) * $tasks_per_page);
    $bottom = $top + $tasks_per_page - 1;

    if($top < 0) {
        $top = 0;
    } elseif($top > count($archived_tasks) - 1) {
        $top = count($archived_tasks) - 1;
    }

    if($bottom < 0) {
        $bottom = 0;
    } elseif($bottom > count($archived_tasks) - 1) {
        $bottom = count($archived_tasks) - 1;
    }


    $app->view()->setData('archived_tasks', $archived_tasks);
    $app->view()->appendData(array(
                'page_no' => $page_no,
                'last' => $total_pages,
                'top' => $top,
                'bottom' => $bottom
    ));
    $app->render('archived-tasks.tpl');
})->name('archived-tasks');

$app->get('/badge/list', function () use ($app) {
    $badge_dao = new BadgeDao();
    $badgeList = $badge_dao->getAllBadges();

    $app->view()->setData('current_page', 'badge-list');
    $app->view()->appendData(array('badgeList' => $badgeList));

    $app->render('badge-list.tpl');
})->name('badge-list');

$app->get('/org/create/badge/:org_id/', function ($org_id) use ($app) {
    if(isValidPost($app)) {
        $post = (object)$app->request()->post();

        if($post->title == '' || $post->description == '') {
            $app->flash('error', "All fields must be filled out");
        } else {
            $params = array();
            $params['title'] = $post->title;
            $params['description'] = $post->description;
            $params['owner_id'] = $org_id;

            $badge_dao = new BadgeDao();
            $badge = new Badge($params);
            $badge_dao->save($badge);
            $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org_id)));
        }
    }

    $app->view()->setData('org_id', $org_id);

    $app->render('org.create-badge.tpl');
})->via('POST')->name('org-create-badge');

$app->get('/org/:org_id/assign/:badge_id/', function ($org_id, $badge_id) use ($app) {
    $badge_dao = new BadgeDao();
    $badge = $badge_dao->find(array('badge_id' => $badge_id));

    $app->view()->setData('badge', $badge);
    $app->view()->appendData(array(
            'org_id' => $org_id
    ));

    if($app->request()->isPost()) {
        $post = (object) $app->request()->post();

        if(User::isValidEmail($post->email)) {
            $user_dao = new UserDao();
            $user = $user_dao->find(array('email' => $post->email));

            if(!is_null($user)) {
                $user_badges = $user_dao->getUserBadges($user);
                $badge_ids = array();
                if(count($user_badges) > 0) {
                    foreach($user_badges as $badge_tmp) {
                        $badge_ids[] = $badge_tmp['badge_id'];
                    }
                }

                if(!in_array($badge_id, $badge_ids)) {
                    $badge_dao->assignBadge($user, $badge);
    
                    $user_name = '';
                    if($user->getDisplayName() != '') {
                        $user_name = $user->getDisplayName();
                    } else {
                        $user_name = $user->getEmail();
                    }

                    $app->flash('info', "Successfully Assigned Badge \"".$badge->getTitle()."\" to user $user_name");
                    $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org_id)));
                } else {
                    $app->flashNow('error', 'The user '.$post->email.' already has that badge');
                }
            } else {
                $app->flashNow('error', 
                    'The email address '.$post->email.' is not registered on the system. 
                    Are you using the correct email address?'
                );
            }
        } else {
            $app->flashNow('error', "You did not enter a valid email address");
        }
    }

    $app->render('org.assign-badge.tpl');
})->via("POST")->name('org-assign-badge');

$app->get('/org/profile/:org_id', function ($org_id) use ($app) {
    $org_dao = new OrganisationDao();
    $org = $org_dao->find(array('id' => $org_id));

    $user_dao = new UserDao();
    $currentUser = $user_dao->getCurrentUser();

    $badge_dao = new BadgeDao();
    $org_badges = $badge_dao->getOrgBadges($org_id);

    $org_member_ids = $org_dao->getOrgMembers($org_id);

    $org_members = array();
    if(count($org_member_ids) > 0) {
        foreach($org_member_ids as $org_mem) {
            $org_members[] = $org_mem['user_id'];
        }
    }

    $app->view()->setData('current_page', 'org-public-profile');
    $app->view()->appendData(array('org' => $org,
                                    'org_members' => $org_members,
                                    'org_badges' => $org_badges
    ));

    $app->render('org-public-profile.tpl');
})->via('POST')->name('org-public-profile');

$app->get('/org/private/:org_id', 'authUserForOrg', function ($org_id) use ($app) {
    $org_dao = new OrganisationDao();
    $org = $org_dao->find(array('id' => $org_id));
   
    if($app->request()->isPost()) {
        $name = $app->request()->post('name');
        if($name != NULL) {
            $org->setName($name);
        }

        $home_page = $app->request()->post('home_page');
        if($home_page != NULL) {
            $org->setHomePage($home_page);
        }

        $bio = $app->request()->post('bio');
        if($bio != NULL) {
            $org->setBiography($bio);
        }

        $org_dao->save($org);
        $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org->getId())));
    }
    
    $app->view()->setData('org', $org);

    $app->render('org-private-profile.tpl');
})->via('POST')->name('org-private-profile');

$app->get('/org/request/queue/:org_id', 'authUserForOrg', function ($org_id) use ($app) {
    $org_dao = new OrganisationDao();
    $org = $org_dao->find(array('id' => $org_id));

    $user_dao = new UserDao();

    $requests = $org_dao->getMembershipRequests($org_id);
    $user_list = array();
    if(count($requests) > 0) {
        foreach($requests as $request) {
            $user_list[] = $user_dao->find(array('user_id' => $request['user_id']));
        }
    }

    $app->view()->setData('org', $org);
    $app->view()->appendData(array('user_list' => $user_list));

    $app->render('org.request_queue.tpl');
})->name('org-request-queue');

$app->get('/org/:org_id/request/:user_id/:accept', function ($org_id, $user_id, $accept) use ($app) {
    $org_dao = new OrganisationDao();
    if($accept == "true") {
        echo "<p>Accepting Request</p>";
        $org_dao->acceptMemRequest($org_id, $user_id);
    } else {
        echo "<p>Refusing Request</p>";
        $org_dao->refuseMemRequest($org_id, $user_id);
    }
    
    $app->redirect($app->urlFor('org-request-queue', array('org_id' => $org_id)));
})->name('org-process-request');

$app->get('/org/request/:org_id', function ($org_id) use ($app) {
    $user_dao = new UserDao();
    $user = $user_dao->getCurrentUser();
    $user_orgs = $user_dao->findOrganisationsUserBelongsTo($user->getUserId());
    if(is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
        $org_dao = new OrganisationDao();
        if($org_dao->requestMembership($user->getUserId(), $org_id)) {
            $app->flash("success", "Successfully requested membership.");
        } else {
            $app->flash("error", "You have already sent a membership request to this Organisation");
        }
    } else {
        $app->flash("error", "You are already a member of this organisation");
    }
    $app->redirect($app->urlFor('org-public-profile', array('org_id' => $org_id)));
})->name('org-request-membership');


function isValidPost(&$app) {
    return $app->request()->isPost() && sizeof($app->request()->post()) > 2;
}

/**
 * Set up application objects
 * 
 * Given that we don't have object factories implemented, we'll initialise them directly here.
 */
$app->hook('slim.before', function () use ($app) {
    $user_dao = new UserDao();
    if ($current_user = $user_dao->getCurrentUser()) {
        $app->view()->appendData(array('user' => $current_user));
        if ($user_dao->belongsToRole($current_user, 'organisation_member')) {
            $app->view()->appendData(array(
                'user_is_organisation_member' => true,
                'user_organisations' => $user_dao->findOrganisationsUserBelongsTo($current_user->getUserId())
            ));
        }
    }
});

$app->run();
