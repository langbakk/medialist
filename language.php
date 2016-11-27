<?php

	$chosenlanguage = [	'mainheading' 			=> 'Uploadr',
						'uploadlabel'			=> 'choose file',
						'uploadsubmit'			=> 'upload',
						'uploadreset'			=> 'reset form',
						'username'				=> 'username',
						'password'				=> 'password',
						'no_account'			=> 'no account? Register here',
						'login'					=> 'log in',
						'logout'				=> 'log out',
					];

foreach($chosenlanguage as $key => $value) {
	define('__'.strtoupper($key).'',$value);
}

?>