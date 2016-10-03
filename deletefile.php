<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
// if ($isloggedin && $use_login == true) {
	$deletefile = (isset($_POST['filename']) ? $_POST['filename'] : '');
	if (!empty($deletefile)) {
		$username = (isset($_POST['username']) ? $_POST['username'].'/' : $username);
		$fullpath = $userpath.$username.'/'.$deletefile;

		$checkthumbs = explode('/',$deletefile);
		$checkthumbs[1] = ($checkthumbs[0] == 'video') ? $checkthumbs[1].'.jpg' : $checkthumbs[1];
		$thumbs = ($checkthumbs[0] == 'pictures' || $checkthumbs[0] == 'video') ? unlink($userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]) : false;
		unlink($userpath.$username.$deletefile);

		echo json_encode(["content"=>"File deleted","infotype"=>"success"]);
	}
// }
?>