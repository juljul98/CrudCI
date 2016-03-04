<?php

function today(){ return date("Y-m-d H:i:s"); }

function generate_json($data){
	header("access-control-allow-origin: *");
	header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
	header('Content-type: application/json');
	echo json_encode($data);
}

function formatsize($size, $precision = 2){
	if(empty($size) || $size <= 0) { 
		return 0;
	} else {
		$base = log($size) / log(1024);
		$suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
		return round(pow(1024, $base - floor($base)), $precision) . ' ' .$suffixes[floor($base)];
	}
}

function randomstring($length = 20) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

### START ENCRYPTING AND DECRYPTING AREA ###
function get_rnd_iv($iv_len){
	$iv = '';
	while ($iv_len-- > 0){ $iv .= chr(mt_rand() & 0xff); }
	return $iv;
}

function encrypt($plain_text, $password = 't0P45i', $iv_len = 16){
	$plain_text .= "\x13";
	$n = strlen($plain_text);
	
	if ($n % 16) $plain_text .= str_repeat("\0", 16 - ($n % 16));
	$i = 0;
	$enc_text = get_rnd_iv($iv_len);
	$iv = substr($password ^ $enc_text, 0, 512);
	
	while ($i < $n){
		$block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
		$enc_text .= $block;
		$iv = substr($block . $iv, 0, 512) ^ $password;
		$i += 16;
	}
	
	return base64_encode($enc_text);
}
	
function decrypt($enc_text, $password = 't0P45i', $iv_len = 16){
	$enc_text = base64_decode($enc_text);
	$n = strlen($enc_text);
	$i = $iv_len;
	$plain_text = '';
	$iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
   	
	while ($i < $n){
		$block = substr($enc_text, $i, 16);
		$plain_text .= $block ^ pack('H*', md5($iv));
		$iv = substr($block . $iv, 0, 512) ^ $password;
		$i += 16;
	}
	
	return preg_replace('/\\x13\\x00*$/', '', $plain_text);
} 
// END ENCRYPTING AND DECRYPTING AREA

function gen_thumb($imgSrc,$width = 0, $height = 0) {
	$img_info = getimagesize($imgSrc);
	
	// Get new dimensions
	list($width_orig, $height_orig) = getimagesize($imgSrc);
	$ratio_orig = $width_orig/$height_orig;

	if($width == 0 || $width > $width_orig){ $width = $width_orig; }
	if($height == 0 || $height > $height_orig ){  $height = $height_orig; }
	
	if ($width/$height > $ratio_orig) { $width = $height*$ratio_orig; }
	else { $height = $width/$ratio_orig; }

	// Resample
	$image_p = imagecreatetruecolor($width, $height);
	switch ($img_info[2]) {
		case IMAGETYPE_GIF  : $image = imagecreatefromgif($imgSrc);  break;
		case IMAGETYPE_JPEG : $image = imagecreatefromjpeg($imgSrc); break;
		case IMAGETYPE_PNG  : $image = imagecreatefrompng($imgSrc);  break;
		default : IMAGETYPE_JPEG : $image = imagecreatefromjpeg($imgSrc); break;
	}
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	// Output
	switch ($img_info[2]) {
		case IMAGETYPE_GIF  : 
			$red = imagecolorallocate($image_p, 255, 0, 0);
			$black = imagecolorallocate($image_p, 0, 0, 0);
			imagecolortransparent($image_p, $black);
			
			header('Content-Type: image/gif');
			imagegif($image_p, null, 100);break;
		case IMAGETYPE_JPEG : header('Content-Type: image/jpeg');imagejpeg($image_p, null, 100);break;
		case IMAGETYPE_PNG  : 
			$red = imagecolorallocate($image_p, 255, 0, 0);
			$black = imagecolorallocate($image_p, 0, 0, 0);
			imagecolortransparent($image_p, $black);
			
			header('Content-Type: image/png');
			imagepng($image_p, null, 9, PNG_ALL_FILTERS);
			break;
		default : header('Content-Type: image/jpeg');imagejpeg($image_p, null, 100);break;
	}
	imagedestroy($image_p);
	exit;
}

function relativedate($seconds,$displaytime = true) {
	$secs = strtotime('now') - $seconds;
		
	$minute = 60;
	$hour = 60*60;
	$day = 60*60*24;
	$week = 60*60*24*7;
     
	if($secs < $minute){ $time_string = "Just now"; }
	elseif($secs < $hour) {
		$ts = floor($secs / $minute);
		if($ts == 1) { $time_string = $ts . " minute ago"; }
		else { $time_string = $ts . " minutes ago"; }
	}
	elseif($secs < $day){
		$ts = floor($secs / $hour);
		if($ts == 1){ $time_string = $ts . " hour ago"; }
		else { $time_string = $ts . " hours ago"; }
	}
	elseif($secs < $week) {
		$ts = floor($secs / $day);
		if($ts == 1) { $time_string = "Yesterday at " . date("h:i a",strtotime('now') - $secs); }
		else { $time_string = $ts . " days ago"; }
	} else {
		if($displaytime){ $time_string = date("M d, Y h:i a",$seconds); }
		else{ $time_string = date("M d, Y",$seconds); }
	}
	return $time_string;
}

function relativedate_notif($date) {
	if($date == date("Y-m-d")){ return "Today"; }
	elseif($date == date("Y-m-d",strtotime('-1 day'))){ return "Yesterday"; }
	else { return date("M d, Y",strtotime($date)); }
}

function validate_alphaspace($text){
	if(preg_match("/^([-a-z ])+$/i", $text)){ return true; }
	else { return false; }
}

function validate_alphanumericspace($text){
	if(preg_match("/^[a-z\d\-_\s]+$/i", $text)){ return true; }
	else { return false; }
}

function download_file($path,$name){
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	//@apache_setenv('no-gzip', 1);
	@ini_set('zlib.output_compression', 'Off');

	// sanitize the file request, keep just the name and extension (do not change this!)
	$file_path  = ci()->input->get_post('file'); 
	$path_parts = pathinfo($file_path);
	
	if(isset($path_parts['extension'])) { $file_ext = $path_parts['extension']; }
	else { $file_ext = ""; }
	
	// this is where the file located in the server you can change it
	$file_path  = $path;
	
	// allow a file to be streamed instead of sent as an attachment
	$is_attachment = isset($_REQUEST['stream']) ? false : true;
	
	// make sure the file exists
	if (is_file($file_path)){
		$file_size  = filesize($file_path);
		$file = @fopen($file_path,"rb");
		
		if ($file){
			// change this to your desired filename
			$file_name = $name;
			
			// set the headers, prevent caching
			header("Pragma: public");
			header("Expires: -1");
			header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
			header("Content-Disposition: attachment; filename=\"$file_name\"");

			// set appropriate headers for attachment or streamed file
			if ($is_attachment) {
				header("Content-Disposition: attachment; filename=\"$file_name\"");
			} else {
				header('Content-Disposition: inline;');
			}

			// set the mime type based on extension, add yours if needed.
			$ctype_default = "application/octet-stream";
			$content_types = array(
				"exe" => "application/octet-stream",
				"zip" => "application/zip",
				"mp3" => "audio/mpeg",
				"mpg" => "video/mpeg",
				"avi" => "video/x-msvideo",
			);
			$ctype = isset($content_types[$file_ext]) ? $content_types[$file_ext] : $ctype_default;
			header("Content-Type: " . $ctype);

			//check if http_range is sent by browser (or download manager)
			if(isset($_SERVER['HTTP_RANGE'])){
				list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				if ($size_unit == 'bytes'){
					//multiple ranges could be specified at the same time, but for simplicity only serve the first range
					//http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
					list($range, $extra_ranges) = explode(',', $range_orig, 2);
				} else {
					$range = '';
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					exit;
				}
			} else{
				$range = '';
			}

			//figure out download piece from range (if set)
			list($seek_start, $seek_end) = explode('-', $range, 2);

			//set start and end based on range (if set), else set defaults
			//also check for invalid ranges.
			$seek_end   = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)),($file_size - 1));
			$seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);

			//Only send partial content header if downloading a piece of the file (IE workaround)
			if ($seek_start > 0 || $seek_end < ($file_size - 1)) {
				header('HTTP/1.1 206 Partial Content');
				header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$file_size);
				header('Content-Length: '.($seek_end - $seek_start + 1));
			} else {
				header("Content-Length: $file_size");
			}
			
			header('Accept-Ranges: bytes');

			set_time_limit(0);
			fseek($file, $seek_start);

			while(!feof($file)) {
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
				if (connection_status()!=0) {
					@fclose($file);
					exit;
				}			
			}
		
			// file save was a success
			@fclose($file);
			exit;
		} else {
			// file couldn't be opened
			header("HTTP/1.0 500 Internal Server Error");
			exit;
		}
	}
}