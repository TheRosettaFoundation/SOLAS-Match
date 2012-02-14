<?php

class Upload {
	public static function hasFileBeenUploaded($field_name) {
		return (isset($_FILES[$field_name]) && is_uploaded_file($_FILES[$field_name]['tmp_name']));
	}
}