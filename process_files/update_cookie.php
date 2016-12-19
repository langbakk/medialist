<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
require_once($processpath.'functions.php');
if (isset($_POST['setsort'])) {
	$posted = $_POST['setsort'];
	setcookie('setsort',$_POST['setsort'],time() + 31536000, '/');
	$cookie = isset($_COOKIE['setsort']) ? $_COOKIE['setsort'] : '';
	if ($debug == true) {
		logThis('cookiesetsort','setsort is set to: '.$posted.' and cookie is set to: '.getcookie('setsort').''."\r\n",FILE_APPEND);
	}
}
if(isset($_POST['setsort']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	$returnmessage = json_encode(["content"=>"Sorting updated","infotype"=>"success"]);
} elseif(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	$returnmessage = json_encode(["content"=>"Cookie updated","infotype"=>"success"]);
} else {
	if (isset($_SERVER['HTTP_REFERER'])) {
		header('Refresh: 3; url='.$_SERVER['HTTP_REFERER']);	
	} else {
		header('Refresh: 3; url=gallery');
	}
}
echo $returnmessage;
?>