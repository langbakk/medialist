<?php
if (isset($_POST['submit_registration'])) {
	if (!empty($_POST['username']) && !empty($_POST['password'])) {
		$userlistfile = 'conf/.userlist';
		$getusers = file($userlistfile);
		$register = true;
		foreach ($getusers as $key => $value) {
			$registered_username = strtolower(trim(explode('//',$value)[0]));
			if (strtolower(trim($_POST['username'])) == $registered_username) {
				$register = false;
			}
		}
		$registerpassword = valueCrypt::vC_pwHash($_POST['password']);
		$content = "\r\n".$_POST['username'].' // '.$registerpassword.' // user';
		if ($register == true) {
			file_put_contents($userlistfile,$content,FILE_APPEND | LOCK_EX);
			echo '<p class="messagebox success visible">User registered</p>';
		} else {
			echo '<p class="messagebox error visible">Username already registered</p>';
		}
	}
}
echo '<div class="container">
		<h2>'.mb_ucfirst(__REGISTERUSER).'</h2>
		<div class="content">
		<form id="user_registration_form" method="post" action="register">
			<label for="username">'.mb_ucfirst(__USERNAME).'</label>
			<input type="text" name="username" id="username" placeholder="'.mb_ucfirst(__REGISTER_USERNAME_PLACEHOLDER).'">
			<label for="password">'.mb_ucfirst(__PASSWORD).'</label>
			<input type="password" name="password" id="password" placeholder="'.mb_ucfirst(__REGISTER_PASSWORD_PLACEHOLDER).'">
			<input type="submit" name="submit_registration" value="'.mb_ucfirst(__REGISTERUSER).'">
		</form>
		</div>
	</div>';
?>