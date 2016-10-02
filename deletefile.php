<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
// if ($isloggedin && $use_login == true) {
	$deletefile = (isset($_POST['filename']) ? $_POST['filename'] : '');
	if (!empty($deletefile)) {
		$fullpath = $userpath.$username.'/'.$deletefile;

		$checkthumbs = explode('/',$deletefile);
		$thumbs = ($checkthumbs[0] == 'pictures') ? unlink($userpath.$username.'/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]) : false;
		unlink($userpath.$username.'/'.$deletefile);

		echo json_encode(["content"=>"File deleted","infotype"=>"success"]);
	}
// }
?>