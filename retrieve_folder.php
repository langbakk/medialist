<?php
session_start();
require_once('conf/config.php');
require_once('functions.php');
require_once('language.php');

$currentcontent = (isset($_POST['currentfile'])) ? $_POST['currentfile'] : '';

if (!empty($currentcontent)) {
	$getinfo = pathinfo($currentcontent);
	$ext = strtolower($getinfo['extension']);
	if (in_array($ext,allowedExtensions(''))) {
		if (in_array($ext,allowedExtensions('audio'))) {
			$folder = $userpath.$username.'/music/';
			$getcontent = opendir($folder);
			$foldervalue = array();
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		} elseif (in_array($ext,allowedExtensions('video'))) {
			$folder = $userpath.$username.'/video/';
			$getcontent = opendir($folder);
			$foldervalue = array();
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		} elseif (in_array($ext,allowedExtensions('image'))) {
			$folder = $userpath.$username.'/images/';
			$getcontent = opendir($folder);
			$foldervalue = array();
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		} elseif (in_array($ext,allowedExtensions('document'))) {
			$folder = $userpath.$username.'/documents/';
			$getcontent = opendir($folder);
			$foldervalue = array();
			while (false !== ($file = readdir($getcontent))) {
				if (is_dir($folder.$file) && $file != '.' && $file != '..') {
					$foldervalue[] = $file;
				}
			}
		}
	}
	echo json_encode($foldervalue);
} else {
	echo 'no folder found';
}

?>