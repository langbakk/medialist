<?php

ini_set('display_errors',1); // this should be commented out in production environments
error_reporting(E_ALL); // this should be commented out in production environments


		function removeExtension($strName) {
			$ext = explode('.',$strName);
			$ext = array_reverse($ext);
			$ext = $ext[0];
					$strName = substr($strName, 0, -strlen($ext) -1);

			return $strName;
		}
		function getExtension($strName) {
			$info = pathinfo($strName);
			$ext = $info['extension'];
			return $ext;
		}
		function allowedExtensions($filename) {
			$filetype = file($filename, FILE_IGNORE_NEW_LINES);
			$allowedfiletypes = array();
			foreach ($filetype as $key => $value) {
				if (stripos($value, "//") === false && stripos($value, '?>') === false && stripos($value, '<?php') === false) {
					$allowedfiletypes[] = str_replace('\'','',$value);
				}
			}
			return $allowedfiletypes;
		}
		function in_array_recursive($needle, $haystack, $strict = false) {
    		foreach ($haystack as $item) {
        		if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_recursive($needle, $item, $strict))) {
            		return true;
        		}
    		}
		    return false;
		}
		function createThumbs($path, $imageName, $thumbWidth) {
    		$info = pathinfo($path . $imageName); // parse path for the extension
			    if (in_array(strtolower($info['extension']),allowedExtensions('allowed_extensions.php')))  {
			    	if ($info['extension'] == 'jpg' || $info['extension'] == 'jpeg') {
			    		$img = imagecreatefromjpeg( "{$path}{$imageName}" );
			    	} elseif ($info['extension'] == 'png') {
			    		$img = imagecreatefrompng( "{$path}{$imageName}" );
			    	} elseif ($info['extension'] == 'gif') {
			    		$img = imagecreatefromgif( "{$path}{$imageName}" );
			    	}
			    	if (!file_exists($info['dirname'].'/thumbs/'.$info['basename'])) {
	      			$width = imagesx( $img );
	      			$height = imagesy( $img );

	      			$new_width = $thumbWidth;
	      			$new_height = floor( $height * ( $thumbWidth / $width ) );

	      			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

	      			imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

	   				imagejpeg( $tmp_img, "{$path}thumbs/{$imageName}" );
	      			}
	    		}
  			}
  		function displayMenu($baseurl, $usedb = false) {
  			if ($usedb == false) {
  				$menuArray = array(1 => 'index.php', 2 => 'upload.php', 3 => 'userprofile.php');
  				$main_menu = '<nav id="mainmenu">
  								<ul>';
  					foreach ($menuArray as $key => $value) {
  						$menutext = pathinfo($value);
  						$main_menu .= '<li><a href="'.$baseurl.$menutext['basename'].'">'.ucfirst((($menutext['filename'] == 'index') ? 'home' : $menutext['filename'])).'</a></li>';
  					}
  				$main_menu .= '</ul>
  							</nav>';
  			}
  			return $main_menu;
  		}
  		function updateCurrentUploads($filename, $adduploadfile) {
  			$handle = file_put_contents($filename, "'".$adduploadfile."'".PHP_EOL, FILE_APPEND);
  		}
  		function returnCurrentUploads($filename) {
  			$handle = file_get_contents($filename);
			$lines = explode(PHP_EOL,$handle);
			$count = count($lines);
			if ($count != 1) {
				$returncontent = '<div><h3>You\'ve uploaded the following files:</h3><ul>';
			foreach ($lines as $line) {
				$returncontent .= '<li>'.str_replace('\'','',$line).'</li>';
			}
				$returncontent .= '</ul></div>';
				return $returncontent;
			}
  		}
?>