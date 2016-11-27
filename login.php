<?php
require_once('conf/config.php');
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';
echo '<div class="container">';
if (isset($_POST['submit_logout'])) {
		session_destroy();
		session_unset();
		header('refresh: 0');
} elseif (isset($_POST['submit_login']) && $username != '' && $password != '') {
	if ($use_db === false) {
		$success = false;
		for ($i = 0; $i < count($user_array); $i++) {
			$exploded_user_array = explode('//',$user_array[$i]);
			if (($username == trim($exploded_user_array[0])) && (valueCrypt::vC_pwHash($password,trim($exploded_user_array[1])) === trim($exploded_user_array[1]))) {
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = trim($exploded_user_array[0]);
				$_SESSION['usertype'] = trim($exploded_user_array[2]);
				$_SESSION['storagelimit'] = (array_key_exists(4,$exploded_user_array)) ? trim($exploded_user_array[4]) : $storage_limit;
				$_SESSION['allowpublic'] = (array_key_exists(3,$exploded_user_array)) ? trim($exploded_user_array[3]) : 0;
				$_SESSION['userstartpage'] = (array_key_exists(5,$exploded_user_array)) ? trim($exploded_user_array[5]) : 'upload';
				$success = true;
				$folderexist = false;
				$foldercreated = false;
				if (!is_dir($userpath.$username)) {
					mkdir($userpath.$username, 0744, true);				
				}
				$directories = [1 => '/pictures', 2 => '/pictures/thumbs', 3 => '/video', 4 => '/video/thumbs', 5 => '/audio', 6 => '/documents', 7 => '/applications'];
				if (is_dir($userpath.$username)) {
					$foldercreated = false;
					foreach ($directories as $key => $dir) {
						if (!is_dir($userpath.$username.$dir)) {
							mkdir($userpath.$username.$dir, 0744, true);
							file_put_contents($userpath.$username.$dir.'/.gitignore','# Ignore everything in this directory'."\r\n".'*'."\r\n".'# Except this file'."\r\n".'!.gitignore');
							$foldercreated = true;
						}
					}
					$folderexist = true;
				} 
				if ($folderexist == true && $foldercreated == true) {
					echo '<p class="messagebox success">'.mb_ucfirst(__USERFOLDERS_CREATED).'</p>';
					header('refresh: 2; url=upload');
				} elseif ($folderexist == true && $foldercreated == false) {
					echo '<p class="messagebox info">'.mb_ucfirst(__USERFOLDERS_EXIST).'</p>';
					$redirectpage = $_SESSION['userstartpage'];
					header('refresh: 0; url='.$redirectpage);
				} else {
					header('refresh: 0');
				}
			} 
		}
		if ($success == false) {
			echo '<p class="messagebox error">'.mb_ucfirst(__LOGIN_ERROR_NO_MATCH).'</p>';
		}
	}
} 

if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false) || !isset($_SESSION['loggedin'])) {
		if ($allow_public == true && !$isloggedin) {
		echo '<p class="messagebox info visible remove_box">'.mb_ucfirst(__LOGIN_INFO_MESSAGE).'</p>';
	}
echo '<h2>'.str_replace(' ','',mb_ucfirst(__LOGIN)).'</h2>
		<div class="content">
		<form id="loginform" method="post" action="login">
			<label for="username">'.mb_ucfirst(__USERNAME).'</label>
			<input type="text" id="username" name="username" value="'.$username.'" placeholder="'.mb_ucfirst(__LOGIN_USERNAME_PLACEHOLDER).'">
			<label for="password">'.mb_ucfirst(__PASSWORD).'</label>
			<input type="password" id="password" name="password" value="'.$password.'" placeholder="'.mb_ucfirst(__LOGIN_PASSWORD_PLACEHOLDER).'">
			<a href="register" class="center">'.mb_ucfirst(__NO_ACCOUNT).'</a>
			<input type="submit" name="submit_login" value="'.mb_ucfirst(__LOGIN).'">
		</form>
		</div>';
}
echo '</div>';
?>