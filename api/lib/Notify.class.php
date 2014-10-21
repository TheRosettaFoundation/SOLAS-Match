<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/MessagingClient.class.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../vendor/autoload.php";

\DrSlump\Protobuf::autoload();

require_once __DIR__."/../../Common/protobufs/emails/EmailMessage.php";
require_once __DIR__."/../../Common/protobufs/emails/UserTaskClaim.php";
require_once __DIR__."/../../Common/protobufs/emails/PasswordResetEmail.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgMembershipAccepted.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgMembershipRefused.php";
require_once __DIR__."/../../Common/protobufs/emails/TaskArchived.php";
require_once __DIR__."/../../Common/protobufs/emails/TaskClaimed.php";
require_once __DIR__."/../../Common/protobufs/emails/UserFeedback.php";
require_once __DIR__."/../../Common/protobufs/emails/OrgFeedback.php";
require_once __DIR__."/../../Common/protobufs/emails/EmailVerification.php";
require_once __DIR__."/../../Common/protobufs/emails/BannedLogin.php";
require_once __DIR__."/../../Common/protobufs/emails/UserBadgeAwardedEmail.php";
require_once __DIR__."/../../Common/protobufs/emails/ProjectImageStatusChangedEmail.php";
require_once __DIR__."/../../Common/protobufs/emails/ProjectImageUploadedEmail.php";
require_once __DIR__."/../../Common/protobufs/emails/ProjectImageRemovedEmail.php";

require_once __DIR__."/../../Common/protobufs/Requests/TaskUploadNotificationRequest.php";
require_once __DIR__.'/../../Common/protobufs/Requests/OrgCreatedNotificationRequest.php';
require_once __DIR__.'/../../Common/protobufs/notifications/TaskRevokedNotification.php';

class Notify
{
    public static function sendBannedLoginEmail($userId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Emails\BannedLogin();
            $proto->setUserId($userId);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->BannedLoginTopic
            );
        }
    }

    public static function sendEmailVerification($userId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $messageProto = new Common\Protobufs\Emails\EmailVerification();
            $messageProto->setUserId($userId);
            $message = $messagingClient->createMessageFromProto($messageProto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->EmailVerificationTopic
            );
        }
    }

    public static function sendOrgFeedback($feedback)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $message = $messagingClient->createMessageFromProto($feedback);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->OrgFeedbackTopic
            );
        }
    }

    public static function sendOrgCreatedNotifications($orgId)
    {
        $client = new Lib\MessagingClient();
        if ($client->init()) {
            $proto = new Common\Protobufs\Requests\OrgCreatedNotificationRequest();
            $proto->setOrgId($orgId);
            $message = $client->createMessageFromProto($proto);
            $client->sendTopicMessage(
                $message,
                $client->MainExchange,
                $client->OrgCreatedTopic
            );
        }
    }

    public static function sendUserAssignedBadgeEmail($userId, $badgeId)
    {
        $client = new Lib\MessagingClient();
        if ($client->init()) {
            $proto = new Common\Protobufs\Emails\UserBadgeAwardedEmail();
            $proto->setUserId($userId);
            $proto->setBadgeId($badgeId);
            $message = $client->createMessageFromProto($proto);
            $client->sendTopicMessage(
                $message,
                $client->MainExchange,
                $client->UserBadgeAwardedTopic
            );
        }
    }

    public static function sendUserFeedback($feedback)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $message = $messagingClient->createMessageFromProto($feedback);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->UserFeedbackTopic
            );
        }
    }

    public static function notifyUserClaimedTask($userId, $taskId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new Common\Protobufs\Emails\UserTaskClaim();
            $message_type->setUserId($userId);
            $message_type->setTaskId($taskId);
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->UserTaskClaimTopic
            );
        }
    }

    public static function sendPasswordResetEmail($user_id)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new Common\Protobufs\Emails\PasswordResetEmail();
            $message_type->setUserId($user_id);
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->PasswordResetTopic
            );
        }
    }

    public static function notifyUserOrgMembershipRequest($userId, $orgId, $accepted)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            if ($accepted) {
                $message_type = new Common\Protobufs\Emails\OrgMembershipAccepted();
                $message_type->setUserId($userId);
                $message_type->setOrgId($orgId);
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage(
                    $message,
                    $messagingClient->MainExchange,
                    $messagingClient->OrgMembershipAcceptedTopic
                );
            } else {
                $message_type = new Common\Protobufs\Emails\OrgMembershipRefused();
                $message_type->setUserId($userId);
                $message_type->setOrgId($orgId);
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage(
                    $message,
                    $messagingClient->MainExchange,
                    $messagingClient->OrgMembershipRefusedTopic
                );
            }
        }
    }

    public static function notifyOrgClaimedTask($userId, $taskId)
    {
        $subscribed_users = DAO\TaskDao::getSubscribedUsers($taskId);
        if (count($subscribed_users) > 0) {
            $messagingClient = new Lib\MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new Common\Protobufs\Emails\TaskClaimed();
                $message_type->setTaskId($taskId);
                $message_type->setTranslatorId($userId);
                foreach ($subscribed_users as $user) {
                    $message_type->setUserId($user->getId());
                    $message = $messagingClient->createMessageFromProto($message_type);
                    $messagingClient->sendTopicMessage(
                        $message,
                        $messagingClient->MainExchange,
                        $messagingClient->TaskClaimedTopic
                    );
                }
            }
        }
    }

    public static function sendTaskUploadNotifications($taskId, $version)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $messageProto = new Common\Protobufs\Requests\TaskUploadNotificationRequest();
            $messageProto->setTaskId($taskId);
            $messageProto->setFileVersion($version);
            $message = $messagingClient->createMessageFromProto($messageProto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->TaskUploadNotificationTopic
            );
        }
    }

    public static function sendTaskArchivedNotifications($taskId, $subscribedUsers)
    {
        if (count($subscribedUsers) > 0) {
            $messagingClient = new Lib\MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new Common\Protobufs\Emails\TaskArchived();
                $message_type->setTaskId($taskId);
                foreach ($subscribedUsers as $user) {
                    $message_type->setUserId($user->getId());
                    $message = $messagingClient->createMessageFromProto($message_type);
                    $messagingClient->sendTopicMessage(
                        $message,
                        $messagingClient->MainExchange,
                        $messagingClient->TaskArchivedTopic
                    );
                }
            }
        }
    }

    public static function sendTaskRevokedNotifications($taskId, $claimantId)
    {
        $client = new Lib\MessagingClient();
        if ($client->init()) {
            $messageProto = new Common\Protobufs\Notifications\TaskRevokedNotification();
            $messageProto->setTaskId($taskId);
            $messageProto->setClaimantId($claimantId);
            $message = $client->createMessageFromProto($messageProto);
            $client->sendTopicMessage(
                $message,
                $client->MainExchange,
                $client->TaskRevokedTopic
            );
        }
    }
    
    public static function sendProjectImageUploaded($projectId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Emails\ProjectImageUploadedEmail();
            $proto->setProjectId($projectId);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->ProjectImageUploadedTopic
            );
        }
    }
    
    public static function sendProjectImageStatusChangedEmail($projectId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Emails\ProjectImageStatusChangedEmail();
            $proto->setProjectId($projectId);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->ProjectImageStatusChangedTopic
            );
        }
    }
    
    public static function sendProjectImageRemoved($projectId)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Emails\ProjectImageRemovedEmail();
            $proto->setProjectId($projectId);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->ProjectImageRemovedTopic
            );
        }
    }
}
