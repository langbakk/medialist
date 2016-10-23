<?php
require_once('conf/config.php');
if (!session_id()) { session_start(); };
if ($isloggedin) {
	if (isset($_GET['imagefile'])) {
		header('Content-type: image/jpeg');
		if (isset($_GET['thumbs'])) {
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/thumbs/'.$_GET['imagefile']);
		} else {
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/'.$_GET['imagefile'].'');
		}
    } elseif (isset($_GET['docfile'])) {
    	header('Content-Disposition: attachment; filename='.$_GET['docfile'].'');
    	header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'documents/'.$_GET['docfile'].'');
    } elseif (isset($_GET['vidfile'])) {
    	if (isset($_GET['thumbs'])) {
    		header('Content-type: image/jpeg');
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/thumbs/'.$_GET['vidfile']);
		} else {
			header('Content-Disposition: attachment; filename='.$_GET['vidfile'].'');
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/'.$_GET['vidfile'].'');
		}
    }
    exit;
}
?>