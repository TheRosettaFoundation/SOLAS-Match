<?php

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
require_once __DIR__.'/../../Common/protobufs/emails/FeedbackEmail.php';

class Notify 
{
    public static function sendOrgFeedback($task, $user, $feedback)
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $messageType = new FeedbackEmail();
            $messageType->taskId = $task->getId();
            $messageType->userId = $user->getUserId();
            $messageType->feedback = $feedback;
            $message = $messagingClient->createMessageFromProto($messageType);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                    $messagingClient->FeedbackEmailTopic);
        }
    }

    public static function notifyUserClaimedTask($user, $task) 
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new UserTaskClaim();
            $message_type->user_id = $user->getUserId();
            $message_type->task_id = $task->getId();
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                    $messagingClient->UserTaskClaimTopic);
        } else {
            echo "<p>Failed to initialize messaging client</p>";
        }
    }

    public static function sendPasswordResetEmail($user_id)
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new PasswordResetEmail();
            $message_type->user_id = $user_id;
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                    $messagingClient->PasswordResetTopic);
        } else {
            echo "<p>Failed to initialize messaging client</p>";
        }
    }

    public static function notifyUserOrgMembershipRequest($user_id, $org_id, $accepted)
    {
        $org_dao = new OrganisationDao();
        $org = $org_dao->getOrg($org_id, null, null, null)[0];

        $user_dao = new UserDao();
        $user = $user_dao->find(array('user_id' => $user_id));

        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            if ($accepted) {
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
    }

    public static function sendEmailNotifications($taskId, $notificationType)
    {
        $app = Slim::getInstance();

        $task_dao = new TaskDao();
        $subscribed_users = $task_dao->getSubscribedUsers($taskId);

        if (count($subscribed_users) > 0) {
            $messagingClient = new MessagingClient();
            if ($messagingClient->init()) {
                switch ($notificationType) {
                        
                    case NotificationTypes::ARCHIVE:
                        $message_type = new TaskArchived();
                        $message_type->task_id = $taskId;
                        foreach ($subscribed_users as $user) {
                            $message_type->user_id = $user->getUserId();
                            $message = $messagingClient->createMessageFromProto($message_type);
                            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                    $messagingClient->TaskArchivedTopic);
                        }
                        break;
                            
                    case NotificationTypes::CLAIM:
                        $message_type = new TaskClaimed();
                        $message_type->task_id = $taskId;
                        $translator = $task_dao->getTaskTranslator($taskId);
                        $message_type->translator_id = $translator->getUserId();
                        foreach ($subscribed_users as $user) {
                            $message_type->user_id = $user->getUserId();
                            $message = $messagingClient->createMessageFromProto($message_type);
                            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                    $messagingClient->TaskClaimedTopic);
                        }
                        break;
                            
                    case NotificationTypes::UPLOAD:
                        $message_type = new TaskTranslationUploaded();
                        $message_type->task_id = $taskId;
                        $translator = $task_dao->getTaskTranslator($taskId);
                        $message_type->translator_id = $translator->getUserId();
                        foreach ($subscribed_users as $user) {
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
        }
    }
}
