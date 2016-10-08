<?php
if (!session_id()) { session_start(); };
$returnmessage = [];
require_once('conf/config.php');
	$symbolic = (isset($_POST['filename']) ? $_POST['filename'] : '');
	if (file_exists($userpath.$username.$symbolic)) {
		if (!empty($symbolic)) {
			// $username = ((isset($_POST['username']) && !empty($_POST['username'])) ? $_POST['username'].'/' : $username);

			$checkthumbs = explode('/',$symbolic);
			$checkthumbs[1] = ($checkthumbs[0] == 'video') ? $checkthumbs[1].'.jpg' : $checkthumbs[1];
			$new_symbolic = ($checkthumbs[0] == 'video') ? $checkthumbs[0].'/'.explode('/',$username)[0].'_'.$checkthumbs[1].'.jpg' : $checkthumbs[0].'/'.explode('/',$username)[0].'_'.$checkthumbs[1];
			if (!is_link($userpath.'public/'.$checkthumbs[0].'/thumbs/'.$username.'_'.$checkthumbs[1])) {
				$thumbs = ($checkthumbs[0] == 'pictures' || $checkthumbs[0] == 'video') ? symlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1],$userpath.'public/'.$checkthumbs[0].'/thumbs/'.explode('/',$username)[0].'_'.$checkthumbs[1]) : false;
			}
			if (!is_link($userpath.'public/'.$new_symbolic)) {
				symlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$symbolic,$userpath.'public/'.$new_symbolic);
			} else {
				$returnmessage = json_encode(["content"=>"File already exist in public folder","infotype"=>"warning"]);
			}
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && empty($returnmessage)) {
				$returnmessage = json_encode(["content"=>"File made public","infotype"=>"success"]);
			} elseif (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$returnmessage = $returnmessage;
			} else {
				header('location: gallery');
			}
		}
	} else {
		$returnmessage = json_encode(["content"=>"Filename doesn't exist","infotype"=>"error"]);
	}
	echo $returnmessage;
?>