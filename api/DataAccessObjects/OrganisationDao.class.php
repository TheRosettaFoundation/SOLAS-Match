<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

//! The Organisation Data Access Object for the API
/*!
  A class for setting and retrieving Badge related data from the database.
  Used by the API Route Handlers to supply info requested through the API and perform actions.
  All data is retrieved an input with direct access to the database using stored procedures.
*/

require_once __DIR__."/../../Common/protobufs/models/Organisation.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class OrganisationDao
{
    //! Retrieve a single Organisation from the database
    /*!
      Gets a single Organisation object from the database. If a valid Organisation id is passed then that Organisation
      will be returned. If a valid name is passed then the Organisation with that name will be returned. If both
      parameters are null then this will return null.
      @param int $orgId is the id of an organisation
      @param String $name is the name of an Organisation
      @return A single organisation object or null
    */
    public static function getOrg($orgId = null, $name = null)
    {
        $org = null;

        if (!is_null($orgId) || !is_null($name)) {
            $args = Lib\PDOWrapper::cleanseNull($orgId).",".
                Lib\PDOWrapper::cleanseNullOrWrapStr($name).",".
                "null, null, null, null, null, null, null";

            $result = Lib\PDOWrapper::call("getOrg", $args);
            if (is_array($result)) {
                $org = Common\Lib\ModelFactory::buildModel("Organisation", $result[0]);
            }
        }
        return $org;
    }

    //! Retrieve a single Organisation's Extended Profile data from the database
    /*!
      @param int $orgId is the id of an OrganisationExtendedProfile
      @return A single OrganisationExtendedProfile object or null
    */
    public static function getOrganisationExtendedProfile($orgId = null)
    {
        $org = null;

        $result = Lib\PDOWrapper::call('getOrganisationExtendedProfile', Lib\PDOWrapper::cleanseNull($orgId));
        if (!empty($result)) {
            $org = Common\Lib\ModelFactory::buildModel("OrganisationExtendedProfile", $result[0]);
        }
        return $org;
    }

    //! Get an Organisation object from the database
    /*!
      Used to retrieve an Organisation from the database. It accepts a number of arguments that it uses to filter
      the list of Organisations returned. If any argument is null or '' then it will be ignored. If all arguments
      are null or '' then it will return a list of all Organisations.
      @param int|null $id is the id of the Organisation or null
      @param string|null $name is the name of the Organisation or null
      @param string|null $homepage is the home page URL of the Organisation or null
      @param string|null $bio is the bography of the Organisation or null
      @param string|null $email is the email address provided by the Organisation or null
      @param string|null $address is the address of the Organisation or null
      @param string|null $city is the City in which the Organisation is based or null
      @param string|null $country is the Country in which the Organisation is based or null
      @param string|null $regionalFocus is the area on which the Organisation is focused or null
      @return Returns an Array of Organisations or an empty array if none match
    */
    public static function getOrgs(
        $id = null,
        $name = null,
        $homepage = null,
        $bio = null,
        $email = null,
        $address = null,
        $city = null,
        $country = null,
        $regionalFocus = null
    ) {
        $ret = array();
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($name).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($homepage).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($bio).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($email).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($address).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($city).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($country).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($regionalFocus);
        $result = Lib\PDOWrapper::call("getOrg", $args);
        if (is_array($result)) {
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Organisation", $row);
            }
        } else {
            $ret = null;
        }
        return $ret;
    }
    
    //! Search for an Organisation by name
    /*!
      Search for an Organisation by name. This will return a list of Organisations with names containing the text
      used as an argument. For example a search for "ABC" would return "ABColumbia" and any other Organisation
      containing that string.
      @param string $orgName is the string the search is based on
      @return Returns a list of Organisations matching the search requirements or null
    */
    public static function searchForOrg($orgName)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseWrapStr($orgName);
        $result = Lib\PDOWrapper::call("searchForOrg", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Organisation", $row);
            }
        }
        return $ret;
    }
    
    //! Create/Update an Organisation
    /*!
      Used to create or update an Organisation. If the Organisation object passed to this function contains an
      Organisation id then the Organisation with that id will be updated so that its fields match that of the
      Organisation object argument. If the Organisation does not have an id (i.e. it is null) then a new Organisation
      will be created with the data specified in the object. The Organisation object returned by this function will
      contain the id of the created Organisation in this case.
      @param Organisation $org is the Organisation object that contains the data for the create/update
      @return Returns an updated Organisation object to match what is now in the DB
    */
    public static function insertAndUpdate($org)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($org->getId()).",".
            Lib\PDOWrapper::cleanseWrapStr($org->getHomepage()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getBiography()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getEmail()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getAddress()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getCity()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getCountry()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getRegionalFocus());
        
        $result = Lib\PDOWrapper::call("organisationInsertAndUpdate", $args);
        if (is_array($result)) {
            $ret = Common\Lib\ModelFactory::buildModel("Organisation", $result[0]);
        }
        return $ret;
    }

    //! Create/Update an Organisation's Extended Profile
    /*!
      Used to create or update an OrganisationExtendedProfile.
      If an OrganisationExtendedProfile with the same id as the OrganisationExtendedProfile object passed to this function exists
      then the OrganisationExtendedProfile will be updated so that its fields match that of the OrganisationExtendedProfile object argument.
      Otherwise a new OrganisationExtendedProfile will be created with the data specified in the OrganisationExtendedProfile object argument.
      @param OrganisationExtendedProfile $org is the OrganisationExtendedProfile object that contains the data for the create/update
      @return Returns null
    */
    public static function insertAndUpdateExtendedProfile($org)
    {
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($org->getId()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getFacebook()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getLinkedin()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getPrimaryContactName()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getPrimaryContactTitle()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getPrimaryContactEmail()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getPrimaryContactPhone()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getOtherContacts()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getStructure()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getAffiliations()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getUrlVideo1()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getUrlVideo2()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getUrlVideo3()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getSubjectMatters()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getActivitys()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getEmployees()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getFundings()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getFinds()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getTranslations()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getRequests()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getContents()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getPages()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getSources()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getTargets()) . ',' .
            Lib\PDOWrapper::cleanseWrapStr($org->getOftens());
        Lib\PDOWrapper::call('organisationExtendedProfileInsertAndUpdate', $args);
        return null;
    }

    //! Delete an Organisation
    /*!
      Permanently delete an Organisation from the system. There is currently no Organisation archive so it is not
      possible to revive a deleted Organisation
      @param int $orgId is the id of the Organisation being deleted
      @return Returns '1' if the specified Organisation existed and was deleted, '0' otherwise
    */
    public static function delete($orgId)
    {
        $args = Lib\PDOWrapper::cleanse($orgId);
        $result= Lib\PDOWrapper::call("deleteOrg", $args);
        return $result[0]['result'];
    }

    //! Get a list of Users that are tracking an Organisation
    /*!
      Get a list of Users that are tracking an Organisation. If a User is tracking an Organisation they should receive
      email updates on Organisation activity
      @param int $orgId is the id of the Organisation
      @return Returns a list of Users that are tracking the specified Organisation or null
    */
    
    
    public static function getUsersTrackingOrg($orgId)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($orgId);
        $result = Lib\PDOWrapper::call("getUsersTrackingOrg", $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("User", $row);
            }
        }
        return $ret;
    }

    public static function getSubscription($org_id)
    {
        $result = Lib\PDOWrapper::call('getSubscription', Lib\PDOWrapper::cleanse($org_id));
        if (empty($result)) return [];
        return $result[0];
    }

    public static function updateSubscription($org_id, $level, $spare, $start_date, $comment)
    {
        $args = Lib\PDOWrapper::cleanse($org_id) . ',' .
                Lib\PDOWrapper::cleanse($level) . ',' .
                Lib\PDOWrapper::cleanse($spare) . ',' .
                Lib\PDOWrapper::cleanseWrapStr($start_date) . ',' .
                Lib\PDOWrapper::cleanseWrapStr($comment);
        $result = Lib\PDOWrapper::call('updateSubscription', $args);
        return $result[0]['result'];
    }


   
}
