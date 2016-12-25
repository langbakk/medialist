<?php
if (!session_id()) { session_start(); };
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
$returnmessage = [];
$symbolic = (isset($_POST['filename']) ? $_POST['filename'] : '');
if ($_SESSION['username'] == explode('/',$username)[0]) {
	if (file_exists($document_root.'/'.$userpath.$username.$symbolic)) {
		$checkthumbs = explode('/',$symbolic);
		if ($checkthumbs[0] == 'pictures') {
			if (stripos($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1],'private__') !== false) {
				rename($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1],$document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.(explode('__',$checkthumbs[1])[1]));
			} else {
				rename($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1],$document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/private__'.$checkthumbs[1]);
			}
		} elseif ($checkthumbs[0] == 'video') {
			rename($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1].'.jpg',$document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/private__'.$checkthumbs[1].'jpg');
		}
		if (stripos($document_root.'/'.$userpath.$username.$checkthumbs[0].'/'.$checkthumbs[1],'private__') !== false) {
			rename($document_root.'/'.$userpath.$username.$symbolic,$document_root.'/'.$userpath.$username.$checkthumbs[0].'/'.(explode('__',$checkthumbs[1])[1]));
			$returnmessage = ["content"=>"File is no longer private","infotype"=>"success"];
		} else {
			rename($document_root.'/'.$userpath.$username.$symbolic,$document_root.'/'.$userpath.$username.$checkthumbs[0].'/private__'.$checkthumbs[1]);	
		}
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && (empty($returnmessage))) {
			$returnmessage = ["content"=>"File is set private","infotype"=>"success"];
		} elseif (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$returnmessage = $returnmessage;
		} else {
			header('location: userprofile');
		}
	} else {
		$returnmessage = ["content"=>"Filename doesn't exist","infotype"=>"error"];
	}
}
echo json_encode($returnmessage);
?>