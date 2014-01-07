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
		function allowedExtensions($type, $filename = 'allowed_extensions.php') {
			$filetype = file($filename, FILE_IGNORE_NEW_LINES);
			$allowedfiletypes = array();
			foreach ($filetype as $key => $value) {
				if (stripos($value, "//") === false && stripos($value, '?>') === false && stripos($value, '<?php') === false && !empty($value)) {
					$value = str_replace('\'','',$value);
					$get_type = explode('/',$value);
					if (!empty($type) && $type == $get_type[0]) {
						$allowedfiletypes[] = $get_type[1];
					} elseif (empty($type)) {
						$allowedfiletypes[] = $get_type[1];
					} 
				}
			}
			return $allowedfiletypes;
		}
		function allowedMimeTypes($type, $filename = 'allowed_mimetypes.php') {
			$mimetype = file($filename, FILE_IGNORE_NEW_LINES);
			$allowedmimetypes = array();
			foreach ($mimetype as $key => $value) {
				if (stripos($value, "//") === false && stripos($value, '?>') === false && stripos($value, '<?php') === false) {
					$allowedmimetypes[] = str_replace('\'','',$value);
				}
			}
			if (!empty($type)) {
				$allowedmimetypes_replace = array();
				foreach ($allowedmimetypes as $key => $value) {
					$value = explode('/',$value);
					if ($type == $value[0]) {
						$allowedmimetypes_replace[] = $value[0].'/'.$value[1];
					}
				}
				$allowedmimetypes = $allowedmimetypes_replace;
			}
			return $allowedmimetypes;
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
			    if (in_array(strtolower($info['extension']),allowedExtensions('image')))  {
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
  		function displayMenu($baseurl, $baseurl_page, $usedb = false) {
  			if ($usedb == false) {
  				$menuArray = array(1 => 'index.php', 2 => 'gallery.php', 3 => 'upload.php', 4 => 'userprofile.php');
  				$main_menu = '<nav id="mainmenu">
  								<ul>';
  					foreach ($menuArray as $key => $value) {
  						$menutext = pathinfo($value);
  						$useurl = ($menutext['basename'] == 'index.php') ? $baseurl.$menutext['basename'] : $baseurl_page.$menutext['filename'];
  						$main_menu .= '<li><a href="'.$useurl.'">'.ucfirst((($menutext['filename'] == 'index') ? 'home' : $menutext['filename'])).'</a></li>';
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
			natsort($lines);
			if ($count != 1) {
				$returncontent = '<div><h3>You\'ve uploaded the following files:</h3><ul>';
			foreach ($lines as $line) {
				$returncontent .= '<li>'.str_replace('\'','',$line).'</li>';
			}
				$returncontent .= '</ul></div>';
				return $returncontent;
			}
  		}
  		function loadFiles($filetype, $path, $recursive = 0, $testfileallow = 0) {
		    $results = array();     // create an array to hold directory list
		    $handler = opendir($path);     // create a handler for the directory

		    while ($file = readdir($handler)) {
		    	$ext = pathinfo($file, PATHINFO_EXTENSION);     // open directory and walk through the filenames
		            $testfile = substr($file,0,4);
		            if ($file != "." && $file != ".." && $ext == $filetype && $testfileallow == 1) {
		                if ($testfile == 'new_') {
		                    $results[] = $file;     // if file is a test-file (prefixed with new_) add it to the results
		                }
		                $results[] = $file; // add the rest of the files, unless it's a directory
		            } elseif ($file != "." && $file != ".." && $ext == $filetype && ($testfile != 'new_' && $testfileallow == 0)) {
		                    $results[] = $file;     // add all results apart from test-files (prefixed with new_) to the results, unless it's a directory
		            }
		    }
		    closedir($handler);     // tidy up: close the handler
		    natsort($results);
		        $recursive = ($recursive == 1 || $recursive = '') ? '../' : '';
		    	if ($filetype == 'js') {
		    		foreach ($results as $key => $value) {
		    			echo '<script src="'.$recursive.$path.$value.'" type="text/javascript"></script>', PHP_EOL;
		    		}
		    	} elseif ($filetype == 'css') {
		            foreach ($results as $key => $value) {
		                echo '<link type="text/css" href="'.$recursive.$path.$value.'" rel="stylesheet" media="screen, projection">', PHP_EOL;
		            }
		        }
		}



?>