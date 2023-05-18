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
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            if ($accepted) {
                $message_type = new Common\Protobufs\Emails\OrgMembershipAccepted();
                $message_type->setUserId($user_id);
                $message_type->setOrgId($org_id);
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage(
                    $message,
                    $messagingClient->MainExchange,
                    $messagingClient->OrgMembershipAcceptedTopic
                );
            } else {
                $message_type = new Common\Protobufs\Emails\OrgMembershipRefused();
                $message_type->setUserId($user_id);
                $message_type->setOrgId($org_id);
                $message = $messagingClient->createMessageFromProto($message_type);
                $messagingClient->sendTopicMessage(
                    $message,
                    $messagingClient->MainExchange,
                    $messagingClient->OrgMembershipRefusedTopic
                );
            }
        }
                        OrgMembershipAcceptedGenerator::run(queue_request["user_id"].toInt(), queue_request["org_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        OrgMembershipAccepted,
        !!$user_id,
        $badge_id,
        !!$org_id,
        $project_id,
        $task_id,
        $claimant_id,
        $feedback);
                        OrgMembershipRefusedEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["org_id"].toInt());

        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        OrgMembershipRefused,
        !!$user_id,
        $badge_id,
        !!$org_id,
        $project_id,
        $task_id,
        $claimant_id,
        $feedback);
    }


    public static function sendProjectImageUploaded($project_id)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Emails\ProjectImageUploadedEmail();
            $proto->setProjectId($project_id);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->ProjectImageUploadedTopic
            );
        }
                        NewImageUploadedEmailGenerator::run(queue_request["project_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        ProjectImageUploadedEmail,
        $user_id,
        $badge_id,
        $org_id,
        !!$project_id,
        $task_id,
        $claimant_id,
        $feedback);
    }


    public static function sendProjectImageApprovedEmail($project_id)
    {
        $project = DAO\ProjectDao::getProject($project_id);
        $orgAdmins = DAO\AdminDao::getAdmins(null, $project->getOrganisationId());

        if (!empty($orgAdmins) && count($orgAdmins) > 0) {
            $messagingClient = new Lib\MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new Common\Protobufs\Emails\ProjectImageApprovedEmail();
                $message_type->setProjectId($project_id);
                foreach ($orgAdmins as $user) {
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
                        ProjectImageApprovedEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["project_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        ProjectImageApprovedEmail,
        !!$user_id,
        $badge_id,
        $org_id,
        !!$project_id,
        $task_id,
        $claimant_id,
        $feedback);
    }


    public static function sendProjectImageDisapprovedEmail($project_id)
    {
        $project = DAO\ProjectDao::getProject($project_id);
        $orgAdmins = DAO\AdminDao::getAdmins(null, $project->getOrganisationId());

        if (!empty($orgAdmins) && count($orgAdmins) > 0) {
            $messagingClient = new Lib\MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new Common\Protobufs\Emails\ProjectImageDisapprovedEmail();
                $message_type->setProjectId($project_id);
                foreach ($orgAdmins as $user) {
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
                        ProjectImageDisapprovedEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["project_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        ProjectImageDisapprovedEmail,
        !!$user_id,
        $badge_id,
        $org_id,
        !!$project_id,
        $task_id,
        $claimant_id,
        $feedback);
    }



    public static function sendProjectImageRemoved($project_id)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $proto = new Common\Protobufs\Emails\ProjectImageRemovedEmail();
            $proto->setProjectId($project_id);
            $message = $messagingClient->createMessageFromProto($proto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->ProjectImageRemovedTopic
            );
        }
                        ProjectImageRemovedEmailGenerator::run(queue_request["project_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        ProjectImageRemovedEmail,
        $user_id,
        $badge_id,
        $org_id,
        !!$project_id,
        $task_id,
        $claimant_id,
        $feedback);
    }




    public static function sendTaskArchivedNotifications($task_id, $subscribedUsers)
    {
        if (!empty($subscribedUsers) && count($subscribedUsers) > 0) {
            $messagingClient = new Lib\MessagingClient();
            if ($messagingClient->init()) {
                $message_type = new Common\Protobufs\Emails\TaskArchived();
                $message_type->setTaskId($task_id);
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
                        TaskArchivedEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["task_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        TaskArchived,
        !!$user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        $claimant_id,
        $feedback);
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
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        !!$claimant_id,
        !!$feedback);
    }


    public static function notifyUserClaimedTask($user_id, $task_id)
    {
error_log("notifyUserClaimedTask($user_id, $task_id)");
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new Common\Protobufs\Emails\UserTaskClaim();
            $message_type->setUserId($user_id);
            $message_type->setTaskId($task_id);
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->UserTaskClaimTopic
            );
error_log("notifyUserClaimedTask($user_id, $task_id) After Send");
        }
                        UserTaskClaimEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["task_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        UserTaskClaim,
        !!$user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        $claimant_id,
        $feedback);
    }


    public static function notifyOrgClaimedTask($user_id, $task_id)
    {
error_log("notifyOrgClaimedTask($user_id, $task_id)");
        $subscribed_users = DAO\TaskDao::getSubscribedUsers($task_id);
        if (!empty($subscribed_users) && count($subscribed_users) > 0) {
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



    public static function sendTaskUploadNotifications($task_id, $version)
    {
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $messageProto = new Common\Protobufs\Requests\TaskUploadNotificationRequest();
            $messageProto->setTaskId($task_id);
            $messageProto->setFileVersion($version);
            $message = $messagingClient->createMessageFromProto($messageProto);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->TaskUploadNotificationTopic
            );
        }
                        SendTaskUploadNotifications::run(queue_request["task_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        TaskUploadNotificationRequest,
        $user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        $claimant_id,
        $feedback);
    }



    public static function sendTaskRevokedNotifications($task_id, $claimant_id)
    {
        $client = new Lib\MessagingClient();
        if ($client->init()) {
            $messageProto = new Common\Protobufs\Notifications\TaskRevokedNotification();
            $messageProto->setTaskId($task_id);
            $messageProto->setClaimantId($claimant_id);
            $message = $client->createMessageFromProto($messageProto);
            $client->sendTopicMessage(
                $message,
                $client->MainExchange,
                $client->TaskRevokedTopic
            );
        }

                        TaskRevokedNotificationHandler::run(queue_request["task_id"].toInt(), queue_request["claimant_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        TaskRevokedNotification,
        $user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        !!$claimant_id,
        $feedback);
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
error_log("notifyUserTaskCancelled($user_id, $task_id)");
        $messagingClient = new Lib\MessagingClient();
        if ($messagingClient->init()) {
            $message_type = new Common\Protobufs\Emails\UserTaskCancelled();
            $message_type->setUserId($user_id);
            $message_type->setTaskId($task_id);
            $message = $messagingClient->createMessageFromProto($message_type);
            $messagingClient->sendTopicMessage(
                $message,
                $messagingClient->MainExchange,
                $messagingClient->UserTaskCancelledTopic
            );
error_log("notifyUserTaskCancelled($user_id, $task_id) After Send");
        }

                        UserTaskCancelledEmailGenerator::run(queue_request["user_id"].toInt(), queue_request["task_id"].toInt());
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
        UserTaskCancelled,
        !!$user_id,
        $badge_id,
        $org_id,
        $project_id,
        !!$task_id,
        $claimant_id,
        $feedback);
    }
}