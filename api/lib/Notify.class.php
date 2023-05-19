<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../vendor/autoload.php";

define("PROJECTQUEUE",                     "3");

define("EmailVerification",               "13");
define("PasswordResetEmail",               "5");
define("UserBadgeAwardedEmail",           "22");
define("BannedLogin",                     "14");
define("UserReferenceEmail",              "21");
define("OrgCreatedNotificationRequest",  "100");
define("OrgMembershipAccepted",            "3");
define("OrgMembershipRefused",             "4");
define("ProjectImageUploadedEmail",       "29");
define("ProjectImageApprovedEmail",       "31");
define("ProjectImageDisapprovedEmail",    "32");
define("ProjectImageRemovedEmail",        "30");
define("TaskArchived",                     "6");
define("OrgFeedback",                     "18");
define("UserTaskClaim",                    "2");
define("TaskClaimed",                      "7");
define("TaskUploadNotificationRequest",  "101");
define("TaskRevokedNotification",        "102");
define("UserFeedback",                    "11");
define("UserTaskCancelled",               "36");

class Notify
{
    public static function sendEmailVerification($user_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            EmailVerification,
            $user_id,
            0,
            0,
            0,
            0,
            0,
            '');
    }

    public static function sendPasswordResetEmail($user_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            PasswordResetEmail,
            $user_id,
            0,
            0,
            0,
            0,
            0,
            '');
    }

    public static function sendUserAssignedBadgeEmail($user_id, $badge_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            UserBadgeAwardedEmail,
            $user_id,
            $badge_id,
            0,
            0,
            0,
            0,
            '');
    }

    public static function sendBannedLoginEmail($user_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            BannedLogin,
            $user_id,
            0,
            0,
            0,
            0,
            0,
            '');
    }

    public static function sendOrgCreatedNotifications($org_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            OrgCreatedNotificationRequest,
            0,
            0,
            $org_id,
            0,
            0,
            0,
            '');
    }

    public static function notifyUserOrgMembershipRequest($user_id, $org_id, $accepted)
    {
        if ($accepted) {
            DAO\UserDao::insert_queue_request(
                PROJECTQUEUE,
                OrgMembershipAccepted,
                $user_id,
                0,
                $org_id,
                0,
                0,
                0,
                '');
        } else {
            DAO\UserDao::insert_queue_request(
                PROJECTQUEUE,
                OrgMembershipRefused,
                $user_id,
                0,
                $org_id,
                0,
                0,
                0,
                '');
        }
    }

    public static function sendProjectImageUploaded($project_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            ProjectImageUploadedEmail,
            0,
            0,
            0,
            $project_id,
            0,
            0,
            '');
    }

    public static function sendProjectImageApprovedEmail($project_id)
    {
        $project = DAO\ProjectDao::getProject($project_id);
        $orgAdmins = DAO\AdminDao::getAdmins(null, $project->getOrganisationId());
        if (!empty($orgAdmins)) {
            foreach ($orgAdmins as $user) {
                $user_id = $user->getId();
                DAO\UserDao::insert_queue_request(
                    PROJECTQUEUE,
                    ProjectImageApprovedEmail,
                    $user_id,
                    0,
                    0,
                    $project_id,
                    0,
                    0,
                    '');
            }
        }
    }

    public static function sendProjectImageDisapprovedEmail($project_id)
    {
        $project = DAO\ProjectDao::getProject($project_id);
        $orgAdmins = DAO\AdminDao::getAdmins(null, $project->getOrganisationId());
        if (!empty($orgAdmins)) {
            foreach ($orgAdmins as $user) {
                $user_id = $user->getId();
                DAO\UserDao::insert_queue_request(
                    PROJECTQUEUE,
                    ProjectImageDisapprovedEmail,
                    $user_id,
                    0,
                    0,
                    $project_id,
                    0,
                    0,
                    '');
            }
        }
    }

    public static function sendProjectImageRemoved($project_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            ProjectImageRemovedEmail,
            0,
            0,
            0,
            $project_id,
            0,
            0,
            '');
    }

    public static function sendTaskArchivedNotifications($task_id, $subscribedUsers)
    {
        if (!empty($subscribedUsers)) {
            foreach ($subscribedUsers as $user) {
                $user_id = $user->getId();
                DAO\UserDao::insert_queue_request(
                    PROJECTQUEUE,
                    TaskArchived,
                    $user_id,
                    0,
                    0,
                    0,
                    $task_id,
                    0,
                    '');
            }
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
                        OrgFeedbackGenerator::run(queue_request["claimant_id"].toInt(), queue_request["task_id"].toInt(), queue_request["user_id"].toInt(), queue_request["feedback"].toString());  // user_id is admin or owner
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        OrgFeedback,
        !!$user_id,
            0,
        $org_id,
            0,
        !!$task_id,
        !!$claimant_id,
        !!$feedback);
    }

    public static function notifyUserClaimedTask($user_id, $task_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            UserTaskClaim,
            $user_id,
            0,
            0,
            0,
            $task_id,
            0,
            '');
        error_log("notifyUserClaimedTask($user_id, $task_id)");
    }

    public static function notifyOrgClaimedTask($user_id, $task_id)
    {
        $subscribed_users = DAO\TaskDao::getSubscribedUsers($task_id);
        if (!empty($subscribed_users)) {
            $messagingClient = new Lib\MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new Common\Protobufs\Emails\TaskClaimed();
                $message_type->setTaskId($task_id);
                $message_type->setTranslatorId($user_id);
                foreach ($subscribed_users as $user) {
                    $message_type->setUserId($user->getId());
                    $message = $messagingClient->createMessageFromProto($message_type);
                    $messagingClient->sendTopicMessage(
                        $message,
                        $messagingClient->MainExchange,
                        $messagingClient->TaskClaimedTopic
                    );
error_log("notifyOrgClaimedTask($user_id, $task_id) After Send to: " . $user->getId());
                }
            }
        }
                        TaskClaimedEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["task_id"].toInt(), queue_request["claimant_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        TaskClaimed,
        !!$user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        !!$claimant_id,
        $feedback);
    }

    public static function sendTaskUploadNotifications($task_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            TaskUploadNotificationRequest,
            0,
            0,
            0,
            0,
            $task_id,
            0,
            '');
    }

    public static function sendTaskRevokedNotifications($task_id, $claimant_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            TaskRevokedNotification,
            0,
            0,
            0,
            0,
            $task_id,
            $claimant_id,
            '');
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
                        UserFeedbackGenerator::run(queue_request["claimant_id"].toInt(), queue_request["task_id"].toInt(), queue_request["feedback"].toString());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        UserFeedback,
        $user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        !!$claimant_id,
        !!$feedback);
    }

    public static function notifyUserTaskCancelled($user_id, $task_id)
    {
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            UserTaskCancelled,
            $user_id,
            0,
            0,
            0,
            $task_id,
            0,
            '');
        error_log("notifyUserTaskCancelled($user_id, $task_id)");
    }
}
