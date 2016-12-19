<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
require_once($processpath.'functions.php');

// var_dump($_POST);

if ($_SERVER['HTTP_REFERER'] == $baseurl.'admin') {
	if (!isset($_POST['deleteuser'])) {
		$newuserlist = '#username // password // userrole // allow userlist link // diskspace-setting // preferred startpage'."\r\n";
		$c = 0;
		foreach ($user_array as $uakey => $uavalue) {
			$c++;
			$user = explode('//',$uavalue);
			if (isset($_POST['submit_userchanges']) && (trim($_POST['username']) == trim($user[0]))) {
				// echo 'this happened';
				$updated_username = isset($_POST['username']) ? trim($_POST['username']) : trim($user[0]);
				$updated_password = isset($_POST['password']) ? $_POST['password'] : trim($user[1]);
				$updated_usertype = isset($_POST['usertype']) ? $_POST['usertype'] : trim($user[2]);
				$updated_userlistlink = isset($_POST['userlistlink']) ? 1 : 0;
				$updated_userdiskspace = (isset($_POST['userdiskspace']) && trim($_POST['userdiskspace']) != $defaultsize) ? trim($_POST['userdiskspace']) : $defaultsize;
				$updated_userstartpage = (isset($_POST['userstartpage'])) ? strtolower(trim($_POST['userstartpage'])) : trim($user[5]);
			$newuserlist .= $updated_username.' // '.$updated_password.' // '.$updated_usertype.' // '.$updated_userlistlink.' // '.$updated_userdiskspace.' // '.$updated_userstartpage."\r\n";
			} else  {
				$newuserlist .= $uavalue."\r\n";	
			}
		}
		if (isset($_POST['submit_userchanges']) && (trim($_POST['username']) != trim($user[0]))) {
			$updated_username = isset($_POST['username']) ? trim($_POST['username']) : '';
			$updated_password = isset($_POST['password']) ? valueCrypt::vC_pwHash($_POST['password']) : '';
			$updated_usertype = isset($_POST['usertype']) ? $_POST['usertype'] : '';
			$updated_userlistlink = isset($_POST['userlistlink']) ? 1 : 0;
			$updated_userdiskspace = (isset($_POST['userdiskspace']) && trim($_POST['userdiskspace']) != $defaultsize) ? trim($_POST['userdiskspace']) : $defaultsize;
			$updated_userstartpage = (isset($_POST['userstartpage'])) ? strtolower(trim($_POST['userstartpage'])) : '';
			$newuserlist .= $updated_username.' // '.$updated_password.' // '.$updated_usertype.' // '.$updated_userlistlink.' // '.$updated_userdiskspace.' // '.$updated_userstartpage;
		}
		unlink($confpath.'.userlist');
		file_put_contents($confpath.'.userlist', $newuserlist);
	} elseif (isset($_POST['deleteuser']) && isset($_POST['username'])) {
		$newuserlist = '#username // password // userrole // allow userlist link // diskspace-setting // preferred startpage'."\r\n";
		$c = 0;
		foreach ($user_array as $uakey => $uavalue) {
			$c++;
			$user = explode('//',$uavalue);
			if (trim($_POST['username']) != trim($user[0])) {
				$newuserlist .= $uavalue."\r\n";
			} 
		}
		unlink($confpath.'.userlist');
		file_put_contents($confpath.'.userlist', $newuserlist);
		$content = $_POST['username'].' was removed';
		$infotype = 'success';
	}	
}  
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	echo json_encode(["content"=>"$content","infotype"=>"$infotype"]);
} else {
	header('Location: '.$_SERVER['HTTP_REFERER'].'#user_management_container');
}
?>