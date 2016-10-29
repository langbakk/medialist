<?php
if (!session_id()) { session_start(); };
$returnmessage = json_encode([]);
$deletefile = '';
require_once('conf/config.php');
	if (isset($_POST['filename']) && ((isset($_POST['deletepublic'])) == true)) {
		$tmpfn = explode('/',$_POST['filename']);
		$tmpfn[1] = explode('/',$username)[0].'__'.$tmpfn[1]; 
		$deletefile = join('/',$tmpfn);
	} elseif (isset($_POST['filename'])) {
		$deletefile = $_POST['filename'];
	} 
	$username = ((isset($_POST['username']) && !empty($_POST['username'])) ? $_POST['username'].'/' : (((isset($_POST['deletepublic'])) == true) ? 'public/' : $username));
	if (file_exists($userpath.$username.$deletefile)) {
		if (!empty($deletefile)) {
			$checkthumbs = explode('/',$deletefile);
			$checkthumbs[1] = ($checkthumbs[0] == 'video') ? $checkthumbs[1].'.jpg' : $checkthumbs[1];
			$thumbs = ($checkthumbs[0] == 'pictures' || $checkthumbs[0] == 'video') ? unlink($userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]) : false;
			unlink($userpath.$username.$deletefile);
			if (is_link($userpath.'public/'.$deletefile)) {
				unlink($userpath.'public/'.$deletefile);
				unlink($userpath.'public/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]);
			}
			if((isset($_POST['deletepublic']) == true) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$returnmessage = json_encode(["content"=>"File removed from public","infotype"=>"success"]);
			} elseif(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$returnmessage = json_encode(["content"=>"File deleted","infotype"=>"success"]);
			} else {
				header('location: gallery');
			}
		}
	} else if (is_link($userpath.$username.$deletefile)) {
		$returnmessage = json_encode(["content"=>"it's a link","infotype"=>"info"]);
	}
	echo $returnmessage;
?>


<?php
// if (!session_id()) { session_start(); };
// $returnmessage = [];
// require_once('conf/config.php');
// 	$symbolic = (isset($_POST['filename']) ? $_POST['filename'] : '');
// 	if (file_exists($userpath.$username.$symbolic)) {
// 		if (!empty($symbolic)) {
// 			// $username = ((isset($_POST['username']) && !empty($_POST['username'])) ? $_POST['username'].'/' : $username);

// 			$checkthumbs = explode('/',$symbolic);
// 			$checkthumbs[1] = ($checkthumbs[0] == 'video') ? $checkthumbs[1].'.jpg' : $checkthumbs[1];
// 			if (!is_link($userpath.'public/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1])) {
// 				$thumbs = ($checkthumbs[0] == 'pictures' || $checkthumbs[0] == 'video') ? symlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1],$userpath.'public/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]) : false;
// 			}
// 			if (!is_link($userpath.'public/'.$symbolic)) {
// 				symlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$symbolic,$userpath.'public/'.$symbolic);
// 			} else {
// 				$returnmessage = json_encode(["content"=>"File already exist in public folder","infotype"=>"warning"]);
// 			}
// 			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && empty($returnmessage)) {
// 				$returnmessage = json_encode(["content"=>"File made public","infotype"=>"success"]);
// 			} elseif (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
// 				$returnmessage = $returnmessage;
// 			} else {
// 				header('location: gallery');
// 			}
// 		}
// 	} else {
// 		$returnmessage = json_encode(["content"=>"Filename doesn't exist","infotype"=>"error"]);
// 	}
// 	echo $returnmessage;
?>