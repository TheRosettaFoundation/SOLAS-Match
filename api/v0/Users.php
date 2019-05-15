<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API\Lib as Lib;
use \SolasMatch\API as API;
use SolasMatch\API\DAO\AdminDao;
use \SolasMatch\Common\Exceptions as Exceptions;

require_once __DIR__.'/../../Common/protobufs/models/OAuthResponse.php';
require_once __DIR__."/../../Common/protobufs/models/PasswordResetRequest.php";
require_once __DIR__."/../../Common/protobufs/models/PasswordReset.php";
require_once __DIR__."/../../Common/lib/Settings.class.php";
require_once __DIR__."/../DataAccessObjects/UserDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../lib/Notify.class.php";
require_once __DIR__."/../lib/Middleware.php";
require_once '/repo/neon-php/neon.php';


class Users
{
    public static function init()
    {
        $app = \Slim\Slim::getInstance();

        $app->group('/v0', function () use ($app) {
            $app->group('/users', function () use ($app) {
                $app->group('/:userId', function () use ($app) {
                    $app->group('/trackedTasks', function () use ($app) {

                        /* Routes starting /v0/users/:userId/trackedTasks */
                        $app->put(
                            '/:taskId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::addUserTrackedTasksById'
                        );

                        $app->delete(
                            '/:taskId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::deleteUserTrackedTasksById'
                        );
                    });
                    $app->group('/badges', function () use ($app) {

                        /* Routes starting /v0/users/:userId/badges */
                        $app->put(
                            '/:badgeId/',
                            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge',
                            '\SolasMatch\API\V0\Users::addUserbadgesByID'
                        );

                        $app->delete(
                            '/:badgeId/',
                            '\SolasMatch\API\Lib\Middleware::authenticateUserOrOrgForOrgBadge',
                            '\SolasMatch\API\V0\Users::deleteUserbadgesByID'
                        );
                    });

                    $app->group('/tasks', function () use ($app) {

                        /* Routes starting /v0/users/:userId/tasks */
                        $app->get(
                            '/:taskId/review(:format)/',
                            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask',
                            '\SolasMatch\API\V0\Users::getUserTaskReview'
                        );
                        
                        $app->post(
                            '/:taskId/',
                            '\SolasMatch\API\Lib\Middleware::authenticateTaskNotClaimed',
                            '\SolasMatch\API\V0\Users::userClaimTask'
                        );
                        
                        $app->delete(
                            '/:taskId(:format)/',
                            '\SolasMatch\API\Lib\Middleware::authUserOrOrgForTask',
                            '\SolasMatch\API\V0\Users::userUnClaimTask'
                        );
                    });

                    $app->group('/tags', function () use ($app) {

                        /* Routes starting /v0/users/:userId/tags */
                        $app->put(
                            '/:tagId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::addUserTagById'
                        );

                        $app->delete(
                            '/:tagId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::deleteUserTagById'
                        );
                    });

                    $app->group('/projects', function () use ($app) {

                        /* Routes starting /v0/users/:userId/projects */
                        $app->get(
                            '/:projectId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::userTrackProject'
                        );

                        $app->delete(
                            '/:projectId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::userUnTrackProject'
                        );

                        $app->put(
                            '/:projectId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::userTrackProject'
                        );
                    });

                    $app->group('/organisations', function () use ($app) {

                        /* Routes starting /v0/users/:userId/organisations */
                        $app->put(
                            '/:organisationId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::userTrackOrganisation'
                        );

                        $app->delete(
                            '/:organisationId/',
                            '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                            '\SolasMatch\API\V0\Users::userUnTrackOrganisation'
                        );
                    });

                    /* Routes starting /v0/users/:userId */
                    $app->get(
                        '/filteredClaimedTasks/:orderBy/:limit/:offset/:taskType/:taskStatus(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getFilteredUserClaimedTasks'
                    );

                    $app->get(
                        '/filteredClaimedTasksCount/:taskType/:taskStatus(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getFilteredUserClaimedTasksCount'
                    );
                    
                    $app->get(
                        '/recentTasks/:limit/:offset(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserRecentTasks'
                    );
                    
                    $app->get(
                        '/recentTasksCount(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserRecentTasksCount'
                    );

                    $app->put(
                        '/requestReference(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::userRequestReference'
                    );

                    $app->get(
                        '/realName(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authenticateUserMembership',
                        '\SolasMatch\API\V0\Users::getUserRealName'
                    );

                    $app->get(
                        '/verified(:format)/',
                        '\SolasMatch\API\V0\Users::isUserVerified'
                    );

                    $app->get(
                        '/orgs(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserOrgs'
                    );

                    $app->get(
                        '/badges(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserbadges'
                    );

                    $app->post(
                        '/badges(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::addUserbadges'
                    );

                    $app->get(
                        '/tags(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserTags'
                    );

                    $app->post(
                        '/tags(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::addUserTag'
                    );

                    $app->get(
                        '/taskStreamNotification(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserTaskStreamNotification'
                    );

                    $app->delete(
                        '/taskStreamNotification(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::removeUserTaskStreamNotification'
                    );

                    $app->put(
                        '/taskStreamNotification(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::updateTaskStreamNotification'
                    );

                    $app->get(
                        '/tasks(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserTasks'
                    );

                    $app->get(
                        '/topTasksCount(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserTopTasksCount'
                    );
                    
                    $app->get(
                        '/topTasks(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserTopTasks'
                    );
                    
                    $app->get(
                        '/archivedTasks/:limit/:offset(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserArchivedTasks'
                    );
                    
                    $app->get(
                        '/archivedTasksCount(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getUserArchivedTasksCount'
                    );

                    $app->get(
                        '/trackedTasks(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserTrackedTasks'
                    );

                    $app->post(
                        '/trackedTasks(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::addUserTrackedTasks'
                    );

                    $app->get(
                        '/projects(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserTrackedProjects'
                    );

                    $app->get(
                        '/personalInfo(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserPersonalInfo'
                    );

                    $app->post(
                        '/personalInfo(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::createUserPersonalInfo'
                    );

                    $app->put(
                        '/personalInfo(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::updateUserPersonalInfo'
                    );

                    $app->get(
                        '/secondaryLanguages(:format)/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::getSecondaryLanguages'
                    );

                    $app->post(
                        '/secondaryLanguages(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::createSecondaryLanguage'
                    );

                    $app->get(
                        '/organisations(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                        '\SolasMatch\API\V0\Users::getUserTrackedOrganisations'
                    );
                });

                $app->group('/:uuid', function () use ($app) {

                    /* Routes starting /v0/users/:uuid */
                    $app->get(
                        '/registered(:format)/',
                        '\SolasMatch\API\V0\Users::getRegisteredUser'
                    );

                    $app->post(
                        '/finishRegistration(:format)/',
                        '\SolasMatch\API\V0\Users::finishRegistration'
                    );

                    $app->post(
                        '/manuallyFinishRegistration(:format)/',
                        '\SolasMatch\API\V0\Users::finishRegistrationManually'
                    );
                });

                $app->group('/email/:email', function () use ($app) {

                    /* Routes starting /v0/users/email/:email */
                    $app->get(
                        '/passwordResetRequest/time(:format)/',
                        '\SolasMatch\API\V0\Users::getPasswordResetRequestTime'
                    );

                    $app->get(
                        '/passwordResetRequest(:format)/',
                        '\SolasMatch\API\V0\Users::hasUserRequestedPasswordReset'
                    );
                    
                    $app->get(
                        '/getBannedComment(:format)/',
                        '\SolasMatch\API\Lib\Middleware::authenticateIsUserBanned',
                        '\SolasMatch\API\V0\Users::getBannedComment'
                    );

                    $app->post(
                        '/passwordResetRequest(:format)/',
                        '\SolasMatch\API\V0\Users::createPasswordResetRequest'
                    );
                });

                /* Routes starting /v0/users */
                $app->delete(
                    '/removeSecondaryLanguage/:userId/:languageCode/:countryCode/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::deleteSecondaryLanguage'
                );

                $app->get(
                    '/subscribedToOrganisation/:userId/:organisationId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::userSubscribedToOrganisation'
                );

                $app->delete(
                    '/leaveOrg/:userId/:orgId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOrAdminForOrg',
                    '\SolasMatch\API\V0\Users::userLeaveOrg'
                );

                $app->get(
                    '/:email/auth/code(:format)/',
                    '\SolasMatch\API\V0\Users::getAuthCode'
                );

                $app->get(
                    '/subscribedToTask/:userId/:taskId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::userSubscribedToTask'
                );

                $app->get(
                    '/subscribedToProject/:userId/:projectId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::userSubscribedToProject'
                );

                $app->get(
                    '/isBlacklistedForTask/:userId/:taskId/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Users::isBlacklistedForTask'
                );
                
                $app->get(
                        '/isBlacklistedForTaskByAdmin/:userId/:taskId/',
                        '\SolasMatch\API\Lib\Middleware::isloggedIn',
                        '\SolasMatch\API\V0\Users::isBlacklistedForTaskByAdmin'
                );

                $app->put(
                    '/assignBadge/:email/:badgeId/',
                    '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge',
                    '\SolasMatch\API\V0\Users::assignBadge'
                );

                $app->get(
                    '/getClaimedTasksCount/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::getUserClaimedTasksCount'
                );

                $app->post(
                    '/authCode/login(:format)/',
                    '\SolasMatch\API\V0\Users::getAccessToken'
                );

                $app->post(
                    '/gplus/login(:format)/',
                    '\SolasMatch\API\V0\Users::loginWithGooglePlus'
                );

                $app->get(
                    '/getByEmail/:email/',
                    '\SolasMatch\API\Lib\Middleware::registerValidation',
                    '\SolasMatch\API\V0\Users::getUserByEmail'
                );

                $app->get(
                    '/passwordReset/:key/',
                    '\SolasMatch\API\V0\Users::getResetRequest'
                );

                $app->get(
                    '/getCurrentUser(:format)/',
                    '\SolasMatch\API\V0\Users::getCurrentUser'
                );

                $app->get(
                    '/login(:format)/',
                    '\SolasMatch\API\V0\Users::getLoginTemplate'
                );

                $app->post(
                    '/login(:format)/',
                    '\SolasMatch\API\V0\Users::login'
                );

                $app->get(
                    '/passwordReset(:format)/',
                    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                    '\SolasMatch\API\V0\Users::getResetTemplate'
                );

                $app->post(
                    '/passwordReset(:format)/',
                    '\SolasMatch\API\V0\Users::resetPassword'
                );

                $app->get(
                    '/register(:format)/',
                    '\SolasMatch\API\V0\Users::getRegisterTemplate'
                );

                $app->post(
                    '/register(:format)/',
                    '\SolasMatch\API\V0\Users::register'
                );

                $app->post(
                    '/changeEmail(:format)/',
                    '\SolasMatch\API\Lib\Middleware::authenticateSiteAdmin',
                    '\SolasMatch\API\V0\Users::changeEmail'
                );

                // Security (work done directly in ui/DataAccessObjects/UserDao.class.php: getUser($userId) )
                //$app->get(
                //    '/:userId/',
                //    '\SolasMatch\API\Lib\Middleware::isloggedIn',
                //    '\SolasMatch\API\V0\Users::getUser'
                //);

                $app->put(
                    '/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::updateUser'
                );

                $app->delete(
                    '/:userId/',
                    '\SolasMatch\API\Lib\Middleware::authUserOwnsResource',
                    '\SolasMatch\API\V0\Users::deleteUser'
                );
            });

            /* Routes starting /v0 */
            $app->get(
                '/users(:format)/',
                '\SolasMatch\API\V0\Users::getUsers'
            );
        });
    }

    public static function addUserTrackedTasksById($userId, $taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        $data = DAO\UserDao::trackTask($userId, $taskId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function deleteUserTrackedTasksById($userId, $taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        $data = DAO\UserDao::ignoreTask($userId, $taskId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function deleteUserbadgesByID($userId, $badgeId, $format = ".json")
    {
        if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
            $badgeId = explode('.', $badgeId);
            $format = '.'.$badgeId[1];
            $badgeId = $badgeId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::removeUserBadge($userId, $badgeId), null, $format);
    }

    public static function addUserbadgesByID($userId, $badgeId, $format = ".json")
    {
        if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
            $badgeId = explode('.', $badgeId);
            $format = '.'.$badgeId[1];
            $badgeId = $badgeId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $badgeId), null, $format);
    }

    public static function getUserTags($userId, $format = ".json")
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, null);
        API\Dispatcher::sendResponse(null, DAO\UserDao::getUserTags($userId, $limit), null, $format);
    }

    public static function addUserTag($userId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Tag');
        $data = DAO\UserDao::likeTag($userId, $data->getId());
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserTaskStreamNotification($userId, $format = ".json")
    {
        $data = DAO\UserDao::getUserTaskStreamNotification($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserTaskReview($userId, $taskId, $format = '.json')
    {
        $reviews = DAO\TaskDao::getTaskReviews(null, $taskId, $userId);
        API\Dispatcher::sendResponse(null, $reviews[0], null, $format);
    }

    public static function userUnClaimTask($userId, $taskId, $format = ".json")
    {
        if (strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        $feedback = API\Dispatcher::getDispatcher()->request()->getBody();
        $feedback = trim($feedback);
        if ($feedback != '') {
            API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId, $feedback), null, $format);
        } else {
            API\Dispatcher::sendResponse(null, DAO\TaskDao::unClaimTask($taskId, $userId), null, $format);
        }
        Lib\Notify::sendTaskRevokedNotifications($taskId, $userId);
    }

    public static function addUserTagById($userId, $tagId, $format = ".json")
    {
        if (!is_numeric($tagId) && strstr($tagId, '.')) {
            $tagId = explode('.', $tagId);
            $format = '.'.$tagId[1];
            $tagId = $tagId[0];
        }
        $data = DAO\UserDao::likeTag($userId, $tagId);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function deleteUserTagById($userId, $tagId, $format = ".json")
    {
        if (!is_numeric($tagId) && strstr($tagId, '.')) {
            $tagId = explode('.', $tagId);
            $format = '.'.$tagId[1];
            $tagId = $tagId[0];
        }
        $data = DAO\UserDao::removeTag($userId, $tagId);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userTrackProject($userId, $projectId, $format = ".json")
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        $data = DAO\UserDao::trackProject($projectId, $userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userUnTrackProject($userId, $projectId, $format = ".json")
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        $data = DAO\UserDao::unTrackProject($projectId, $userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userTrackOrganisation($userId, $organisationId, $format = ".json")
    {
        if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
            $organisationId = explode('.', $organisationId);
            $format = '.'.$organisationId[1];
            $organisationId = $organisationId[0];
        }
        $data = DAO\UserDao::trackOrganisation($userId, $organisationId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userUnTrackOrganisation($userId, $organisationId, $format = ".json")
    {
        if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
            $organisationId = explode('.', $organisationId);
            $format = '.'.$organisationId[1];
            $organisationId = $organisationId[0];
        }
        $data = DAO\UserDao::unTrackOrganisation($userId, $organisationId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userRequestReference($userId, $format = ".json")
    {
        DAO\UserDao::requestReference($userId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function getUserRealName($userId, $format = '.json')
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::getUserRealName($userId), null, $format);
    }

    public static function isUserVerified($userId, $format = '.json')
    {
        $ret = DAO\UserDao::isUserVerified($userId);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function getUserOrgs($userId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::findOrganisationsUserBelongsTo($userId), null, $format);
    }

    public static function getUserbadges($userId, $format = ".json")
    {
        API\Dispatcher::sendResponse(null, DAO\UserDao::getUserBadges($userId), null, $format);
    }

    public static function addUserbadges($userId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Badge');
        API\Dispatcher::sendResponse(null, DAO\BadgeDao::assignBadge($userId, $data->getId()), null, $format);
    }

    public static function removeUserTaskStreamNotification($userId, $format = ".json")
    {
        $ret = DAO\UserDao::removeTaskStreamNotification($userId);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function updateTaskStreamNotification($userId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification');
        $ret = DAO\UserDao::requestTaskStreamNotification($data);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function getUserTasks($userId, $format = ".json")
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 10);
        $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
        API\Dispatcher::sendResponse(null, DAO\TaskDao::getUserTasks($userId, $limit, $offset), null, $format);
    }

    public static function userClaimTask($userId, $taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\TaskDao::claimTask($taskId, $userId), null, $format);
        Lib\Notify::notifyUserClaimedTask($userId, $taskId);
        Lib\Notify::notifyOrgClaimedTask($userId, $taskId);
    }

    public static function getUserTopTasks($userId, $format = ".json")
    {
        $limit = API\Dispatcher::clenseArgs('limit', Common\Enums\HttpMethodEnum::GET, 5);
        $offset = API\Dispatcher::clenseArgs('offset', Common\Enums\HttpMethodEnum::GET, 0);
        $filter = API\Dispatcher::clenseArgs('filter', Common\Enums\HttpMethodEnum::GET, '');
        $strict = API\Dispatcher::clenseArgs('strict', Common\Enums\HttpMethodEnum::GET, false);
        $filters = Common\Lib\APIHelper::parseFilterString($filter);
        $filter = "";
        $taskType = '';
        $sourceLanguageCode = '';
        $targetLanguageCode = '';
        if (isset($filters['taskType']) && $filters['taskType'] != '') {
            $taskType = $filters['taskType'];
        }
        if (isset($filters['sourceLanguage']) && $filters['sourceLanguage'] != '') {
            $sourceLanguageCode = $filters['sourceLanguage'];
        }
        if (isset($filters['targetLanguage']) && $filters['targetLanguage'] != '') {
            $targetLanguageCode = $filters['targetLanguage'];
        }
        $dao = new DAO\TaskDao();
        $data = $dao->getUserTopTasks(
            $userId,
            $strict,
            $limit,
            $offset,
            $taskType,
            $sourceLanguageCode,
            $targetLanguageCode
        );
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserTopTasksCount($userId, $format = ".json")
    {
        $filter = API\Dispatcher::clenseArgs('filter', Common\Enums\HttpMethodEnum::GET, '');
        $strict = API\Dispatcher::clenseArgs('strict', Common\Enums\HttpMethodEnum::GET, false);
        $filters = Common\Lib\APIHelper::parseFilterString($filter);
        $filter = "";
        $taskType = '';
        $sourceLanguageCode = '';
        $targetLanguageCode = '';
        if (isset($filters['taskType']) && $filters['taskType'] != '') {
            $taskType = $filters['taskType'];
        }
        if (isset($filters['sourceLanguage']) && $filters['sourceLanguage'] != '') {
            $sourceLanguageCode = $filters['sourceLanguage'];
        }
        if (isset($filters['targetLanguage']) && $filters['targetLanguage'] != '') {
            $targetLanguageCode = $filters['targetLanguage'];
        }
        $dao = new DAO\TaskDao();
        $data = $dao->getUserTopTasksCount(
            $userId,
            $strict,
            $taskType,
            $sourceLanguageCode,
            $targetLanguageCode
        );
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }
    
    
    public static function getFilteredUserClaimedTasks(
            $userId,
            $orderBy,
            $limit,
            $offset,
            $taskType,
            $taskStatus,
            $format = ".json"
    ) {
        if (!is_numeric($taskStatus) && strstr($taskStatus, '.')) {
            $taskStatus = explode('.', $taskStatus);
            $format = '.'.$taskStatus[1];
            $taskStatus = $taskStatus[0];
        }

        API\Dispatcher::sendResponse(
            null,
            DAO\TaskDao::getFilteredUserClaimedTasks(
                $userId,
                $orderBy,
                $limit,
                $offset,
                $taskType,
                $taskStatus
            ),
            null,
            $format
        );
    }

    public static function getFilteredUserClaimedTasksCount(
            $userId,
            $taskType,
            $taskStatus,
            $format = ".json"
    ) {
        if (!is_numeric($taskStatus) && strstr($taskStatus, '.')) {
            $taskStatus = explode('.', $taskStatus);
            $format = '.'.$taskStatus[1];
            $taskStatus = $taskStatus[0];
        }

        API\Dispatcher::sendResponse(
            null,
            DAO\TaskDao::getFilteredUserClaimedTasksCount(
                $userId,
                $taskType,
                $taskStatus
            ),
            null,
            $format
        );
    }
    
    public static function getUserRecentTasks(
            $userId,
            $limit,
            $offset,
            $format = ".json"
    ) {
        if (!is_numeric($offset) && strstr($offset, '.')) {
            $offset = explode('.', $offset);
            $format = '.'.$offset[1];
            $offset = $offset[0];
        }
        API\Dispatcher::sendResponse(
        null,
        DAO\TaskDao::getUserRecentTasks(
        $userId,
        $limit,
        $offset
        ),
        null,
        $format
        );
    }

    public static function getUserRecentTasksCount(
            $userId,
            $format = ".json"
    ) {
        API\Dispatcher::sendResponse(
        null,
        DAO\TaskDao::getUserRecentTasksCount(
        $userId
        ),
        null,
        $format
        );
    }

    public static function getUserArchivedTasks($userId, $limit, $offset, $format = ".json")
    {
        if (!is_numeric($offset) && strstr($offset, '.')) {
            $offset = explode('.', $offset);
            $format = '.'.$offset[1];
            $offset = $offset[0];
        }
        
        $data = DAO\TaskDao::getUserArchivedTasks($userId, $limit, $offset);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }
    
    public static function getUserArchivedTasksCount($userId, $format = ".json")
    {
        $data = DAO\TaskDao::getUserArchivedTasksCount($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserTrackedTasks($userId, $format = ".json")
    {
        $data = DAO\UserDao::getTrackedTasks($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function addUserTrackedTasks($userId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\Task');
        $data = DAO\UserDao::trackTask($userId, $data->getId());
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserTrackedProjects($userId, $format = ".json")
    {
        $data = DAO\UserDao::getTrackedProjects($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getUserPersonalInfo($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\UserDao::getPersonalInfo(null, $userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function createUserPersonalInfo($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation");
        API\Dispatcher::sendResponse(null, DAO\UserDao::savePersonalInfo($data), null, $format);
    }

    public static function updateUserPersonalInfo($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\UserPersonalInformation');
        $data = DAO\UserDao::savePersonalInfo($data);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getSecondaryLanguages($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\UserDao::getSecondaryLanguages($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function createSecondaryLanguage($userId, $format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Locale");
        API\Dispatcher::sendResponse(null, DAO\UserDao::createSecondaryLanguage($userId, $data), null, $format);
    }

    public static function getUserTrackedOrganisations($userId, $format = ".json")
    {
        $data = DAO\UserDao::getTrackedOrganisations($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getRegisteredUser($uuid, $format = '.json')
    {
        $user = DAO\UserDao::getRegisteredUser($uuid);
        API\Dispatcher::sendResponse(null, $user, null, $format);
    }

    public static function finishRegistration($uuid, $format = '.json')
    {
        $user = DAO\UserDao::getRegisteredUser($uuid);
        if ($user != null) {
            error_log("finishRegistration($uuid) " . $user->getId());
            $ret = DAO\UserDao::finishRegistration($user->getId());
            API\Dispatcher::sendResponse(null, $ret, null, $format);
        } else {
            API\Dispatcher::sendResponse(null, "Invalid UUID", Common\Enums\HttpStatusEnum::UNAUTHORIZED, $format);
        }
    }

    public static function finishRegistrationManually($email, $format = '.json')
    {
        error_log("finishRegistrationManually($email)");
        $ret = DAO\UserDao::finishRegistrationManually($email);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function getPasswordResetRequestTime($email, $format = ".json")
    {
        $resetRequest = DAO\UserDao::getPasswordResetRequests($email);
        API\Dispatcher::sendResponse(null, $resetRequest->getRequestTime(), null, $format);
    }

    public static function hasUserRequestedPasswordReset($email, $format = ".json")
    {
        $data = DAO\UserDao::hasRequestedPasswordReset($email) ? 1 : 0;
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function createPasswordResetRequest($email, $format = ".json")
    {
        $user = DAO\UserDao::getUser(null, $email);
        if ($user) {
            API\Dispatcher::sendResponse(null, DAO\UserDao::createPasswordReset($user), null, $format);
            Lib\Notify::sendPasswordResetEmail($user->getId());
        } else {
            API\Dispatcher::sendResponse(null, null, null, $format);
        }
    }

    public static function deleteSecondaryLanguage($userId, $languageCode, $countryCode, $format = ".json")
    {
        if (strstr($countryCode, '.')) {
            $countryCode = explode('.', $countryCode);
            $format = '.'.$countryCode[1];
            $countryCode = $countryCode[0];
        }
        $data = DAO\UserDao::deleteSecondaryLanguage($userId, $languageCode, $countryCode);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function userSubscribedToOrganisation($userId, $organisationId, $format = ".json")
    {
        if (!is_numeric($organisationId) && strstr($organisationId, '.')) {
            $organisationId = explode('.', $organisationId);
            $format = '.'.$organisationId[1];
            $organisationId = $organisationId[0];
        }
        API\Dispatcher::sendResponse(
            null,
            DAO\UserDao::isSubscribedToOrganisation($userId, $organisationId),
            null,
            $format
        );
    }

    public static function userLeaveOrg($userId, $orgId, $format = ".json")
    {
        if (!is_numeric($orgId) && strstr($orgId, '.')) {
            $orgId = explode('.', $orgId);
            $format = '.'.$orgId[1];
            $orgId = $orgId[0];
        }
        $data = DAO\OrganisationDao::revokeMembership($orgId, $userId);
        if (is_array($data)) {
            $data = $data[0];
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getAuthCode($email, $format = '.json')
    {
        $user = DAO\UserDao::getUser(null, $email);
        if (!$user) {
            error_log("apiRegister($email) in getAuthCode()");
            DAO\UserDao::apiRegister($email, md5($email), false);
            $user = DAO\UserDao::getUser(null, $email);
            DAO\UserDao::finishRegistration($user->getId());
            //Set new user's personal info to show their preferred language as English.
            $newUser = DAO\UserDao::getUser(null, $user->getEmail());
            $userInfo = new Common\Protobufs\Models\UserPersonalInformation();
            $english = DAO\LanguageDao::getLanguage(null, "en");
            $userInfo->setUserId($newUser->getId());
            $userInfo->setLanguagePreference($english->getId());
            $personal_info = DAO\UserDao::savePersonalInfo($userInfo);
            self::update_user_with_neon_data($newUser, $personal_info);
        }
        $params = array();
        try {
            if (DAO\AdminDao::isUserBanned($user->getId())) {
                throw new \Exception("User is banned");
            }
            $server = API\Dispatcher::getOauthServer();
            $authCodeGrant = $server->getGrantType('authorization_code');
            $params = $authCodeGrant->checkAuthoriseParams();
            $authCode = $authCodeGrant->newAuthoriseRequest('user', $user->getId(), $params);
        } catch (\Exception $e) {
            DAO\UserDao::logLoginAttempt($user->getId(), $email, 0);
            error_log("Exception $email");
            if (!isset($params['redirect_uri'])) {
                API\Dispatcher::getDispatcher()->redirect(
                    API\Dispatcher::getDispatcher()->request()->getReferrer().
                    "?error=auth_failed&error_message={$e->getMessage()}"
                );
            } else {
                API\Dispatcher::getDispatcher()->redirect(
                    $params['redirect_uri']."?error=auth_failed&error_message={$e->getMessage()}"
                );
            }
        }
        API\Dispatcher::getDispatcher()->redirect($params['redirect_uri']."?code=$authCode");
    }

    public static function userSubscribedToTask($userId, $taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\UserDao::isSubscribedToTask($userId, $taskId), null, $format);
    }

    public static function userSubscribedToProject($userId, $projectId, $format = ".json")
    {
        if (!is_numeric($projectId) && strstr($projectId, '.')) {
            $projectId = explode('.', $projectId);
            $format = '.'.$projectId[1];
            $projectId = $projectId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\UserDao::isSubscribedToProject($userId, $projectId), null, $format);
    }

    public static function isBlacklistedForTask($userId, $taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\UserDao::isBlacklistedForTask($userId, $taskId), null, $format);
    }
    
    public static function isBlacklistedForTaskByAdmin($userId, $taskId, $format = ".json")
    {
        if (!is_numeric($taskId) && strstr($taskId, '.')) {
            $taskId = explode('.', $taskId);
            $format = '.'.$taskId[1];
            $taskId = $taskId[0];
        }
        API\Dispatcher::sendResponse(null, DAO\UserDao::isBlacklistedForTaskByAdmin($userId, $taskId), null, $format);
    }

    public static function assignBadge($email, $badgeId, $format = ".json")
    {
        if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
            $badgeId = explode('.', $badgeId);
            $format = '.'.$badgeId[1];
            $badgeId = $badgeId[0];
        }
        $ret = false;
        $user = DAO\UserDao::getUser(null, $email);
        $ret = DAO\BadgeDao::assignBadge($user->getId(), $badgeId);
        API\Dispatcher::sendResponse(null, $ret, null, $format);
    }

    public static function getUserClaimedTasksCount($userId, $format = '.json')
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\TaskDao::getUserTasksCount($userId);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }
    
    
    public static function loginWithGooglePlus($format = '.json')
    {
        try {
            $data = API\Dispatcher::getDispatcher()->request()->getBody();
            $parsed_data = array();
            parse_str($data, $parsed_data);
            $id_token = $parsed_data['token'];

            $request =  Common\Lib\Settings::get('googlePlus.token_validation_endpoint');
            $client = new Common\Lib\APIHelper('');
            $ret = $client->externalCall(
                null,
                $request,
                Common\Enums\HttpMethodEnum::GET,
                null,
                array('id_token' => $id_token)
            );
            $response = json_decode($ret);
            // error_log("oauth2/v3/tokeninfo response: " . print_r($response, true));

            $client_id = Common\Lib\Settings::get('googlePlus.client_id');
            if ($client_id != $response->aud) {
                throw new \Exception("Received token is not intended for this application.");
            }
            if (empty($response->email)) {
                throw new \Exception("Unable to obtain user's email address from Google.");
           }

            API\Dispatcher::sendResponse(null, $response->email, null, $format, null);
        } catch (\Exception $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::BAD_REQUEST, $format);
        }
    }

    public static function getAccessToken($format = '.json')
    {
        try {
            $server = API\Dispatcher::getOauthserver();
            $authCodeGrant = $server->getGrantType('authorization_code');
            $accessToken = $authCodeGrant->completeFlow();

            $oAuthToken = new Common\Protobufs\Models\OAuthResponse();
            $oAuthToken->setToken($accessToken['access_token']);
            $oAuthToken->setTokenType($accessToken['token_type']);
            $oAuthToken->setExpires($accessToken['expires']);
            $oAuthToken->setExpiresIn($accessToken['expires_in']);

            $user = DAO\UserDao::getLoggedInUser($accessToken['access_token']);
            $user->setPassword("");
            $user->setNonce("");

            DAO\UserDao::logLoginAttempt($user->getId(), $user->getEmail(), 1);

            API\Dispatcher::sendResponse(null, $user, null, $format, $oAuthToken);
        } catch (\Exception $e) {
            error_log("Exception getAccessToken");
            API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::BAD_REQUEST, $format);
        }
    }

    public static function getUserByEmail($email, $format = ".json")
    {
        if (!is_numeric($email) && strstr($email, '.')) {
            $temp = array();
            $temp = explode('.', $email);
            $lastIndex = sizeof($temp)-1;
            if ($lastIndex > 0) {
                $email = $temp[0];
                for ($i = 1; $i < $lastIndex; $i++) {
                    $email = "{$email}.{$temp[$i]}";
                }
                if ($temp[$lastIndex] != "json") {
                    $email = "{$email}.{$temp[$lastIndex]}";
                }
            }
        }
        $data = DAO\UserDao::getUser(null, $email);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getResetRequest($key, $format = ".json")
    {
        if (!is_numeric($key) && strstr($key, '.')) {
            $key = explode('.', $key);
            $format = '.'.$key[1];
            $key = $key[0];
        }
        $data = DAO\UserDao::getPasswordResetRequests(null, $key);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function getCurrentUser($format = ".json")
    {
        $user = DAO\UserDao::getLoggedInUser();
        API\Dispatcher::sendResponse(null, $user, null, $format);
    }

    public static function getLoginTemplate($format = ".json")
    {
        $data = new Common\Protobufs\Models\Login();
        $data->setEmail("sample@example.com");
        $data->setPassword("sample_password");
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function login($format = ".json")
    {
        $body = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $loginData = $client->deserialize($body, "\SolasMatch\Common\Protobufs\Models\Login");
        $params = array();
        $params['client_id'] = API\Dispatcher::clenseArgs('client_id', Common\Enums\HttpMethodEnum::GET, null);
        $params['client_secret'] = API\Dispatcher::clenseArgs('client_secret', Common\Enums\HttpMethodEnum::GET, null);
        $params['username'] = $loginData->getEmail();
        $params['password'] = $loginData->getPassword();
        try {
            $server = API\Dispatcher::getOauthServer();
            $response = $server->getGrantType('password')->completeFlow($params);
            $oAuthResponse = new Common\Protobufs\Models\OAuthResponse();
            $oAuthResponse->setToken($response['access_token']);
            $oAuthResponse->setTokenType($response['token_type']);
            $oAuthResponse->setExpires($response['expires']);
            $oAuthResponse->setExpiresIn($response['expires_in']);

            $user = DAO\UserDao::getLoggedInUser($response['access_token']);
            $user->setPassword("");
            $user->setNonce("");
            API\Dispatcher::sendResponse(null, $user, null, $format, $oAuthResponse);
        } catch (Common\Exceptions\SolasMatchException $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), $e->getCode(), $format);
        } catch (\Exception $e) {
            API\Dispatcher::sendResponse(null, $e->getMessage(), Common\Enums\HttpStatusEnum::UNAUTHORIZED, $format);
        }
    }

    public static function getResetTemplate($format = ".json")
    {
        $data = Common\Lib\ModelFactory::buildModel("PasswordReset", array());
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function resetPassword($format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\PasswordReset');
        $result = DAO\UserDao::passwordReset($data->getPassword(), $data->getKey());
        API\Dispatcher::sendResponse(null, $result, null, $format);
    }

    public static function getRegisterTemplate($format = ".json")
    {
        $data = new Common\Protobufs\Models\Register();
        $data->setPassword("test");
        $data->setEmail("test@test.rog");
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function register($format = ".json")
    {
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Register");
        error_log("apiRegister() in register() " . $data->getEmail());
        $registered = DAO\UserDao::apiRegister($data->getEmail(), $data->getPassword());
        //Set new user's personal info to show their preferred language as English.
        $newUser = DAO\UserDao::getUser(null, $data->getEmail());
        $userInfo = new Common\Protobufs\Models\UserPersonalInformation();
        $english = DAO\LanguageDao::getLanguage(null, "en");
        $userInfo->setUserId($newUser->getId());
        $userInfo->setLanguagePreference($english->getId());
        $personal_info = DAO\UserDao::savePersonalInfo($userInfo);
        self::update_user_with_neon_data($newUser, $personal_info);
        
        API\Dispatcher::sendResponse(null, $registered, null, $format);
    }

    public static function update_user_with_neon_data($newUser, $userInfo)
    {
        global $from_neon_to_trommons_pair;
$NEON_NATIVELANGFIELD = 64;
$NEON_SOURCE1FIELD    = 167;
$NEON_TARGET1FIELD    = 168;
$NEON_SOURCE2FIELD    = 169;
$NEON_TARGET2FIELD    = 170;
$NEON_LEVELFIELD      = 173;

        $email = $newUser->getEmail();
        error_log("update_user_with_neon_data($email)");

        $neon = new \Neon();

        $credentials = array(
            'orgId'  => Common\Lib\Settings::get('neon.org_id'),
            'apiKey' => Common\Lib\Settings::get('neon.api_key')
        );

        $loginResult = $neon->login($credentials);
        if (isset($loginResult['operationResult']) && $loginResult['operationResult'] === 'SUCCESS') {
            $search = array(
                'method' => 'account/listAccounts',
                'columns' => array(
                'standardFields' => array(
                    'Email 1',
                    'First Name',
                    'Last Name',
                    'Preferred Name',
                    'Company Name',
                    'Company ID'),
                'customFields' => array(
                    $NEON_NATIVELANGFIELD,
                    $NEON_SOURCE1FIELD,
                    $NEON_TARGET1FIELD,
                    $NEON_SOURCE2FIELD,
                    $NEON_TARGET2FIELD,
                    $NEON_LEVELFIELD),
                )
            );
            $search['criteria'] = array(array('Email', 'EQUAL', $email));

            $result = $neon->search($search);

            $neon->go(array('method' => 'common/logout'));

            if (empty($result) || empty($result['searchResults'])) {
                error_log("update_user_with_neon_data($email), no results from NeonCRM");
            } else {
                foreach ($result['searchResults'] as $r) {
                    $first_name = (empty($r['First Name'])) ? '' : $r['First Name'];
                    if (!empty($first_name)) break; // If we find a First Name, then we have found the good account and we should use this one "$r" (normally there will only be one account)
                }

                $last_name  = (empty($r['Last Name']))  ? '' : $r['Last Name'];
                if (!empty($first_name)) $userInfo->setFirstName($first_name);
                if (!empty($last_name))  $userInfo->setLastName($last_name);
                DAO\UserDao::savePersonalInfo($userInfo);

                $display_name = (empty($r['Preferred Name'])) ? '' : $r['Preferred Name'];
                if (!empty($display_name)) $newUser->setDisplayName($display_name);

                $nativelang = (empty($r['Native language'])) ? '' : $r['Native language'];
                if (!empty($from_neon_to_trommons_pair[$nativelang])) {
                    $locale = new Common\Protobufs\Models\Locale();
                    $locale->setLanguageCode($from_neon_to_trommons_pair[$nativelang][0]);
                    //$locale->setCountryCode($from_neon_to_trommons_pair[$nativelang][1]); Meeting 20180110...
                    $locale->setCountryCode('--');
                    $newUser->setNativeLocale($locale);
                }

                DAO\UserDao::save($newUser);

                $org_name    = (empty($r['Company Name'])) ? '' : $r['Company Name'];
                $org_id_neon = (empty($r['Company ID']))   ? '' : $r['Company ID'];

                error_log("first_name: $first_name, last_name: $last_name, display_name: $display_name, nativelang: $nativelang, org_name: $org_name, org_id_neon: $org_id_neon");

                $sourcelang1  = (empty($r['Primary Source Language']))   ? '' : $r['Primary Source Language'];
                $targetlang1  = (empty($r['Primary Target Language']))   ? '' : $r['Primary Target Language'];
                $sourcelang2  = (empty($r['Secondary Source Language'])) ? '' : $r['Secondary Source Language'];
                $targetlang2  = (empty($r['Secondary Target Language'])) ? '' : $r['Secondary Target Language'];

                $neon_quality_levels = array('unverified' => 1, 'verified' => 2, 'senior' => 3);
                if (empty($r['Level']) || empty($neon_quality_levels[$r['Level']])) {
                    $quality_level = 1;
                } else {
                    $quality_level = $neon_quality_levels[$r['Level']];
                }

                $user_id = $newUser->getId();
                if (!empty($from_neon_to_trommons_pair[$sourcelang1]) && !empty($from_neon_to_trommons_pair[$targetlang1]) && ($sourcelang1 != $targetlang1)) {
                    DAO\UserDao::createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang1][0], $from_neon_to_trommons_pair[$sourcelang1][1], $from_neon_to_trommons_pair[$targetlang1][0], $from_neon_to_trommons_pair[$targetlang1][1], $quality_level);
                }
                if (!empty($from_neon_to_trommons_pair[$sourcelang1]) && !empty($from_neon_to_trommons_pair[$targetlang2]) && ($sourcelang1 != $targetlang2)) {
                    DAO\UserDao::createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang1][0], $from_neon_to_trommons_pair[$sourcelang1][1], $from_neon_to_trommons_pair[$targetlang2][0], $from_neon_to_trommons_pair[$targetlang2][1], $quality_level);
                }
                if (!empty($from_neon_to_trommons_pair[$sourcelang2]) && !empty($from_neon_to_trommons_pair[$targetlang1]) && ($sourcelang2 != $targetlang1)) {
                    DAO\UserDao::createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang2][0], $from_neon_to_trommons_pair[$sourcelang2][1], $from_neon_to_trommons_pair[$targetlang1][0], $from_neon_to_trommons_pair[$targetlang1][1], $quality_level);
                }
                if (!empty($from_neon_to_trommons_pair[$sourcelang2]) && !empty($from_neon_to_trommons_pair[$targetlang2]) && ($sourcelang2 != $targetlang2)) {
                    DAO\UserDao::createUserQualifiedPair($user_id, $from_neon_to_trommons_pair[$sourcelang2][0], $from_neon_to_trommons_pair[$sourcelang2][1], $from_neon_to_trommons_pair[$targetlang2][0], $from_neon_to_trommons_pair[$targetlang2][1], $quality_level);
                }

                $org_name = trim(str_replace(array('"', '<', '>'), '', $org_name)); // Only Trommons value with limitations (not filtered on output)

                if (!empty($org_id_neon) && $org_id_neon != 3783) { // Translators without Borders (TWb)

                    if ($org_id_matching_neon = DAO\UserDao::getOrgIDMatchingNeon($org_id_neon)) {
                        DAO\AdminDao::addOrgAdmin($user_id, $org_id_matching_neon);
                        error_log("update_user_with_neon_data($email), addOrgAdmin($user_id, $org_id_matching_neon)");

                    } elseif ($org = DAO\OrganisationDao::getOrg(null, $org_name)) { // unlikely?
                        DAO\UserDao::insertOrgIDMatchingNeon($org->getId(), $org_id_neon);

                        DAO\AdminDao::addOrgAdmin($user_id, $org->getId());
                        error_log("update_user_with_neon_data($email), addOrgAdmin($user_id, " . $org->getId() . "), $org_name existing");

                    } elseif (!empty($org_name)) {
                        $org = new Common\Protobufs\Models\Organisation();
                        $org->setName($org_name);
                        $org->setEmail($email);

                        $org = DAO\OrganisationDao::insertAndUpdate($org);
                        error_log("update_user_with_neon_data($email), created Org: $org_name");
                        if (!empty($org) && $org->getId() > 0) {
                            DAO\UserDao::insertOrgIDMatchingNeon($org->getId(), $org_id_neon);

                            DAO\AdminDao::addOrgAdmin($user_id, $org->getId());
                            error_log("update_user_with_neon_data($email), addOrgAdmin($user_id, " . $org->getId() . ')');
                            Lib\Notify::sendOrgCreatedNotifications($org->getId());
                        }
                    }
                }
            }
        } else {
            error_log("update_user_with_neon_data($email), could not connect to NeonCRM");
        }
    }

    public static function changeEmail($format = ".json")
    {
        $user = DAO\UserDao::getLoggedInUser();
        if (!is_null($user) && DAO\AdminDao::isAdmin($user->getId(), null)) {
            $data = API\Dispatcher::getDispatcher()->request()->getBody();
            $client = new Common\Lib\APIHelper($format);
            $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Register");

            // password field has been repurposed to hold User for which email is to be changed
            $registered = DAO\UserDao::changeEmail($data->getPassword(), $data->getEmail());
        }
        else {
            $registered = null;
        }
        API\Dispatcher::sendResponse(null, $registered, null, $format);
    }

    public static function getUser($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = DAO\UserDao::getUser($userId);
        if (!is_null($data)) {
            $data->setPassword("");
            $data->setNonce("");
        }
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function updateUser($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        $data = API\Dispatcher::getDispatcher()->request()->getBody();
        $client = new Common\Lib\APIHelper($format);
        $data = $client->deserialize($data, '\SolasMatch\Common\Protobufs\Models\User');
        $data->setId($userId);
        $data = DAO\UserDao::save($data);
        API\Dispatcher::sendResponse(null, $data, null, $format);
    }

    public static function deleteUser($userId, $format = ".json")
    {
        if (!is_numeric($userId) && strstr($userId, '.')) {
            $userId = explode('.', $userId);
            $format = '.'.$userId[1];
            $userId = $userId[0];
        }
        error_log("deleteUser($userId)");
        DAO\UserDao::deleteUser($userId);
        API\Dispatcher::sendResponse(null, null, null, $format);
    }

    public static function getUsers($format = ".json")
    {
        API\Dispatcher::sendResponse(null, "display all users", null, $format);
    }
    
    public static function getBannedComment($email, $format = ".json")
    {
        $client = new Common\Lib\APIHelper($format);
        
        $user = DAO\UserDao::getUser(null, $email);
        $userId = $user->getId();
        $bannedUser = AdminDao::getBannedUser($userId);
        $bannedUser = $bannedUser[0];
        $comment = $bannedUser->getComment();
        
        API\Dispatcher::sendResponse(null, $comment, null, $format);
    }
}

Users::init();
