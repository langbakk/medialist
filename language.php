<?php

	$chosenlanguage = array (
							'mainheading' 			=> 'Medialist',
							'uploadlabel'			=> 'Choose file',
							'uploadsubmit'			=> 'Upload'
							);

foreach($chosenlanguage as $key => $value) {
	define('__'.strtoupper($key).'',$value);
}

?>