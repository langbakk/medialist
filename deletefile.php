<?php
session_start();
require_once('conf/config.php');
// if ($isloggedin && $use_login == true) {
	$deletefile = $_POST['filename'];
	$fullpath = $userpath.$username.'/'.$deletefile;

	$checkthumbs = explode('/',$deletefile);
	$thumbs = ($checkthumbs[0] == 'pictures') ? unlink($userpath.$username.'/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]) : false;
	unlink($userpath.$username.'/'.$deletefile);

	echo 'success';
// }
?>