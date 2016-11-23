<?php

	$chosenlanguage = [	'mainheading' 			=> 'Uploadr',
						'uploadlabel'			=> 'Choose file',
						'uploadsubmit'			=> 'Upload',
						'uploadreset'			=> 'Reset form'
					];

foreach($chosenlanguage as $key => $value) {
	define('__'.strtoupper($key).'',$value);
}

?>