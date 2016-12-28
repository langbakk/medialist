<?php
if (!session_id()) { session_start(); };
$returnmessage = json_encode([]);
$deletefile = '';
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
	if (isset($_POST['filename']) && ((isset($_POST['deletepublic'])) == true)) {
		$tmpfn = explode('/',$_POST['filename']);
		$tmpfn[1] = explode('/',$username)[0].'__'.$tmpfn[1]; 
		$delete_sharedfile = join('/',$tmpfn);
		$deletefile = join('/',$tmpfn);
	} elseif (isset($_POST['filename'])) {
		$tmpfn = explode('/',$_POST['filename']);
		$tmpfn[1] = $tmpfn[1]; 
		$delete_sharedfile = join('/',$tmpfn);
		$deletefile = $_POST['filename'];
	} 
	$username = ((isset($_POST['username']) && !empty($_POST['username'])) ? $_POST['username'].'/' : (((isset($_POST['deletepublic'])) == true) ? 'public/' : $username));
	if ((file_exists($document_root.'/'.$userpath.$username.$deletefile)) || (file_exists($document_root.'/'.$userpath.$username.$tmpfn[0].'/thumbs/'.$tmpfn[1])) || (is_link($document_root.'/'.$userpath.$username.$deletefile))) {
		if (!empty($deletefile)) {
			$checkthumbs = explode('/',$deletefile);
			$checkthumbs[1] = ($checkthumbs[0] == 'video') ? $checkthumbs[1].'.jpg' : $checkthumbs[1];
			if ($checkthumbs[0] == 'pictures' || $checkthumbs[0] == 'video') {
				if (file_exists($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1])) {
					unlink($document_root.'/'.$userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]);
				}
			}
			if (file_exists($document_root.'/'.$userpath.$username.$deletefile)) {
				unlink($document_root.'/'.$userpath.$username.$deletefile);
			}
			if (is_link($document_root.'/'.$userpath.'public/'.$deletefile)) {
				unlink($document_root.'/'.$userpath.'public/'.$deletefile);
				unlink($document_root.'/'.$userpath.'public/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]);
			} elseif (is_link($document_root.'/'.$userpath.'public/'.$delete_sharedfile)) {
				unlink($document_root.'/'.$userpath.'public/'.$delete_sharedfile);
				unlink($document_root.'/'.$userpath.'public/'.$checkthumbs[0].'/thumbs/'.(($checkthumbs[0] == 'video') ? $tmpfn[1].'.jpg' : $tmpfn[1]));
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
	} else {
		$returnmessage = json_encode(["content"=>"The file was not deleted","infotype"=>"error"]);
	}
	echo $returnmessage;
?>