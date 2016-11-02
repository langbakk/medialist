<?php
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';
echo '<div class="container">';
if (isset($_POST['submit_login']) && $username != '' && $password != '') {
	if ($use_db === false) {
		$success = false;
		for ($i = 0; $i < count($user_array); $i++) {
			$exploded_user_array = explode('//',$user_array[$i]);
			if (($username == trim($exploded_user_array[0])) && (valueCrypt::vC_pwHash($password,trim($exploded_user_array[1])) === trim($exploded_user_array[1]))) {
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = trim($exploded_user_array[0]);
				$_SESSION['usertype'] = trim($exploded_user_array[2]);
				$_SESSION['storagelimit'] = (array_key_exists(3,$exploded_user_array)) ? trim($exploded_user_array[3]) : $storage_limit;
				$success = true;
				$folderexist = false;
				$foldercreated = false;
				if (!is_dir($userpath.$username)) {
					mkdir($userpath.$username, 0744, true);
					// file_put_contents($userpath.$username.'/index.html','<p>Placeholder</p>');					
				}
				$directories = [1 => '/pictures', 2 => '/pictures/thumbs', 3 => '/video', 4 => '/video/thumbs', 5 => '/music', 6 => '/documents'];
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
					echo '<p class="messagebox success">User-folders created!</p>';
					header('refresh: 2; url=upload');
				} elseif ($folderexist == true && $foldercreated == false) {
					echo '<p class="messagebox info">User-folders already exists</p>';
					header('refresh: 2; url=upload');
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
echo '<form id="loginform" method="post" action="login">
	<input type="text" id="username" name="username" value="'.$username.'" placeholder="Please input your username">
	<input type="password" id="password" name="password" value="'.$password.'" placeholder="Please input your password">
	<a href="register" class="center">No account? Register here</a>
	<input type="submit" name="submit_login" value="Login">
</form>';
}
echo '</div>';
?>