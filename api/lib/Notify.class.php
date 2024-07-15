<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/Settings.class.php";

require_once __DIR__."/../../Common/protobufs/emails/OrgFeedback.php";

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
        DAO\UserDao::insert_queue_request(
            PROJECTQUEUE,
            OrgFeedback,
            $feedback->getUserId(),
            0,
            0,
            0,
            $feedback->getTaskId(),
            $feedback->getClaimantId(),
            $feedback->getFeedback());
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

    public static function notifyOrgClaimedTask($claimant_id, $task_id)
    {
        $subscribed_users = DAO\TaskDao::getSubscribedUsers($task_id);
        if (!empty($subscribed_users)) {
            foreach ($subscribed_users as $user) {
                $user_id = $user->getId();
                DAO\UserDao::insert_queue_request(
                    PROJECTQUEUE,
                    TaskClaimed,
                    $user_id,
                    0,
                    0,
                    0,
                    $task_id,
                    $claimant_id,
                    '');
                error_log("notifyOrgClaimedTask($claimant_id, $task_id) Send to: $user_id");
            }
        }
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
}
