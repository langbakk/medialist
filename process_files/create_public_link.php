<?php
if (!session_id()) { session_start(); };
$returnmessage = [];
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
	$symbolic = (isset($_POST['filename']) ? $_POST['filename'] : '');

	if (file_exists($document_root.'/'.$userpath.$username.$symbolic)) {
		if (!empty($symbolic)) {
			$checkthumbs = explode('/',$symbolic);
			$new_symbolic = ($checkthumbs[0] == 'video') ? $checkthumbs[0].'/'.explode('/',$username)[0].'__'.$checkthumbs[1] : $checkthumbs[0].'/'.explode('/',$username)[0].'__'.$checkthumbs[1];
				$thumbs = 	(($checkthumbs[0] == 'pictures') ? 
					symlink($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1],$document_root.'/'.$userpath.'public/'.$checkthumbs[0].'/thumbs/'.explode('/',$username)[0].'__'.$checkthumbs[1]) : 
					(($checkthumbs[0] == 'video') ? 
					symlink($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1].'.jpg',$document_root.'/'.$userpath.'public/'.$checkthumbs[0].'/thumbs/'.explode('/',$username)[0].'__'.$checkthumbs[1].'.jpg') : 
					false));
			if (!is_link($userpath.'public/'.$new_symbolic)) {
				symlink($document_root.'/'.$userpath.$username.$symbolic,$document_root.'/'.$userpath.'public/'.$new_symbolic);
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