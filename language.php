<?php

	$chosenlanguage = array (
							'mainheading' 			=> 'Medialist'
							);

foreach($chosenlanguage as $key => $value) {
	define('__'.strtoupper($key).'',$value);
}

?>