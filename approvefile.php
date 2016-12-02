<?php
if (!session_id()) { session_start(); };
$returnmessage = json_encode([]);
$approvefile = '';
require_once('conf/config.php');

if ($usertype == 'admin' && array_reverse(explode('/',$_SERVER['HTTP_REFERER']))[0] == 'moderate') {
	if (isset($_POST['filename'])) {
		$tmpfn = explode('/',$_POST['filename']);
		$tmpfolder = $tmpfn[0];
		$tmpuser = explode('__',$tmpfn[1])[0];
		$tmpfilename = explode('__',$tmpfn[1])[1];
	} 
	if (file_exists($userpath.'moderation/'.$_POST['filename'])) {
		rename($userpath.'moderation/'.$_POST['filename'],$userpath.$tmpuser.'/'.$tmpfolder.'/'.$tmpfilename);
		if ($tmpfolder == 'pictures') {
			rename($userpath.'moderation/'.$tmpfolder.'/thumbs/'.$tmpuser.'__'.$tmpfilename,$userpath.$tmpuser.'/'.$tmpfolder.'/thumbs/'.$tmpfilename);
		}
		$returnmessage = json_encode(["content"=>"File approved, moved to $tmpuser - folder","infotype"=>"success"]);
// 		if (!empty($approvefile)) {
// 			$checkthumbs = explode('/',$approvefile);
// 			$checkthumbs[1] = ($checkthumbs[0] == 'video') ? $checkthumbs[1].'.jpg' : $checkthumbs[1];
// 			$thumbs = ($checkthumbs[0] == 'pictures' || $checkthumbs[0] == 'video') ? unlink($userpath.$username.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]) : false;
// 			unlink($userpath.$username.$approvefile);
// 			if (is_link($userpath.'moderation/'.$approvefile)) {
// 				unlink($userpath.'moderation/'.$approvefile);
// 				unlink($userpath.'moderation/'.$checkthumbs[0].'/thumbs/'.$checkthumbs[1]);
// 			} elseif (is_link($userpath.'moderation/'.$approve_sharedfile)) {
// 				unlink($userpath.'moderation/'.$approve_sharedfile);
// 				unlink($userpath.'moderation/'.$checkthumbs[0].'/thumbs/'.(($checkthumbs[0] == 'video') ? $tmpfn[1].'.jpg' : $tmpfn[1]));
// 			}
// 			if((isset($_POST['deletepublic']) == true) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
// 				$returnmessage = json_encode(["content"=>"File removed from public","infotype"=>"success"]);
// 			} elseif(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
// 				$returnmessage = json_encode(["content"=>"File deleted","infotype"=>"success"]);
// 			} else {
// 				header('location: gallery');
// 			}
// 		}
// 	} else if (is_link($userpath.$username.$approvefile)) {
// 		$returnmessage = json_encode(["content"=>"it's a link","infotype"=>"info"]);
	}
}
	echo $returnmessage;

?>