<?php

namespace SolasMatch\Common\Lib;

use \SolasMatch\Common\Protobufs\Models as Models;
use \SolasMatch\Common\Protobufs\Emails as Emails;

require_once __DIR__."/../protobufs/models/MembershipRequest.php";
require_once __DIR__."/../protobufs/models/ArchivedTask.php";
require_once __DIR__."/../protobufs/models/PasswordResetRequest.php";
require_once __DIR__."/../protobufs/models/PasswordReset.php";
require_once __DIR__."/../protobufs/models/Register.php";
require_once __DIR__."/../protobufs/models/Country.php";
require_once __DIR__."/../protobufs/models/Language.php";
require_once __DIR__."/../protobufs/models/Login.php";
require_once __DIR__."/../protobufs/models/Badge.php";
require_once __DIR__."/../protobufs/models/Tag.php";
require_once __DIR__."/../protobufs/models/Organisation.php";
require_once __DIR__."/../protobufs/models/OrganisationExtendedProfile.php";
require_once __DIR__."/../protobufs/models/TaskMetadata.php";
require_once __DIR__."/../protobufs/models/User.php";
require_once __DIR__."/../protobufs/models/Task.php";
require_once __DIR__."/../protobufs/models/Project.php";
require_once __DIR__."/../protobufs/models/ArchivedProject.php";
require_once __DIR__."/../protobufs/models/Statistic.php";
require_once __DIR__."/../protobufs/models/ProjectFile.php";
require_once __DIR__."/../protobufs/models/TaskReview.php";
require_once __DIR__."/../protobufs/models/UserTaskStreamNotification.php";
require_once __DIR__."/../protobufs/models/Locale.php";
require_once __DIR__."/../protobufs/models/UserPersonalInformation.php";
require_once __DIR__."/../protobufs/models/BannedUser.php";
require_once __DIR__."/../protobufs/models/BannedOrganisation.php";

require_once __DIR__."/../protobufs/emails/UserFeedback.php";
require_once __DIR__."/../protobufs/emails/OrgFeedback.php";

class ModelFactory
{
    public static function buildModel($modelName, $modelData)
    {
        if ($modelName == "Language") {

        }
        $ret = null;

        switch($modelName)
        {
            case "MembershipRequest":
                $ret = self::generateMembershipRequest($modelData);
                break;
            case "ArchivedTask":
                $ret = self::generateArchivedTask($modelData);
                break;
            case "PasswordReset":
                $ret = self::generatePasswordReset($modelData);
                break;
            case "PasswordResetRequest":
                $ret = self::generatePasswordResetRequest($modelData);
                break;
            case "Register":
                $ret = self::generateRegister($modelData);
                break;
            case "Country":
                $ret = self::generateCountry($modelData);
                break;
            case "Language":
                $ret = self::generateLanguage($modelData);
                break;
            case "Login":
                $ret = self::generateLogin($modelData);
                break;
            case "Badge":
                $ret = self::generateBadge($modelData);
                break;
            case "Tag":
                $ret = self::generateTag($modelData);
                break ;
            case "Organisation":
                $ret = self::generateOrganisation($modelData);
                break;
            case "OrganisationExtendedProfile":
                $ret = self::generateOrganisationExtendedProfile($modelData);
                break;
            case "TaskMetadata":
                $ret = self::generateTaskMetadata($modelData);
                break;
            case "User":
                $ret = self::generateUser($modelData);
                break;
            case "UserTaskStreamNotification":
                $ret = self::generateUserTaskStreamNotification($modelData);
                break;
            case "Task":
                $ret = self::generateTask($modelData);
                break;
            case "TaskReview":
                $ret = self::generateTaskReview($modelData);
                break;
            case "Project":
                $ret = self::generateProject($modelData);
                break;
            case "ArchivedProject":
                $ret = self::generateArchivedProject($modelData);
                break;
            case "Statistic":
                $ret = self::generateStatistic($modelData);
                break;
            case "ProjectFile":
                $ret = self::generateProjectFile($modelData);
                break;
            case "UserPersonalInformation":
                $ret = self::generateUserPersonalInformation($modelData);
                break;
            case "Locale":
                $ret = self::generateLocale($modelData);
                break;
            case "BannedUser":
                $ret = self::generateBannedUser($modelData);
                break;
            case "BannedOrganisation":
                $ret = self::generateBannedOrganisation($modelData);
                break;
            case "OAuthResponse":
                $ret = self::generateOAuthResponse($modelData);
                break;
            case "WorkflowGraph" :
                $ret = self::generateWorkflowGraph($modelData);
                break;
            case "UserFeedback" :
                $ret = self::generateUserFeedback($modelData);
                break;
            case "OrgFeedback" :
                $ret = self::generateOrgFeedback($modelData);
                break;
            default:
                echo "Unable to build model $modelName";
        }

        return $ret;
    }

    private static function generateMembershipRequest($modelData)
    {
        $ret = new Models\MembershipRequest();
        $ret ->setId($modelData["id"]);
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['org_id'])) {
            $ret->setOrgId($modelData['org_id']);
        }
        if (isset($modelData['request_time'])) {
            $ret->setRequestTime($modelData['request_time']);
        }
        return $ret;
    }

    private static function generateArchivedTask($modelData)
    {
        $ret = new Models\ArchivedTask();
        $sourceLocale = new Models\Locale();
        $targetLocale = new Models\Locale();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['projectId'])) {
            $ret->setProjectId($modelData['projectId']);
        }
        if (isset($modelData['title'])) {
            $ret->setTitle($modelData['title']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }
        if (isset($modelData['deadline'])) {
            $ret->setDeadline($modelData['deadline']);
        }
        if (isset($modelData['wordCount'])) {
            $ret->setWordCount($modelData['wordCount']);
        }
        if (isset($modelData['createdTime'])) {
            $ret->setCreatedTime($modelData['createdTime']);
        }
        
        if (isset($modelData['sourceLanguageName'])) {
            $sourceLocale->setLanguageName($modelData['sourceLanguageName']);
        }
        if (isset($modelData['sourceLanguageCode'])) {
            $sourceLocale->setLanguageCode($modelData['sourceLanguageCode']);
        }
        if (isset($modelData['sourceCountryName'])) {
            $sourceLocale->setCountryName($modelData['sourceCountryName']);
        }
        if (isset($modelData['sourceCountryCode'])) {
            $sourceLocale->setCountryCode($modelData['sourceCountryCode']);
        }
        
        if (isset($modelData['targetLanguageName'])) {
            $targetLocale->setLanguageName($modelData['targetLanguageName']);
        }
        if (isset($modelData['targetLanguageCode'])) {
            $targetLocale->setLanguageCode($modelData['targetLanguageCode']);
        }
        if (isset($modelData['targetCountryName'])) {
            $targetLocale->setCountryName($modelData['targetCountryName']);
        }
        if (isset($modelData['targetCountryCode'])) {
            $targetLocale->setCountryCode($modelData['targetCountryCode']);
        }
        
        //The following two if statements should only end up having their code executed if the previous 8,
        //isset($modelData['sourceLanguageName'], etc do not. The locale data should be set on the model
        //in the latter form when coming from the database and in the following form when coming from
        //some other places (i.e. sourceLocale and targetLocale existed as nested arrays in $modelData).
        if (isset($modelData['sourceLocale'])) {
            $sourceLocale = self::generateLocale($modelData['sourceLocale']);
        }
        
        if (isset($modelData['targetLocale'])) {
            $targetLocale = self::generateLocale($modelData['targetLocale']);
        }

        $ret->setSourceLocale($sourceLocale);
        $ret->setTargetLocale($targetLocale);
        
        if (isset($modelData['taskType'])) {
            $ret->setTaskType($modelData['taskType']);
        }
        if (isset($modelData['taskStatus'])) {
            $ret->setTaskStatus($modelData['taskStatus']);
        }
        if (isset($modelData['published'])) {
            $ret->setPublished($modelData['published']);
        }
        
        if (isset($modelData['version'])) {
            $ret->setVersion($modelData['version']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFileName($modelData['filename']);
        }
        if (isset($modelData['contentType'])) {
            $ret->setContentType($modelData['contentType']);
        }
        if (isset($modelData['uploadTime'])) {
            $ret->setUploadTime($modelData['uploadTime']);
        }
        if (isset($modelData['userIdClaimed'])) {
            $ret->setUserIdClaimed($modelData['userIdClaimed']);
        }
        if (isset($modelData['userIdArchived'])) {
            $ret->setUserIdArchived($modelData['userIdArchived']);
        }
        if (isset($modelData['prerequisites'])) {
            $ret->setPrerequisites($modelData['prerequisites']);
        }
        if (isset($modelData['userIdTaskCreator'])) {
            $ret->setUserIdTaskCreator($modelData['userIdTaskCreator']);
        }
        if (isset($modelData['archivedDate'])) {
            $ret->setArchivedDate($modelData['archivedDate']);
        }
        
        return $ret;
    }

    private static function generatePasswordReset($modelData)
    {
        $ret = new Models\PasswordReset();
        
        if (isset($modelData['password'])) {
            $ret->setPassword($modelData['password']);
        }
        if (isset($modelData['key'])) {
            $ret->setKey($modelData['key']);
        }

        return $ret;
    }

    private static function generatePasswordResetRequest($modelData)
    {
        $ret = new Models\PasswordResetRequest();

        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['key'])) {
            $ret->setKey($modelData['key']);
        }
        if (isset($modelData['requestTime'])) {
            $ret->setRequestTime($modelData['requestTime']);
        }

        return $ret;
    }

    private static function generateRegister($modelData)
    {
        $ret = new Models\Register();

        if (isset($modelData['email'])) {
            $ret->setEmail($modelData['email']);
        }
        if (isset($modelData['password'])) {
            $ret->setPassword($modelData['password']);
        }

        return $ret;
    }

    private static function generateCountry($modelData)
    {
        $ret = new Models\Country();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['code'])) {
            $ret->setCode($modelData['code']);
        }
        if (isset($modelData['name'])) {
            $ret->setName($modelData['name']);
        }

        return $ret;
    }

    private static function generateLanguage($modelData)
    {
        $ret = new Models\Language();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['code'])) {
            $ret->setCode($modelData['code']);
        }
        if (isset($modelData['name'])) {
            $ret->setName($modelData['name']);
        }

        return $ret;
    }

    private static function generateLogin($modelData)
    {
        $ret = new Models\Login();

        if (isset($modelData['email'])) {
            $ret->setEmail($modelData['email']);
        }
        if (isset($modelData['password'])) {
            $ret->setPassword($modelData['password']);
        }

        return $ret;
    }

    private static function generateBadge($modelData)
    {
        $ret = new Models\Badge();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['title'])) {
            $ret->setTitle($modelData['title']);
        }
        if (isset($modelData['description'])) {
            $ret->setDescription($modelData['description']);
        }
        if (isset($modelData['owner_id'])) {
            $ret->setOwnerId($modelData['owner_id']);
        }

        return $ret;
    }

    private static function generateTag($modelData)
    {
        $ret = new Models\Tag();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['label'])) {
            $ret->setLabel($modelData['label']);
        }

        return $ret;
    }

    private static function generateOrganisation($modelData)
    {
        $ret = new Models\Organisation();
        
        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['name'])) {
            $ret->setName($modelData['name']);
        }
        if (isset($modelData['biography'])) {
            $ret->setBiography($modelData['biography']);
        }
        if (isset($modelData['homepage'])) {
            $ret->setHomepage($modelData['homepage']);
        }
        if (isset($modelData['email'])) {
            $ret->setEmail($modelData['email']);
        }
        if (isset($modelData['address'])) {
            $ret->setAddress($modelData['address']);
        }
        if (isset($modelData['city'])) {
            $ret->setCity($modelData['city']);
        }
        if (isset($modelData['country'])) {
            $ret->setCountry($modelData['country']);
        }
        if (isset($modelData['regionalFocus'])) {
            $ret->setRegionalFocus($modelData['regionalFocus']);
        }

        return $ret;
        
    }

    private static function generateOrganisationExtendedProfile($modelData)
    {
        $ret = new Models\OrganisationExtendedProfile();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['facebook'])) {
            $ret->setFacebook($modelData['facebook']);
        }
        if (isset($modelData['linkedin'])) {
            $ret->setLinkedin($modelData['linkedin']);
        }
        if (isset($modelData['primaryContactName'])) {
            $ret->setPrimaryContactName($modelData['primaryContactName']);
        }
        if (isset($modelData['primaryContactTitle'])) {
            $ret->setPrimaryContactTitle($modelData['primaryContactTitle']);
        }
        if (isset($modelData['primaryContactEmail'])) {
            $ret->setPrimaryContactEmail($modelData['primaryContactEmail']);
        }
        if (isset($modelData['primaryContactPhone'])) {
            $ret->setPrimaryContactPhone($modelData['primaryContactPhone']);
        }
        if (isset($modelData['otherContacts'])) {
            $ret->setOtherContacts($modelData['otherContacts']);
        }
        if (isset($modelData['structure'])) {
            $ret->setStructure($modelData['structure']);
        }
        if (isset($modelData['affiliations'])) {
            $ret->setAffiliations($modelData['affiliations']);
        }
        if (isset($modelData['urlVideo1'])) {
            $ret->setUrlVideo1($modelData['urlVideo1']);
        }
        if (isset($modelData['urlVideo2'])) {
            $ret->setUrlVideo2($modelData['urlVideo2']);
        }
        if (isset($modelData['urlVideo3'])) {
            $ret->setUrlVideo3($modelData['urlVideo3']);
        }
        if (isset($modelData['subjectMatters'])) {
            $ret->setSubjectMatters($modelData['subjectMatters']);
        }
        if (isset($modelData['activitys'])) {
            $ret->setActivitys($modelData['activitys']);
        }
        if (isset($modelData['employees'])) {
            $ret->setEmployees($modelData['employees']);
        }
        if (isset($modelData['fundings'])) {
            $ret->setFundings($modelData['fundings']);
        }
        if (isset($modelData['finds'])) {
            $ret->setFinds($modelData['finds']);
        }
        if (isset($modelData['translations'])) {
            $ret->setTranslations($modelData['translations']);
        }
        if (isset($modelData['requests'])) {
            $ret->setRequests($modelData['requests']);
        }
        if (isset($modelData['contents'])) {
            $ret->setContents($modelData['contents']);
        }
        if (isset($modelData['pages'])) {
            $ret->setPages($modelData['pages']);
        }
        if (isset($modelData['sources'])) {
            $ret->setSources($modelData['sources']);
        }
        if (isset($modelData['targets'])) {
            $ret->setTargets($modelData['targets']);
        }
        if (isset($modelData['oftens'])) {
            $ret->setOftens($modelData['oftens']);
        }

        return $ret;
    }

    private static function generateTaskMetadata($modelData)
    {
        $ret = new Models\TaskMetadata();

        //This id is the task_id, not the id of the TaskFileVersion record (i.e. TaskMetadata) on the db.
        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['version'])) {
            $ret->setVersion($modelData['version']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFilename($modelData['filename']);
        }
        if (isset($modelData['content_type'])) {
            $ret->setContentType($modelData['content_type']);
        }
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['upload_time'])) {
            $ret->setUploadTime($modelData['upload_time']);
        }

        return $ret;
    }

    private static function generateUser($modelData)
    {
        $ret = new Models\User();
        $locale = new Models\Locale();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['email'])) {
            $ret->setEmail($modelData['email']);
        }
        if (isset($modelData['nonce'])) {
            $ret->setNonce($modelData['nonce']);
        }
        if (isset($modelData['password'])) {
            $ret->setPassword($modelData['password']);
        }
        if (isset($modelData['display_name'])) {
            $ret->setDisplayName($modelData['display_name']);
        }
        if (isset($modelData['biography'])) {
            $ret->setBiography($modelData['biography']);
        }
        
        if (isset($modelData['languageName'])) {
            $locale->setLanguageName($modelData['languageName']);
        }
        if (isset($modelData['languageCode'])) {
            $locale->setLanguageCode($modelData['languageCode']);
        }
        if (isset($modelData['countryName'])) {
            $locale->setCountryName($modelData['countryName']);
        }
        if (isset($modelData['countryCode'])) {
            $locale->setCountryCode($modelData['countryCode']);
        }
        if (isset($modelData['languageName']) && isset($modelData['languageCode']) &&
                (isset($modelData['countryName'])) && isset($modelData['countryCode'])) {
            $ret->setNativeLocale($locale);
        }
        
        if (isset($modelData['nativeLocale'])) {
            $ret->setNativeLocale($modelData['nativeLocale']);
        }
        
        if (isset($modelData['created_time'])) {
            $ret->setCreatedTime($modelData['created_time']);
        }
        return $ret;
    }

    private static function generateUserTaskStreamNotification($modelData)
    {
        $ret = new Models\UserTaskStreamNotification();

        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['interval'])) {
            $ret->setInterval($modelData['interval']);
        }
        if (isset($modelData['last_sent'])) {
            $ret->setLastSent($modelData['last_sent']);
        }
        if (isset($modelData['strict'])) {
            if ($modelData['strict']) {
                $ret->setStrict(true);
            } else {
                $ret->setStrict(false);
            }
        }

        return $ret;
    }

    private static function generateTask($modelData)
    {
        $ret = new Models\Task();
        $sourceLocale = new Models\Locale();
        $targetLocale = new Models\Locale();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['projectId'])) {
            $ret->setProjectId($modelData['projectId']);
        }
        if (isset($modelData['title'])) {
            $ret->setTitle($modelData['title']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }
        if (isset($modelData['deadline'])) {
            $ret->setDeadline($modelData['deadline']);
        }
        if (isset($modelData['wordCount'])) {
            $ret->setWordCount($modelData['wordCount']);
        }
        if (isset($modelData['createdTime'])) {
            $ret->setCreatedTime($modelData['createdTime']);
        }
        
        if (isset($modelData['sourceLanguageName'])) {
            $sourceLocale->setLanguageName($modelData['sourceLanguageName']);
        }
        if (isset($modelData['sourceLanguageCode'])) {
            $sourceLocale->setLanguageCode($modelData['sourceLanguageCode']);
        }
        if (isset($modelData['sourceCountryName'])) {
            $sourceLocale->setCountryName($modelData['sourceCountryName']);
        }
        if (isset($modelData['sourceCountryCode'])) {
            $sourceLocale->setCountryCode($modelData['sourceCountryCode']);
        }
        
        if (isset($modelData['targetLanguageName'])) {
            $targetLocale->setLanguageName($modelData['targetLanguageName']);
        }
        if (isset($modelData['targetLanguageCode'])) {
            $targetLocale->setLanguageCode($modelData['targetLanguageCode']);
        }
        if (isset($modelData['targetCountryName'])) {
            $targetLocale->setCountryName($modelData['targetCountryName']);
        }
        if (isset($modelData['targetCountryCode'])) {
            $targetLocale->setCountryCode($modelData['targetCountryCode']);
        }
        
        //The following two if statements should only end up having their code executed if the previous 8,
        // isset($modelData['sourceLanguageName'], etc do not. The locale data should be set on the model
        //in the latter form when coming from the database and in the following form when coming from
        //some other places (i.e. sourceLocale and targetLocale existed as nested arrays in $modelData).
        if (isset($modelData['sourceLocale'])) {
            $sourceLocale = self::generateLocale($modelData['sourceLocale']);
        }
        
        if (isset($modelData['targetLocale'])) {
            $targetLocale = self::generateLocale($modelData['targetLocale']);
        }

        $ret->setSourceLocale($sourceLocale);
        $ret->setTargetLocale($targetLocale);
        
        if (isset($modelData['taskType'])) {
            $ret->setTaskType($modelData['taskType']);
        }
        if (isset($modelData['taskStatus'])) {
            $ret->setTaskStatus($modelData['taskStatus']);
        }
        if (isset($modelData['published'])) {
            $ret->setPublished($modelData['published'] ? 1 : 0);
        }
        
        return $ret;
    }

    private static function generateTaskReview($modelData)
    {
        $ret = new Models\TaskReview();

        if (isset($modelData['project_id'])) {
            $ret->setProjectId($modelData['project_id']);
        }
        if (isset($modelData['task_id'])) {
            $ret->setTaskId($modelData['task_id']);
        }
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['corrections'])) {
            $ret->setCorrections($modelData['corrections']);
        }
        if (isset($modelData['grammar'])) {
            $ret->setGrammar($modelData['grammar']);
        }
        if (isset($modelData['spelling'])) {
            $ret->setSpelling($modelData['spelling']);
        }
        if (isset($modelData['consistency'])) {
            $ret->setConsistency($modelData['consistency']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }

        return $ret;
    }

    private static function generateProject($modelData)
    {
        $ret = new Models\Project();
        $sourceLocale = new Models\Locale();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['title'])) {
            $ret->setTitle($modelData['title']);
        }
        if (isset($modelData['description'])) {
            $ret->setDescription($modelData['description']);
        }
        if (isset($modelData['deadline'])) {
            $ret->setDeadline($modelData['deadline']);
        }
        if (isset($modelData['organisationId'])) {
            $ret->setOrganisationId($modelData['organisationId']);
        }
        if (isset($modelData['impact'])) {
            $ret->setImpact($modelData['impact']);
        }
        if (isset($modelData['reference'])) {
            $ret->setReference($modelData['reference']);
        }
        if (isset($modelData['wordCount'])) {
            $ret->setWordCount($modelData['wordCount']);
        }
        if (isset($modelData['createdTime'])) {
            $ret->setCreatedTime($modelData['createdTime']);
        }
        if (isset($modelData['status'])) {
            $ret->setStatus($modelData['status']);
        }
        
        if (isset($modelData['languageName'])) {
            $sourceLocale->setLanguageName($modelData['languageName']);
        }
        if (isset($modelData['languageCode'])) {
            $sourceLocale->setLanguageCode($modelData['languageCode']);
        }
        if (isset($modelData['countryName'])) {
            $sourceLocale->setCountryName($modelData['countryName']);
        }
        if (isset($modelData['countryCode'])) {
            $sourceLocale->setCountryCode($modelData['countryCode']);
        }

        $ret->setSourceLocale($sourceLocale);
        
        if (isset($modelData['imageUploaded'])) {
            $ret->setImageUploaded($modelData['imageUploaded'] ? 1 : 0);
        }
        
        if (isset($modelData['imageApproved'])) {
            $ret->setImageApproved($modelData['imageApproved'] ? 1 : 0);
        }
        
        if (isset($modelData['tag'])) {
            foreach ($modelData['tag'] as $tag) {
                $builtTag = self::generateTag($tag);
                $ret->appendTag($builtTag);
            }
        }

        return $ret;
    }

    private static function generateArchivedProject($modelData)
    {
        $ret = new Models\ArchivedProject();
        $sourceLocale = new Models\Locale();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['title'])) {
            $ret->setTitle($modelData['title']);
        }
        if (isset($modelData['description'])) {
            $ret->setDescription($modelData['description']);
        }
        if (isset($modelData['impact'])) {
            $ret->setImpact($modelData['impact']);
        }
        if (isset($modelData['deadline'])) {
            $ret->setDeadline($modelData['deadline']);
        }
        if (isset($modelData['organisationId'])) {
            $ret->setOrganisationId($modelData['organisationId']);
        }
        if (isset($modelData['reference'])) {
            $ret->setReference($modelData['reference']);
        }
        if (isset($modelData['wordCount'])) {
            $ret->setWordCount($modelData['wordCount']);
        }
        if (isset($modelData['created'])) {
            $ret->setCreatedTime($modelData['created']);
        }
        
        if (isset($modelData['sourceLanguageName'])) {
            $sourceLocale->setLanguageName($modelData['sourceLanguageName']);
        }
        if (isset($modelData['sourceLanguageCode'])) {
            $sourceLocale->setLanguageCode($modelData['sourceLanguageCode']);
        }
        if (isset($modelData['sourceCountryName'])) {
            $sourceLocale->setCountryName($modelData['sourceCountryName']);
        }
        if (isset($modelData['sourceCountryCode'])) {
            $sourceLocale->setCountryCode($modelData['sourceCountryCode']);
        }
        
        $ret->setSourceLocale($sourceLocale);
       
        if (isset($modelData['userIdArchived'])) {
            $ret->setUserIdArchived($modelData['userIdArchived']);
        }
        if (isset($modelData['userIdProjectCreator'])) {
            $ret->setUserIdProjectCreator($modelData['userIdProjectCreator']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFileName($modelData['filename']);
        }
        if (isset($modelData['fileToken'])) {
            $ret->setFileToken($modelData['fileToken']);
        }
        if (isset($modelData['mimeType'])) {
            $ret->setMimeType($modelData['mimeType']);
        }
        if (isset($modelData['archivedDate'])) {
            $ret->setArchivedDate($modelData['archivedDate']);
        }
        if (isset($modelData['tags'])) {
            $ret->setTags($modelData['tags']);
        }

        if (isset($modelData['imageUploaded'])) {
            $ret->setImageUploaded($modelData['imageUploaded'] ? 1 : 0);
        }
        
        if (isset($modelData['imageApproved'])) {
            $ret->setImageApproved($modelData['imageApproved'] ? 1 : 0);
        }

        return $ret;
    }
    
    
    private static function generateStatistic($modelData)
    {
        $ret = new Models\Statistic();

        if (isset($modelData['name'])) {
            $ret->setName($modelData['name']);
        }
        if (isset($modelData['value'])) {
            $ret->setValue($modelData['value']);
        }
        
        return $ret;
    }
    
    
    private static function generateProjectFile($modelData)
    {
        $ret = new Models\ProjectFile();

        if (isset($modelData['projectId'])) {
            $ret->setProjectId($modelData['projectId']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFilename($modelData['filename']);
        }
        if (isset($modelData['token'])) {
            $ret->setToken($modelData['token']);
        }
        if (isset($modelData['userId'])) {
            $ret->setUserId($modelData['userId']);
        }
        if (isset($modelData['mime'])) {
            $ret->setMime($modelData['mime']);
        }
        
        return $ret;
    }
    
    
    private static function generateUserPersonalInformation($modelData)
    {
        $ret = new Models\UserPersonalInformation();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['userId'])) {
            $ret->setUserId($modelData['userId']);
        }
        if (isset($modelData['firstName'])) {
            $ret->setFirstName($modelData['firstName']);
        }
        if (isset($modelData['lastName'])) {
            $ret->setLastName($modelData['lastName']);
        }
        if (isset($modelData['mobileNumber'])) {
            $ret->setMobileNumber($modelData['mobileNumber']);
        }
        if (isset($modelData['businessNumber'])) {
            $ret->setBusinessNumber($modelData['businessNumber']);
        }
        if (isset($modelData['languagePreference'])) {
            $ret->setLanguagePreference($modelData['languagePreference']);
        }
        if (isset($modelData['jobTitle'])) {
            $ret->setJobTitle($modelData['jobTitle']);
        }
        if (isset($modelData['address'])) {
            $ret->setAddress($modelData['address']);
        }
        if (isset($modelData['city'])) {
            $ret->setCity($modelData['city']);
        }
        if (isset($modelData['country'])) {
            $ret->setCountry($modelData['country']);
        }
        if (isset($modelData['receive_credit'])) {
            $ret->setReceiveCredit($modelData['receive_credit'] ? true : false);
        }
        
        return $ret;
    }
    
    private static function generateLocale($modelData)
    {
        $ret = new Models\Locale();
        
        if (isset($modelData['languageName'])) {
            $ret->setLanguageName($modelData['languageName']);
        }
        if (isset($modelData['languageCode'])) {
            $ret->setLanguageCode($modelData['languageCode']);
        }
        if (isset($modelData['countryName'])) {
            $ret->setCountryName($modelData['countryName']);
        }
        if (isset($modelData['countryCode'])) {
            $ret->setCountryCode($modelData['countryCode']);
        }
        
        return $ret;
    }
    
    private static function generateBannedUser($modelData)
    {
        $ret = new Models\BannedUser();
        
        if (isset($modelData['userId'])) {
            $ret->setUserId($modelData['userId']);
        }
        if (isset($modelData['userIdAdmin'])) {
            $ret->setUserIdAdmin($modelData['userIdAdmin']);
        }
        if (isset($modelData['banType'])) {
            $ret->setBanType($modelData['banType']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }
        if (isset($modelData['bannedDate'])) {
            $ret->setBannedDate($modelData['bannedDate']);
        }
        
        return $ret;
    }
    
    private static function generateBannedOrganisation($modelData)
    {
        $ret = new Models\BannedOrganisation();
        
        if (isset($modelData['orgId'])) {
            $ret->setOrgId($modelData['orgId']);
        }
        if (isset($modelData['userIdAdmin'])) {
            $ret->setUserIdAdmin($modelData['userIdAdmin']);
        }
        if (isset($modelData['banType'])) {
            $ret->setBanType($modelData['banType']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }
        if (isset($modelData['bannedDate'])) {
            $ret->setBannedDate($modelData['bannedDate']);
        }
        
        return $ret;
    }
    
    private static function generateOAuthResponse($modelData)
    {
        $ret = new Models\OAuthResponse();
        
        if (isset($modelData['token'])) {
            $ret->setToken($modelData['token']);
        }
        
        if (isset($modelData['token_type'])) {
            $ret->setTokenType($modelData['token_type']);
        }
        
        if (isset($modelData['expires'])) {
            $ret->setExpires($modelData['expires']);
        }
        
        if (isset($modelData['expires_in'])) {
            $ret->setExpiresIn($modelData['expires_in']);
        }
        
        return $ret;
    }
    
    private static function generateWorkflowGraph($modelData)
    {
        
        $ret = new Models\WorkflowGraph();
        
        if (isset($modelData['projectId'])) {
            $ret->setProjectId($modelData['projectId']);
        }
        
        if (isset($modelData['allNodes'])) {
            
            foreach ($modelData['allNodes'] as $aNode) {
                $ret->appendAllNodes(self::generateWorkflowNode($aNode));
            }
        }
        
        if (isset($modelData['rootNode'])) {
            
            foreach ($modelData['rootNode'] as $rootNodePart) {
                $ret->appendRootNode($rootNodePart);
            }
        }
        
        return $ret;
    }
    
    private static function generateWorkflowNode($modelData)
    {
        $ret = new Models\WorkflowNode();
        
        if (isset($modelData['taskId'])) {
            $ret->setTaskId($modelData['taskId']);
        }
        
        if (isset($modelData['task'])) {
            $ret->setTask(self::generateTask($modelData['task']));
        }
        
        if (isset($modelData['previous'])) {
            foreach ($modelData['previous'] as $prev) {
                $ret->appendPrevious($prev);
            }
        }
        
        if (isset($modelData['next'])) {
            foreach ($modelData['next'] as $next) {
                $ret->appendNext($next);
            }
        }
        
        return $ret;
    }
    
    private static function generateUserFeedback($modelData)
    {
        $ret = new Emails\UserFeedback();
        if (isset($modelData['email_type'])) {
            $ret->setEmailType($modelData['email_type']);
        }
        if (isset($modelData['task_id'])) {
            $ret->setTaskId($modelData['task_id']);
        }
        if (isset($modelData['claimant_id'])) {
            $ret->setClaimantId($modelData['claimant_id']);
        }
        if (isset($modelData['feedback'])) {
            $ret->setFeedback($modelData['feedback']);
        }
        return $ret;
    }
    
    private static function generateOrgFeedback($modelData)
    {
        error_log("In Model factory");
        $temp = print_r($modelData,true);
        error_log($temp);            
        $ret = new Emails\OrgFeedback();
        if (isset($modelData['email_type'])) {
            $ret->setEmailType($modelData['email_type']);
        }
        if (isset($modelData['task_id'])) {
            $ret->setTaskId($modelData['task_id']);
        }
        if (isset($modelData['claimant_id'])) {
            $ret->setClaimantId($modelData['claimant_id']);
        }
        if (isset($modelData['feedback'])) {
            $ret->setFeedback($modelData['feedback']);
        }
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        return $ret;
    }
}
