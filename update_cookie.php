<?php
require_once('conf/config.php');
require_once('functions.php');
if (isset($_POST['setsort'])) {
	setcookie('setsort',$_POST['setsort']);
	if ($debug == true) {
		logThis('cookiesetsort','setsort is set to: '.$_POST['setsort'].' and cookie is set to: '.$_COOKIE['setsort'].''."\r\n",FILE_APPEND);
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