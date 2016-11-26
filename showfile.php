<?php
require_once('conf/config.php');
require_once('functions.php');
if (!session_id()) { session_start(); };
$username = (($isloggedin && isset($_GET['user'])) ? $_GET['user'].'/' : ((!$isloggedin) ? 'public/' : $username));
$filename = explode('&',explode('=',$_SERVER['QUERY_STRING'])[1])[0];

if (!$isloggedin && (isset($_GET['imgfile']) || isset($_GET['docfile']) || isset($_GET['vidfile']))) {
	$querystring = explode('__',explode('=',$_SERVER['QUERY_STRING'])[1]);
	$username = $querystring[0].'/';
	$filename = explode('&',$querystring[1])[0];
} elseif (!empty($_SERVER['QUERY_STRING']) && $username != 'public/') {
	$potential_public_file = explode('__',explode('=',$_SERVER['QUERY_STRING'])[1])[0];
	for ($i = 0; $i < count($user_array); $i++) {
		$exploded_user_array = explode('//',$user_array[$i]);
		if (($potential_public_file == trim($exploded_user_array[0]))) {
			$username = 'public/';
		}
	}
}
	logThis('showfile_processing','Username is set to '.$username.''."\r\n",FILE_APPEND);
	if (isset($_GET['imgfile'])) {
		logThis('showfile_processing','Image-file requested '.$username.$filename."\r\n",FILE_APPEND);
		if (isset($_GET['thumbs']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/thumbs/'.$filename)) {
			logThis('showfile_processing','Thumb-file requested '.$username.'/'.$filename."\r\n",FILE_APPEND);
			header('Content-type: image/jpeg');
			header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/thumbs/'.$filename);
		} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'pictures/'.$filename)) {
			logThis('showfile_processing','image requested '.$username.$filename."\r\n",FILE_APPEND);
			header('Content-type: image/jpeg');
			header('X-Sendfile: '.$userpath.$username.'pictures/'.$filename.'');
		} else {
			header('http/1.0 404 not found');
			logThis('404','Showfile.php - imgfile - Return error 404 '.$username.$filename."\r\n",FILE_APPEND);
			http_response_code(404);
			header('Location: index.php?page=404');
		}
    } elseif (isset($_GET['docfile'])) {
    	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'documents/'.$_GET['docfile'])) {
	    	header('Content-Disposition: attachment; filename='.$_GET['docfile'].'');
	    	header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'documents/'.$_GET['docfile'].'');
	    } else {
	    	header('http/1.0 404 not found');
	    	logThis('404','Showfile.php - docfile - Return error 404 '.$username.$filename."\r\n",FILE_APPEND);
	    	http_response_code(404);
			header('Location: index.php?page=404');	    	
	    }
    } elseif (isset($_GET['vidfile'])) {
    	if (isset($_GET['thumbs'])) {
    		logThis('showfile_processing','Thumbs loaded '.$_GET['vidfile']."\r\n",FILE_APPEND);	
    		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/thumbs/'.$_GET['vidfile'])) {
	    		header('Content-type: image/jpeg');
				header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/thumbs/'.$_GET['vidfile']);
			} else {
				header('http/1.0 404 not found');
				logThis('404','Showfile.php - vidfile - Return error 404 '.$username.$filename."\r\n",FILE_APPEND);
				http_response_code(404);
				header('Location: index.php?page=404');				
			}
		} else {
				logThis('showfile_processing','Video returned '.$_GET['vidfile']."\r\n",FILE_APPEND);
			$getExtension = getExtension($_GET['vidfile']);
				logThis('showfile_processing','Extension of file is '.$getExtension."\r\n",FILE_APPEND);
			if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/'.$_GET['vidfile'])) {				
				if (in_array($getExtension,allowedMimeAndExtensions('extension'))) {
					$getMimeType = allowedMimeAndExtensions($getExtension,'mime');
						logThis('showfile_processing','Mimetype is '.$getMimeType[0]."\r\n",FILE_APPEND);
				}
				header('Content-type: '.$getMimeType[0].'');	
				if (!isset($_GET['loadFile']) || $_GET['loadFile'] != true) {
					header('Content-Disposition: attachment; filename='.$_GET['vidfile'].'');	
				}		
				header('X-Sendfile: '.$_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.'video/'.$_GET['vidfile'].'');
			} else {
				header('http/1.0 404 not found');
				logThis('404','Showfile.php - No file exists - Return error 404 '.$username.$filename."\r\n",FILE_APPEND);
				http_response_code(404);
				header('Location: index.php?page=404');				
			}
		}
    }
    exit;
?>