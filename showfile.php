<?php
require_once('conf/config.php');
if (!session_id()) { session_start(); };
if ($isloggedin) {
	if (isset($_GET['file']) && isset($_GET['thumbs'])) {
		header('Content-type: image/jpeg');
		header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/thumbs/'.$_GET['file']);
		exit;
	} elseif (isset($_GET['file'])) {
		// $getFile = readfile($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/'.$_GET['file']);
		header('Content-type: image/jpeg');
		header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/'.$_GET['file'].''); # make sure $file is the full path, not relative
		exit;
		// echo base64_encode($getFile);
    }
}
?>