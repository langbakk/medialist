<?php
if (isset($_POST['submit_registration'])) {
	if (!empty($_POST['username']) && !empty($_POST['password'])) {
		$userlistfile = 'conf/.userlist';
		$registerpassword = valueCrypt::vC_pwHash($_POST['password']);
		$content = "\r\n".$_POST['username'].' // '.$registerpassword;
		file_put_contents($userlistfile,$content,FILE_APPEND | LOCK_EX);
	}
}
echo '<div class="container">
		<h2>Register user</h2>
		<form id="user_registration_form" method="post" action="register">
			<label for="username">Username</label>
			<input type="text" name="username" id="username">
			<label for="password">Password</label>
			<input type="password" name="password" id="password">
			<input type="submit" name="submit_registration" value="Register user">
		</form>
	</div>';
?>