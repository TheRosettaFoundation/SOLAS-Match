<?php

class Upload {
	public static function hasFileBeenSuccessfullyUploaded($field_name) {
		return (
			self::hasFormBeenSubmitted($field_name) 
			&& self::isUploadedFile($field_name)
			&& self::isUploadedWithoutError($field_name)
		);
	}

	public static function hasFormBeenSubmitted($field_name) {
		return isset($_FILES[$field_name]);
	}

	public static function isUploadedFile($field_name) {
		return is_uploaded_file($_FILES[$field_name]['tmp_name']);
	}

	public static function isUploadedWithoutError($field_name) {
		return $_FILES[$field_name]['error'] == UPLOAD_ERR_OK;
	}
}