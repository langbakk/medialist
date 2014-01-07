<?php
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

if (isset($_POST['submit_login']) && $username != '' && $password != '') {
	if ($use_db === false) {
		$countusers = count($user_array);
		$success = false;
		for ($i = 1; $i <= $countusers; $i++) {
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
				$directories = array(1 => '/images', 2 => '/images/thumbs', 3 => '/video', 4 => '/music', 5 => '/documents');
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
?>
<form id="loginform" method="post" action="index.php">
	<input type="text" id="username" name="username" value="<?php echo $username; ?>" placeholder="Please input your username">
	<input type="password" id="password" name="password" value="<?php echo $password; ?>" placeholder="Please input your password">
	<input type="submit" name="submit_login" value="Login">
</form>
<?php } else { ?>
<form id="loginform" method="post" action="index.php">
	<input type="submit" name="submit_logout" value="Log out">
</form>
<?php } ?>