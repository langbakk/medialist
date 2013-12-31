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
			$ext = explode('.',$strName);
			$ext = array_reverse($ext);
			$ext = str_replace('.','',$ext[0]);

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
  			//$dir = opendir( $pathToImages ); // open the directory
			//while (false !== ($imageName = readdir( $dir ))) {   // loop through it, looking for any/all JPG files:
    		$info = pathinfo($path . $imageName); // parse path for the extension
			    if (in_array(strtolower($info['extension']),allowedExtensions('allowed_extensions.php')))  { // continue only if this is a JPEG image
//			    	echo "Creating thumbnail for {$imageName} <br>";
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

	      			// calculate thumbnail size
	      			$new_width = $thumbWidth;
	      			$new_height = floor( $height * ( $thumbWidth / $width ) );

	      			// create a new temporary image
	      			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

	      			// copy and resize old image into new image
	      			imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

	      			// save thumbnail into a file
	      				imagejpeg( $tmp_img, "{$path}thumbs/{$imageName}" );
	      			}
	    		}
  			}
?>