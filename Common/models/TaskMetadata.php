<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TaskMetadata
 *
 * @author sean
 */
class TaskMetadata {
    public $id;
    public $version;
    public $filename;
    public $contentType;
    public $userID;
    public $uploadTime;
    
    function __construct($params=array()) {
        if (isset($params['task_id'])) {
                $this->id = $params['task_id'];
        }
        if (isset($params['version_id'])) {
                $this->version = $params['version_id'];
        }
        if (isset($params['filename'])) {
                $this->filename = $params['filename'];
        }
        if (isset($params['content_type'])) {
                $this->contentType = $params['content_type'];
        }
        if (isset($params['user_id'])) {
                $this->userID = $params['user_id'];
        }
        if (isset($params['upload_time'])) {
                $this->uploadTime = $params['upload_time'];
        }
    }

    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function setFilename($filename) {
        $this->filename = $filename;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function getUserID() {
        return $this->userID;
    }

    public function setUserID($userID) {
        $this->userID = $userID;
    }

    public function getUploadTime() {
        return $this->uploadTime;
    }

    public function setUploadTime($uploadTime) {
        $this->uploadTime = $uploadTime;
    }

        //put your code here
}

?>
