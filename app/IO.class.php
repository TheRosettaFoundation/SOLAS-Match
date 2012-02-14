<?php

/**
 * Input/Output including parsing input, and formatting output for URLs.
 */
class IO {
	
	function __construct() {
	}

	function get($get_var_name)
	{
		return isset($_GET[$get_var_name]) ? $this->cleanseInput($_GET[$get_var_name]) : false;
	}

	function post($post_var_name)
	{
		return isset($_POST[$post_var_name]) ? $this->cleanseInput($_POST[$post_var_name]) : false;
	}

	// Cleanse input, but keep HTML tags.
	function postHTML($post_var_name)
	{
		return isset($_POST[$post_var_name]) ? $this->cleanseInputKeepHTML($_POST[$post_var_name]) : false;
	}
	
	// Cleanse input: make safe from SQL injection.
	function cleanseInput($str)
	{
		$str = $this->cleanseInputKeepHTML($str);
		//mysql_real_escape_string
		return strip_tags(trim($str));
	}

	// Allow to keep HTML tags.
	function cleanseInputKeepHTML($str)
	{
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return $str;
	}

	function formatForURL($text)
	{
		// Encodes the given text to be included as part of a URL
		return rawurlencode($text);
	}

	function formatFromURL($text)
	{
		// Decodes the given text from URL representation
		return rawurldecode($text);
	}
	
	public static function timeSinceSqlTime($sql_string) {
		return self::timeSince(strtotime($sql_string));
	}

	static function timeSince($unix_time)
	{
		// From http://www.dreamincode.net/code/snippet86.htm
		// Array of time period chunks
		$chunks = array(
		    array(60 * 60 * 24 * 365 , 'year'),
		    array(60 * 60 * 24 * 30 , 'month'),
		    array(60 * 60 * 24 * 7, 'week'),
		    array(60 * 60 * 24 , 'day'),
		    array(60 * 60 , 'hour'),
		    array(60 , 'minute'),
		);
	   
		$today = time(); /* Current unix time  */
		$since = $today - $unix_time;
	   
		// $j saves performing the count function each time around the loop
		for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		    $seconds = $chunks[$i][0];
		    $name = $chunks[$i][1];
		   
		    // finding the biggest chunk (if the chunk fits, break)
		    if (($count = floor($since / $seconds)) != 0) {
		        // DEBUG print "<!-- It's $name -->\n";
		        break;
		    }
		}
	   
		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
	   
		if ($i + 1 < $j) {
		    // now getting the second item
		    $seconds2 = $chunks[$i + 1][0];
		    $name2 = $chunks[$i + 1][1];
		   
		    // add second item if it's greater than 0
		    if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
		        $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
		    }
		}
		return $print;
	}
	
	/*
	 * For a named filename, save file that have been uploaded by form submission.
	 * The file has been specified in a form element <input type="file" name="myfile">
	 * We access that file through PHP's $_FILES array.
	 */
	public static function saveUploadedFile($myfile, $org_id, $task_id)
	{
		/* 
		 * Right now we're assuming that there's one file, but I think it can also be
		 * an array of multiple files.
		 */
		$ret = false;
		if ($_FILES[$myfile]['error'] == UPLOAD_ERR_OK)
		{
			// Save this original file to upload_path/org-N/task-N/v-N
			$uploaddir = TaskFile::absolutePath($org_id, $task_id);
			if (self::saveUploadedFileToFS($uploaddir, $myfile))
			{
				$task = new Task($task_id);
				$ret = $task->recordUploadedFile($uploaddir, $_FILES[$myfile]['name'], $_FILES[$myfile]['type']);
			}
		}
		else if ($_FILES[$myfile]['error'] == UPLOAD_ERR_FORM_SIZE)
		{
			echo 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.<br />';
			echo '<pre>';
			print_r($_FILES);
			echo '</pre>';
		}
		return $ret;
	}
	
	/*
	 * For a named filename, save file that have been uploaded by form submission.
	 * The file has been specified in a form element <input type="file" name="myfile">
	 * We access that file through PHP's $_FILES array.
	 */
	function saveUploadedEditedFile($myfile, &$task_file)
	{
		/* 
		 * Right now we're assuming that there's one file, but I think it can also be
		 * an array of multiple files.
		 */
		$ret = false;
		if ($_FILES[$myfile]['error'] == UPLOAD_ERR_OK)
		{
			// Save this original file to upload_path/org-N/task-N/v-N
			$version = $task_file->nextVersion();
		echo "$version";
			$uploaddir = TaskFile::absolutePath($this->s, $task_file->organisationID(), $task_file->taskID(), $version);
			if ($this->saveUploadedFileToFS($uploaddir, $myfile))
			{
				$ret = $task_file->recordNewlyUploadedVersion($version, $_FILES[$myfile]['name'], $_FILES[$myfile]['type']);
			}
		}
		return $ret;
	}
	
	/*
	 * $files_file is the name of the parameter of the file we want to access
	 * in the $_FILES global array.
	 */
	private static function saveUploadedFileToFS($uploaddir, $files_file)
	{
		$ret = false;
		if ((is_dir($uploaddir)) ? true : mkdir($uploaddir, 0755, true))
		{
			$uploadfile = $uploaddir.DIRECTORY_SEPARATOR.basename($_FILES[$files_file]['name']);		
			$ret = (move_uploaded_file($_FILES[$files_file]['tmp_name'], $uploadfile));
		}
		return $ret;
	}
	
	/*
	 * Pass a requested file back to the browser
	 */
	function downloadFile($absoluteFilePath, $contentType)
	{
		if ($fd = fopen ($absoluteFilePath, "r")) {
			$fsize = filesize($absoluteFilePath);
			$path_parts = pathinfo($absoluteFilePath);
			$ext = strtolower($path_parts["extension"]);
			header('Content-type: '.$contentType);
			header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"');
			header("Content-length: $fsize");
			header("Cache-control: private"); //use this to open files directly
			while(!feof($fd)) {
				$buffer = fread($fd, 2048);
				echo $buffer;
			}
		}
		fclose ($fd);
		return;
	}
	
	/**
	 * Return an integer value of the max file size that can be uploaded to the system,
	 * denominated in megabytes.
	 */
	public static function maxFileSizeMB() {
		$bytes = self::maxFileSizeBytes();
		return round($bytes/1024, 1);
	}

	public static function maxFileSizeBytes() {
		$settings = new Settings();
		return $settings->get('files.max_upload_file_size');
	}

}
