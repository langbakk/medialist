<?php
if (!session_id()) { session_start(); };
$returnmessage = json_encode([]);
$approvefile = '';
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');

if ($usertype == 'admin' && array_reverse(explode('/',$_SERVER['HTTP_REFERER']))[0] == 'moderate') {
	if (isset($_POST['filename'])) {
		$tmpfn = explode('/',$_POST['filename']);
		$tmpfolder = $tmpfn[0];
		$tmpuser = explode('__',$tmpfn[1])[0];
		$tmpfilename = explode('__',$tmpfn[1])[1];
	} 
	if (file_exists($document_root.'/'.$userpath.'moderation/'.$_POST['filename'])) {
		rename($document_root.'/'.$userpath.'moderation/'.$_POST['filename'],$document_root.'/'.$userpath.$tmpuser.'/'.$tmpfolder.'/'.$tmpfilename);
		if ($tmpfolder == 'pictures' || $tmpfolder == 'video') {
			if ($tmpfolder == 'video') {
				$tmpfilename = $tmpfilename.'.jpg';
			}
			rename($document_root.'/'.$userpath.'moderation/'.$tmpfolder.'/thumbs/'.$tmpuser.'__'.$tmpfilename,$document_root.'/'.$userpath.$tmpuser.'/'.$tmpfolder.'/thumbs/'.$tmpfilename);
		}
		$returnmessage = json_encode(["content"=>"File approved, moved to $tmpuser - folder","infotype"=>"success"]);
	}
}
	echo $returnmessage;

?>