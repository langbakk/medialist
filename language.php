<?php

	$chosenlanguage = [	'mainheading' 								=> 'Uploadr',
						'uploadlabel'								=> 'choose file',
						'uploadsubmit'								=> 'upload',
						'uploadreset'								=> 'reset form',
						'username'									=> 'username',
						'password'									=> 'password',
						'no_account'								=> 'no account? Register here',
						'login'										=> 'log in',
						'login_error_no_match'						=> 'user not found, or password not a match',
						'login_info_message'						=> 'you can upload files and have them show in the public gallery without logging in, but you will not be able to set uploads as private, nor make your own albums',
						'login_username_placeholder'				=> 'please input your username',
						'login_password_placeholder'				=> 'please input your password',
						'logout'									=> 'log out',
						'registeruser'								=> 'register user',
						'register_username_placeholder'				=> 'only letters and numbers',
						'register_password_placeholder'				=> 'at least 8 characters length',
						'userfolders_created'						=> 'user-folders created',
						'userfolders_exist'							=> 'user-folders already exist'
					];

foreach($chosenlanguage as $key => $value) {
	define('__'.strtoupper($key).'',$value);
}

?>