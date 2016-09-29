<?php
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

if (isset($_POST['submit_login']) && $username != '' && $password != '') {
	if ($use_db === false) {
		$success = false;
		for ($i = 0; $i < count($user_array); $i++) {
			if (in_array($username,$user_array[$i]) && in_array($password,$user_array[$i])) {
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = $user_array[$i]['username'];
				$success = true;
				$folderexist = false;
				$foldercreated = false;
				if (!is_dir($userpath.$username)) {
					mkdir($userpath.$username, 0744, true);
					file_put_contents($userpath.$username.'/index.html','<p>Placeholder</p>');					
				}
				$directories = array(1 => '/pictures', 2 => '/pictures/thumbs', 3 => '/video', 4 => '/video/thumbs', 5 => '/music', 6 => '/documents');
				if (is_dir($userpath.$username)) {
					foreach ($directories as $key => $dir) {
						if (!is_dir($userpath.$username.$dir)) {
							mkdir($userpath.$username.$dir, 0744, true);
							file_put_contents($userpath.$username.$dir.'/index.html','');
						}
					}
					$folderexist = true;
					$foldercreated = true;
					echo '<p class="messagebox success">User-folders created!</p>';
				} else {
					$folderexist = true;
					$foldercreated = false;
					echo '<p class="messagebox info">User-folder exists</p>';
				}
				if ($folderexist == true && $foldercreated == true) {
					header('refresh: 3');
				} elseif ($folderexist == true && $foldercreated == false) {
					header('refresh: 3');
				} else {
					header('refresh: 0');
				}
			} 
		}
		if ($success == false) {
			echo '<p class="messagebox error">User not found, or password not a match</p>';
		}
	}
} elseif (isset($_POST['submit_logout'])) {
		session_destroy();
		session_unset();
		header('refresh: 0');
}

if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false) || !isset($_SESSION['loggedin'])) {
		if ($allow_public == true && !$isloggedin) {
		echo '<p class="messagebox info visible">You can upload files and have them show in the public gallery without logging in, but you will not be able to set uploads as private, nor make your own albums</p>';
	}
echo '<form id="loginform" method="post" action="'.$baseurl_page.'login">
	<input type="text" id="username" name="username" value="'.$username.'" placeholder="Please input your username">
	<input type="password" id="password" name="password" value="'.$password.'" placeholder="Please input your password">
	<input type="submit" name="submit_login" value="Login">
</form>';
} else {
echo '<form id="loginform" method="post" action="'.$baseurl_page.'login">
	<input type="submit" name="submit_logout" value="Log out">
</form>';
}
?>