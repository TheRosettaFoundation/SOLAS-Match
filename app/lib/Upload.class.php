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

	/*
	 * For a named filename, save file that have been uploaded by form submission.
	 * The file has been specified in a form element <input type="file" name="myfile">
	 * We access that file through PHP's $_FILES array.
	 */
	public static function saveUploadedFile($field_name, $org_id, $task_id) {
		/* 
		 * Right now we're assuming that there's one file, but I think it can also be
		 * an array of multiple files.
		 */
		$ret = false;
		
		if ($_FILES[$field_name]['error'] == UPLOAD_ERR_FORM_SIZE) {
			throw new Exception('Sorry, the file you tried uploading is too large. Please choose a smaller file, or break the file into sub-parts.');
		}

		// Save this original file to upload_path/org-N/task-N/v-N
		$uploaddir = TaskFile::absolutePath($org_id, $task_id);
		self::_saveUploadedFileToFS($uploaddir, $field_name);
		return TaskFile::recordUploadedFile($task_id, $uploaddir, $_FILES[$field_name]['name'], $_FILES[$field_name]['type']);			}
	}

	/*
	 * $files_file is the name of the parameter of the file we want to access
	 * in the $_FILES global array.
	 */
	private static function _saveUploadedFileToFS($uploaddir, $files_file)
	{
		$ret = false;
		if ((is_dir($uploaddir)) ? true : mkdir($uploaddir, 0755, true)) {
			$uploadfile = $uploaddir.DIRECTORY_SEPARATOR.basename($_FILES[$files_file]['name']);		
			$ret = (move_uploaded_file($_FILES[$files_file]['tmp_name'], $uploadfile));
		}
		return $ret;
	}
}