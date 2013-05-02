<?php

require_once "HTTP/Request2.php";
require_once __DIR__."/MessagingClient.class.php";
require_once __DIR__."/../../Common/Settings.class.php";
require_once __DIR__."/../vendor/autoload.php";

\DrSlump\Protobuf::autoload();

require_once __DIR__."/../../Common/protobufs/emails/EmailMessage.php";
require_once __DIR__."/../../Common/protobufs/emails/UserTaskClaim.php";
require_once __DIR__."/../../Common/protobufs/emails/PasswordResetEmail.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgMembershipAccepted.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgMembershipRefused.php";
require_once __DIR__."/../../Common/protobufs/emails/TaskArchived.php";
require_once __DIR__."/../../Common/protobufs/emails/TaskClaimed.php";
require_once __DIR__."/../../Common/protobufs/emails/TaskTranslationUploaded.php";
require_once __DIR__."/../../Common/protobufs/emails/FeedbackEmail.php";
require_once __DIR__."/../../Common/protobufs/emails/EmailVerification.php";

class Notify 
{
    public static function sendEmailVerification($userId)
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $messageProto = new EmailVerification();
            $messageProto->setUserId($userId);
            $message = $messagingClient->createMessageFromProto($messageProto);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                    $messagingClient->EmailVerificationTopic);
        }
    }

    public static function sendOrgFeedback($task, $user, $feedback)
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $messageType = new FeedbackEmail();
            $messageType->taskId = $task->getId();
            $messageType->userId = $user->getId();
            $messageType->feedback = $feedback;
            $message = $messagingClient->createMessageFromProto($messageType);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                    $messagingClient->FeedbackEmailTopic);
        }
    }

    public static function notifyUserClaimedTask($userId, $taskId) 
    {
        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new UserTaskClaim();
            $message_type->user_id = $userId;
            $message_type->task_id = $taskId;
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                    $messagingClient->UserTaskClaimTopic);
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
        } 
    }

    public static function notifyUserOrgMembershipRequest($user_id, $org_id, $accepted)

    {
        $org_dao = new OrganisationDao();
        $org = $org_dao->getOrg($org_id);
        $org = $org[0];
        
        $user_dao = new UserDao();
        $user = $user_dao->getUser($user_id);
        $user = $user[0];


        $messagingClient = new MessagingClient();
        if ($messagingClient->init()) {
            if ($accepted) {
                $message_type = new OrgMembershipAccepted();
                $message_type->user_id = $user->getId();
                $message_type->org_id = $org->getId();
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                        $messagingClient->OrgMembershipAcceptedTopic);
            } else {
                $message_type = new OrgMembershipRefused();
                $message_type->user_id = $user->getId();
                $message_type->org_id = $org->getId();
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange, 
                        $messagingClient->OrgMembershipRefusedTopic);
            }
        } 
    }

    public static function notifyOrgClaimedTask($userId, $taskId)
    {
        $subscribed_users = TaskDao::getSubscribedUsers($taskId);
        if (count($subscribed_users) > 0) {
            $messagingClient = new MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new TaskClaimed();
                $message_type->task_id = $taskId;
                $message_type->translator_id = $userId;
                foreach ($subscribed_users as $user) {
                    $message_type->user_id = $user->getId();
                    $message = $messagingClient->createMessageFromProto($message_type);
                    $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                            $messagingClient->TaskClaimedTopic);
                }
            }
        }
    }

    public static function sendEmailNotifications($taskId, $notificationType)
    {
        $subscribed_users = TaskDao::getSubscribedUsers($taskId);

        if (count($subscribed_users) > 0) {
            $messagingClient = new MessagingClient();
            if ($messagingClient->init()) {
                switch ($notificationType) {
                        
                    case NotificationTypes::ARCHIVE:
                        $message_type = new TaskArchived();
                        $message_type->task_id = $taskId;
                        foreach ($subscribed_users as $user) {
                            $message_type->user_id = $user->getId();
                            $message = $messagingClient->createMessageFromProto($message_type);
                            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                    $messagingClient->TaskArchivedTopic);
                        }
                        break;
                            
                    case NotificationTypes::UPLOAD:
                        $message_type = new TaskTranslationUploaded();
                        $message_type->task_id = $taskId;
                        $translator = TaskDao::getUserClaimedTask($taskId);
                        $message_type->translator_id = $translator->getId();
                        foreach ($subscribed_users as $user) {
                            $message_type->user_id = $user->getId();
                            $message = $messagingClient->createMessageFromProto($message_type);
                            $messagingClient->sendTopicMessage($message, $messagingClient->MainExchange,
                                    $messagingClient->TaskTranslationUploadedTopic);
                        }
                        break;
                            
                 
                }
            } 
        }
    }
}
