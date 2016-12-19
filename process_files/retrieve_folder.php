<?php
if (!session_id()) { session_start(); };
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
require_once($processpath.'functions.php');

$currentcontent = (isset($_POST['currentfile'])) ? $_POST['currentfile'] : '';

if (!empty($currentcontent)) {
	$getinfo = pathinfo($currentcontent);
	$ext = strtolower($getinfo['extension']);
	if (in_array($ext,allowedMimeAndExtensions('extension'))) {
		if (in_array($ext,allowedMimeAndExtensions('audio'))) {
			$folder = $userpath.$username.'/audio/';
			$getcontent = opendir($folder);
			$foldervalue = [];
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		} elseif (in_array($ext,allowedMimeAndExtensions('video'))) {
			$folder = $userpath.$username.'/video/';
			$getcontent = opendir($folder);
			$foldervalue = [];
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		} elseif (in_array($ext,allowedMimeAndExtensions('image'))) {
			var_dump(allowedMimeAndExtensions('image'));
			$folder = $userpath.$username.'pictures/';
			$getcontent = opendir($folder);
			$foldervalue = [];
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		} elseif (in_array($ext,allowedMimeAndExtensions('document'))) {
			$folder = $userpath.$username.'/documents/';
			$getcontent = opendir($folder);
			$foldervalue = [];
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		}
	}
	echo json_encode($foldervalue);
} 

?>