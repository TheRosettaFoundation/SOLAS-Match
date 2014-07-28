<?php

namespace SolasMatch\Common\Lib;

use \SolasMatch\Common\Protobufs\Models as Models;

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

class ModelFactory
{
    public static function buildModel($modelName, $modelData)
    {
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
        if (isset($modelData['request-datetime'])) {
            $ret->setRequestTime($modelData['request-datetime']);
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
        if (isset($modelData['project_id'])) {
            $ret->setProjectId($modelData['project_id']);
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
        if (isset($modelData['word-count'])) {
            $ret->setWordCount($modelData['word-count']);
        }
        if (isset($modelData['created-time'])) {
            $ret->setCreatedTime($modelData['created-time']);
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

        $ret->setTargetLocale($targetLocale);
        
        if (isset($modelData['taskType_id'])) {
            $ret->setTaskType($modelData['taskType_id']);
        }
        if (isset($modelData['taskStatus_id'])) {
            $ret->setTaskStatus($modelData['taskStatus_id']);
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
        if (isset($modelData['content-type'])) {
            $ret->setContentType($modelData['content-type']);
        }
        if (isset($modelData['upload-time'])) {
            $ret->setUploadTime($modelData['upload-time']);
        }
        if (isset($modelData['user_id-claimed'])) {
            $ret->setUserIdClaimed($modelData['user_id-claimed']);
        }
        if (isset($modelData['user_id-archived'])) {
            $ret->setUserIdArchived($modelData['user_id-archived']);
        }
        if (isset($modelData['prerequisites'])) {
            $ret->setPrerequisites($modelData['prerequisites']);
        }
        if (isset($modelData['user_id-taskCreator'])) {
            $ret->setUserIdTaskCreator($modelData['user_id-taskCreator']);
        }
        if (isset($modelData['archived-date'])) {
            $ret->setArchivedDate($modelData['archived-date']);
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
        if (isset($modelData['uid'])) {
            $ret->setKey($modelData['uid']);
        }
        if (isset($modelData['request-time'])) {
            $ret->setRequestTime($modelData['request-time']);
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
        if (isset($modelData['country'])) {
            $ret->setName($modelData['country']);
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
        if (isset($modelData['language'])) {
            $ret->setName($modelData['language']);
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
        if (isset($modelData['home-page'])) {
            $ret->setHomePage($modelData['home-page']);
        }
        if (isset($modelData['e-mail'])) {
            $ret->setEmail($modelData['e-mail']);
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
        if (isset($modelData['regional-focus'])) {
            $ret->setRegionalFocus($modelData['regional-focus']);
        }

        return $ret;
    }

    private static function generateTaskMetadata($modelData)
    {
        $ret = new Models\TaskMetadata();

        if (isset($modelData['task_id'])) {
            $ret->setId($modelData['task_id']);
        }
        if (isset($modelData['version_id'])) {
            $ret->setVersion($modelData['version_id']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFilename($modelData['filename']);
        }
        if (isset($modelData['content-type'])) {
            $ret->setContentType($modelData['content-type']);
        }
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['upload-time'])) {
            $ret->setUploadTime($modelData['upload-time']);
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
        if (isset($modelData['display-name'])) {
            $ret->setDisplayName($modelData['display-name']);
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
        
        if (isset($modelData['created-time'])) {
            $ret->setCreatedTime($modelData['created-time']);
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
        if (isset($modelData['last-sent'])) {
            $ret->setLastSent($modelData['last-sent']);
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
        if (isset($modelData['project_id'])) {
            $ret->setProjectId($modelData['project_id']);
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
        if (isset($modelData['word-count'])) {
            $ret->setWordCount($modelData['word-count']);
        }
        if (isset($modelData['created-time'])) {
            $ret->setCreatedTime($modelData['created-time']);
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

        $ret->setTargetLocale($targetLocale);
        
        if (isset($modelData['task-type_id'])) {
            $ret->setTaskType($modelData['task-type_id']);
        }
        if (isset($modelData['task-status_id'])) {
            $ret->setTaskStatus($modelData['task-status_id']);
        }
        if (isset($modelData['published'])) {
            $ret->setPublished($modelData['published']);
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
        if (isset($modelData['organisation_id'])) {
            $ret->setOrganisationId($modelData['organisation_id']);
        }
        if (isset($modelData['impact'])) {
            $ret->setImpact($modelData['impact']);
        }
        if (isset($modelData['reference'])) {
            $ret->setReference($modelData['reference']);
        }
        if (isset($modelData['word-count'])) {
            $ret->setWordCount($modelData['word-count']);
        }
        if (isset($modelData['created'])) {
            $ret->setCreatedTime($modelData['created']);
        }
        if (isset($modelData['status'])) {
            $ret->setStatus($modelData['status']);
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
        if (isset($modelData['organisation_id'])) {
            $ret->setOrganisationId($modelData['organisation_id']);
        }
        if (isset($modelData['reference'])) {
            $ret->setReference($modelData['reference']);
        }
        if (isset($modelData['word-count'])) {
            $ret->setWordCount($modelData['word-count']);
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
       
        if (isset($modelData['user_id-archived'])) {
            $ret->setUserIdArchived($modelData['user_id-archived']);
        }
        if (isset($modelData['user_id-projectCreator'])) {
            $ret->setUserIdProjectCreator($modelData['user_id-projectCreator']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFileName($modelData['filename']);
        }
        if (isset($modelData['file-token'])) {
            $ret->setFileToken($modelData['file-token']);
        }
        if (isset($modelData['mime-type'])) {
            $ret->setMimeType($modelData['mime-type']);
        }
        if (isset($modelData['archived-date'])) {
            $ret->setArchivedDate($modelData['archived-date']);
        }
        if (isset($modelData['tags'])) {
            $ret->setTags($modelData['tags']);
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

        if (isset($modelData['project_id'])) {
            $ret->setProjectId($modelData['project_id']);
        }
        if (isset($modelData['filename'])) {
            $ret->setFilename($modelData['filename']);
        }
        if (isset($modelData['file-token'])) {
            $ret->setToken($modelData['file-token']);
        }
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['mime-type'])) {
            $ret->setMime($modelData['mime-type']);
        }
        
        return $ret;
    }
    
    
    private static function generateUserPersonalInformation($modelData)
    {
        $ret = new Models\UserPersonalInformation();

        if (isset($modelData['id'])) {
            $ret->setId($modelData['id']);
        }
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['first-name'])) {
            $ret->setFirstName($modelData['first-name']);
        }
        if (isset($modelData['last-name'])) {
            $ret->setLastName($modelData['last-name']);
        }
        if (isset($modelData['mobile-number'])) {
            $ret->setMobileNumber($modelData['mobile-number']);
        }
        if (isset($modelData['business-number'])) {
            $ret->setBusinessNumber($modelData['business-number']);
        }
        if (isset($modelData['job-title'])) {
            $ret->setJobTitle($modelData['job-title']);
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
            $ret->setReceiveCredit($modelData['receive_credit'] == "\x01" ? true : false);
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
        
        if (isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if (isset($modelData['user_id-admin'])) {
            $ret->setUserIdAdmin($modelData['user_id-admin']);
        }
        if (isset($modelData['bannedtype_id'])) {
            $ret->setBanType($modelData['bannedtype_id']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }
        if (isset($modelData['banned-date'])) {
            $ret->setBannedDate($modelData['banned-date']);
        }
        
        return $ret;
    }
    
    private static function generateBannedOrganisation($modelData)
    {
        $ret = new Models\BannedOrganisation();
        
        if (isset($modelData['org_id'])) {
            $ret->setOrgId($modelData['org_id']);
        }
        if (isset($modelData['user_id-admin'])) {
            $ret->setUserIdAdmin($modelData['user_id-admin']);
        }
        if (isset($modelData['bannedtype_id'])) {
            $ret->setBanType($modelData['bannedtype_id']);
        }
        if (isset($modelData['comment'])) {
            $ret->setComment($modelData['comment']);
        }
        if (isset($modelData['banned-date'])) {
            $ret->setBannedDate($modelData['banned-date']);
        }
        
        return $ret;
    }
}
