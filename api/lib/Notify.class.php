<?php
require_once 'Email.class.php';
require_once 'HTTP/Request2.php';
require_once __DIR__.'/MessagingClient.class.php';
require_once __DIR__.'/../../Common/Settings.class.php';
require_once __DIR__."/../vendor/autoload.php";

\DrSlump\Protobuf::autoload();

require_once __DIR__.'/../../Common/protobufs/emails/EmailMessage.php';
require_once __DIR__.'/../../Common/protobufs/emails/UserTaskClaim.php';
require_once __DIR__.'/../../Common/protobufs/emails/PasswordResetEmail.php';
require_once __DIR__.'/../../Common/protobufs/emails/OrgMembershipAccepted.php';
require_once __DIR__.'/../../Common/protobufs/emails/OrgMembershipRefused.php';
require_once __DIR__.'/../../Common/protobufs/emails/TaskArchived.php';
require_once __DIR__.'/../../Common/protobufs/emails/TaskClaimed.php';
require_once __DIR__.'/../../Common/protobufs/emails/TaskTranslationUploaded.php';

class Notify 
{
    public static function notifyUserClaimedTask($user, $task) 
    {
        $settings = new Settings();
        $use_backend = $settings->get('site.backend');
        if(strcasecmp($use_backend, "y") == 0) {
            $messagingClient = new MessagingClient();
            if($messagingClient->init()) {
                $message_type = new UserTaskClaim();
                $message_type->user_id = $user->getUserId();
                $message_type->task_id = $task->getId();
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                        $messagingClient->UserTaskClaimTopic);
            } else {
                echo "<p>Failed to initialize messaging client</p>";
            }
        } else {
        	$app 		= Slim::getInstance();
            $settings   = new Settings();
            $task_url 	= $settings->get('site.url') .  "/task/id/{$task->getId()}/";

            $app->view()->appendData(array(
                    'site_name' => $settings->get('site.name'),
                    'task_url' => $task_url
            ));
            $email_subject = "You have claimed a volunteer translation task, here's how to upload your translated file";
            $email_body = $app->view()->fetch('email.claimed-task.tpl');
            $user_email = $user->getEmail();

            Email::sendEmail($user_email, $email_subject, $email_body);
        }
	}

    public static function sendPasswordResetEmail($uid, $user_id)
    {
        $settings = new Settings();
        $userDao = new UserDao();
        $user = $userDao->find(array('user_id' => $user_id));

        $use_backend = $settings->get('site.backend');
        if(strcasecmp($use_backend, "y") == 0) {
            $messagingClient = new MessagingClient();
            if($messagingClient->init()) {
                $message_type = new PasswordResetEmail();
                $message_type->user_id = $user->getUserId();
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                        $messagingClient->PasswordResetTopic);
            } else {
                echo "<p>Failed to initialize messaging client</p>";
            }
        } else {
            $app = Slim::getInstance();

            $settings = new Settings();
            $site_url = $settings->get('site.url');
            $site_url .= "/$uid/password/reset";


            $app->view()->setData('site_url', $site_url);
            $app->view()->appendData(array('user' => $user));

            $user_email = $user->getEmail();
            $email_subject = "SOLAS Match: Password Reset";
            $email_body = $app->view()->fetch('email.password-reset.tpl');

            Email::sendEmail($user_email, $email_subject, $email_body);
        }
    }

    public static function notifyUserOrgMembershipRequest($user_id, $org_id, $accepted)
    {
        $org_dao = new OrganisationDao();
        $org = $org_dao->find(array('id' => $org_id));

        $user_dao = new UserDao();
        $user = $user_dao->find(array('user_id' => $user_id));

        $settings = new Settings();
        $use_backend = $settings->get('site.backend');
        if(strcasecmp($use_backend, "y") == 0) {
            $messagingClient = new MessagingClient();
            if($messagingClient->init()) {
                if($accepted) {
                    $message_type = new OrgMembershipAccepted();
                    $message_type->user_id = $user->getUserId();
                    $message_type->org_id = $org->getId();
                    $message = $messagingClient->createMessageFromProto($message_type);
                    $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                            $messagingClient->OrgMembershipAcceptedTopic);
                } else {
                    $message_type = new OrgMembershipRefused();
                    $message_type->user_id = $user->getUserId();
                    $message_type->org_id = $org->getId();
                    $message = $messagingClient->createMessageFromProto($message_type);
                    $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                            $messagingClient->OrgMembershipRefusedTopic);
                }
            } else {
                echo "<p>Failed to initialize messaging client</p>";
            }
        } else {
            $app = Slim::getInstance();

            $settings = new Settings();
            $site_url = $settings->get('site.url');

            $app->view()->setData('site_url', $site_url);
            $app->view()->appendData(array(
                            'user' => $user,
                            'org' => $org
            ));

            $user_email = $user->getEmail();
            $email_subject = "SOLAS Match: Organisation Membership Request Feedback";
            if($accepted) {
                $email_body = $app->view()->fetch('email.org-membership-accepted.tpl');
            } else {
                $email_body = $app->view()->fetch("email.org-membership-refused.tpl");
            }

            Email::sendEmail($user_email, $email_subject, $email_body);
        }
    }

    public static function sendEmailNotifications($task, $notificationType)
    {
        $app = Slim::getInstance();

        $settings = new Settings();
        $task_dao = new TaskDao();
        $subscribed_users = $task_dao->getSubscribedUsers($task->getId());

        if(count($subscribed_users) > 0) {

            $use_backend = $settings->get('site.backend');
            if(strcasecmp($use_backend, "y") == 0) {
                $messagingClient = new MessagingClient();
                if($messagingClient->init()) {
                    switch($notificationType) {
                        case NotificationTypes::ARCHIVE:
                            $message_type = new TaskArchived();
                            $message_type->task_id = $task->getId();
                            foreach($subscribed_users as $user) {
                                $message_type->user_id = $user->getUserId();
                                $message = $messagingClient->createMessageFromProto($message_type);
                                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                        $messagingClient->TaskArchivedTopic);
                            }
                            break;
                        case NotificationTypes::CLAIM:
                            $message_type = new TaskClaimed();
                            $message_type->task_id = $task->getId();
                            $translator = $task_dao->getTaskTranslator($task->getId());
                            $message_type->translator_id = $translator->getUserId();
                            foreach($subscribed_users as $user) {
                                $message_type->user_id = $user->getUserId();
                                $message = $messagingClient->createMessageFromProto($message_type);
                                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                        $messagingClient->TaskClaimedTopic);
                            }
                            break;
                        case NotificationTypes::UPLOAD:
                            $message_type = new TaskTranslationUploaded();
                            $message_type->task_id = $task->getId();
                            $translator = $task_dao->getTaskTranslator($task->getId());
                            $message_type->translator_id = $translator->getUserId();
                            foreach($subscribed_users as $user) {
                                $message_type->user_id = $user->getUserId();
                                $message = $messagingClient->createMessageFromProto($message_type);
                                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                        $messagingClient->TaskTranslationUploadedTopic);
                            }
                            break;
                        default:
                            echo "<p>Invalid email type</p>";
                    }
                } else {
                    echo "<p>Failed to initialize messaging client</p>";
                }
            } else {
                $app = Slim::getInstance();

                $translator = null;
                if($task_dao->taskIsClaimed($task->getId())) {
                    $translator = $task_dao->getTaskTranslator($task->getId());
                }

                $site_url = $settings->get('site.url');
    
                $org_dao = new OrganisationDao();
                $org = $org_dao->find(array('id' => $task->getOrgId()));

                if(count($subscribed_users) > 0) {
                    foreach($subscribed_users as $user) {
                        $app->view()->setData('user', $user);
                        $app->view()->appendData(array(
                                'task' => $task,
                                'translator' => $translator,
                                'org' => $org,
                                'site_url' => $site_url
                        ));
                        $email_subject = "A task's status has changed on SOLAS Match";
                        $email_body = $app->view()->fetch($notificationType);
                        $user_email = $user->getEmail();

                        Email::sendEmail($user_email, $email_subject, $email_body);
                    }
                }
            }
        }
    }
}
