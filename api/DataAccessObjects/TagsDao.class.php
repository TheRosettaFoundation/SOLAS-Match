<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

//! Tags Data Access Object for setting getting data about Tags in the API
/*!
  The Tags Data Access Object for manipulating data in the Database. It has direct Database access through the use
  of the PDOWrapper. It is used by the API Route Handlers for retrieving and setting data requested through the API.
*/

require_once __DIR__."/../../Common/protobufs/models/Tag.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class TagsDao
{
    //! Retrieve a single Tag from the database
    /*!
      Get a single Tag from the database. If a valid tag id is passed then that Tag will be returned. If a valid label
      is passed then the Tag with that label will be returned. If both parameters are null then null will be returned.
      @param int $tagId is the id of a Tag
      @param String $label is the title of a Tag
      @return Returns a single Tag object
    */
    public static function getTag($tagId = null, $label = null)
    {
        $ret = null;
        if (!is_null($tagId) && !is_null($label)) {
            $args = Lib\PDOWrapper::cleanseNull($tagId).", ".
                Lib\PDOWrapper::cleanseNullOrWrapStr($label).", null";
            if ($result = Lib\PDOWrapper::call("getTag", $args)) {
                $ret = Common\Lib\ModelFactory::buildModel("Tag", $result[0]);
            }
        }
        return $ret;
    }

    //! Retrieve a Tag/Tags from the database
    /*!
      Retrieve a list of Tags from the database. The list can be filtered using the parameters. If null is passed for
      any parameter it will be ignored. If null is passed for all parameters all Tag objects in the system will be
      returned. The limit argument can be used to limit the number of Tag objects returned.
      @param int $id is the id of the requested Tag object or null
      @param string $label is the text value of a Tag object or null
      @param int $limit is ued to limit the number of Tag objects returned.
      @return Returns a list of Tag objects as filtered by the input parameters or null.
    */
    public static function getTags($id = null, $label = null, $limit = 30)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($label).",".
            Lib\PDOWrapper::cleanseNull($limit);
        if ($result = Lib\PDOWrapper::call("getTag", $args)) {
            $ret = array();
            foreach ($result as $tag) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $tag);
            }
        }
        return $ret;
    }

    //! Create a new Tag
    /*!
      Create a new Tag in the system. To create a Tag pass a unique label for it.
      @param string $label is the text of the tag. It must be unique in the system.
      @return Returns the created Tag object or null on failure.
    */
    public static function create($label)
    {
        $tag = new Common\Protobufs\Models\Tag();
        $tag->setLabel($label);
        return self::save($tag);
    }

    //! Save Tag details to the database
    /*!
      Used to save a Tag to the database. Can be used to create a new Tag or update an existing Tag. <b>Note:</b> Tag
      update functionality is not yet implemented.
      @param Tag $tag is the Tag object that is being created/updated
      @return Returns the created/updated Tag object or null on failure.
    */
    public static function save($tag)
    {
        if (!$tag->hasId()) {
            return self::insert($tag);
        } else {
            error_log("Error: updating existing tag functionality not implemented.");
            die;
        }
    }

    //! Insert a Tag into the database
    private static function insert($tag)
    {
        $args = Lib\PDOWrapper::cleanseWrapStr($tag->getLabel());
        $result = Lib\PDOWrapper::call("tagInsert", $args);
        if ($result) {
            return Common\Lib\ModelFactory::buildModel("Tag", $result[0]);
        } else {
            return null;
        }
    }
    
    //! Get a list of the most used Tags
    /*!
      Get a list of the most popular Tags in the system. Popularity is determined by the number Projects that have the
      Tag.
      @param int $limit Used to limit the number of Tag objects returned
      @return Returns a list of Tag objects.
    */
    public static function getTopTags($limit = 30)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($limit);
        if ($result = Lib\PDOWrapper::call("getTopTags", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }

    //! Delete a Tag
    /*!
      Permanently delete a Tag from the system. This will automatically remove the Tag from all Projects and Tasks.
      @param int $id is the id of the Tag being deleted.
      @return Returns '1' if the Tag was successfully deleted, '0' otherwise
    */
    public static function delete($id)
    {
        $args = Lib\PDOWrapper::cleanseNull($id);
        if ($result = Lib\PDOWrapper::call("deleteTag", $args)) {
            return $result[0]['result'];
        }
        return null;
    }

    //! Search for a Tag
    /*!
      Find a Tag (or Tags) that contain the text passed to this function. The input parameter can be text from the
      beginning, the middle or the end od the Tag label.
      @param string $name is the text to search for.
      @return Returns an array of Tag objects
    */
    public static function searchForTag($name)
    {
        $ret = array();
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($name);
        if ($result = Lib\PDOWrapper::call("searchForTag", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Tag", $row);
            }
        }
        return $ret;
    }
    
    //! Update the list of Tags associated to a project
    /*!
      This function is used to update the ProjectTags table for a single Project. This function removes all Project
      Tags that are not on the list and adds any that are.
      @param int $projectId is the ID of the Project whose Tags are being updated.
      @param Array<Tag> $updatedProjectTagList is the list of Tags.
      @return Returns the updated list of Tags (same as input Tag list).
    */
    public static function updateTags($projectId, $updatedProjectTagList)
    {
        if ($updatedProjectTagList) {
            if (is_null($oldProjectTagList = ProjectDao::getTags($projectId))) {
                $oldProjectTagList = array();
            }
            
            $tagsToRemove = array_udiff(
                $oldProjectTagList,
                $updatedProjectTagList,
                '\SolasMatch\API\DAO\TagsDao::compareTo'
            );

            if (!empty($tagsToRemove)) {
                foreach ($tagsToRemove as $removedTag) {
                    ProjectDao::removeProjectTag($projectId, $removedTag->getId());
                }
            }

            $tagsToAdd = array_udiff(
                $updatedProjectTagList,
                $oldProjectTagList,
                '\SolasMatch\API\DAO\TagsDao::compareTo'
            );

            if (!empty($tagsToAdd)) {
                foreach ($tagsToAdd as $newTag) {
                    if ($tagExists = TagsDao::getTags(null, $newTag->getLabel())) {
                        ProjectDao::addProjectTag($projectId, $tagExists[0]->getId());
                    } else {
                        $tag = TagsDao::create($newTag->getLabel());
                        ProjectDao::addProjectTag($projectId, $tag->getId());
                    }
                }
            }
        }
        return $updatedProjectTagList;
    }

    private static function compareTo($tag1, $tag2)
    {
        return strcasecmp($tag1->getLabel(), $tag2->getLabel());
    }
}
