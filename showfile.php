<?php
require_once('conf/config.php');
if (!session_id()) { session_start(); };

$username = (($isloggedin && isset($_GET['user'])) ? $_GET['user'].'/' : ((!$isloggedin) ? 'public/' : $username));
if (!empty($_SERVER['QUERY_STRING']) && $username != 'public/') {
	$potential_public_file = explode('__',explode('=',$_SERVER['QUERY_STRING'])[1])[0];
	for ($i = 0; $i < count($user_array); $i++) {
		$exploded_user_array = explode('//',$user_array[$i]);
		if (($potential_public_file == trim($exploded_user_array[0]))) {
			$username = 'public/';
		}
	}
}
	if ($debug == true) {
		logThis('showfile_processing','Username is set to '.$username.''."\r\n",FILE_APPEND);
	}
	if (isset($_GET['imgfile'])) {
		header('Content-type: image/jpeg');
		if (isset($_GET['thumbs'])) {
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/thumbs/'.$_GET['imgfile']);
		} else {
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/'.$_GET['imgfile'].'');
		}
    } elseif (isset($_GET['docfile'])) {
    	header('Content-Disposition: attachment; filename='.$_GET['docfile'].'');
    	header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'documents/'.$_GET['docfile'].'');
    } elseif (isset($_GET['vidfile'])) {
    	if (isset($_GET['thumbs'])) {
    		if ($debug == true) {
    			logThis('showfile_processing','Thumbs loaded '.$_GET['vidfile']."\r\n",FILE_APPEND);	
    		}
    		header('Content-type: image/jpeg');
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/thumbs/'.$_GET['vidfile']);
		} else {
			if ($debug == true) {
				logThis('showfile_processing','Video returned '.$_GET['vidfile']."\r\n",FILE_APPEND);
			}
			header('Content-Disposition: attachment; filename='.$_GET['vidfile'].'');
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/'.$_GET['vidfile'].'');
		}
    }
    exit;
?>