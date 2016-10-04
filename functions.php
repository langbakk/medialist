<?php

ini_set('display_errors',1); // this should be commented out in production environments
error_reporting(E_ALL); // this should be commented out in production environments


		function removeExtension($strName) {
			$ext = explode('.',$strName);
			$ext = array_reverse($ext);
			$ext = $ext[0];
			return $strName = substr($strName, 0, -strlen($ext) -1);
		}
		function getExtension($strName) {
			$info = pathinfo($strName);
			return $info['extension'];
		}
		function allowedExtensions($type, $filename = 'conf/.allowed_extensions') {
			$filetype = file($filename, FILE_IGNORE_NEW_LINES);
			$allowedfiletypes = [];
			foreach ($filetype as $key => $value) {
				if (stripos($value, "//") === false && !empty($value)) {
					// $value = str_replace('\'','',$value);
					// $get_type = explode('/',$value);
					// if (!empty($type) && $type == $get_type[0]) {
						// $allowedfiletypes[] = $get_type[1];
					// } elseif (empty($type)) {
						// $allowedfiletypes[] = $get_type[1];
					// } 
					$allowedfiletypes[] = $value;
				}
			}
			return $allowedfiletypes;
		}
		function allowedMimeTypes($type, $filename = 'conf/.allowed_mimetypes') {
			$mimetype = file($filename, FILE_IGNORE_NEW_LINES);
			$allowedmimetypes = [];
			foreach ($mimetype as $key => $value) {
				if (stripos($value, "//") === false) {
					$allowedmimetypes[] = str_replace('\'','',$value);
				}
			}
			if (!empty($type)) {
				$allowedmimetypes_replace = [];
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
  		function generate_image_thumbnail($source_image_path, $thumbnail_image_path, $thumb_width = 200, $thumb_height = 200) {
    		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    		
    		$w = $source_image_width;
    		$h = $source_image_height;
		    
		    $source_gd_image = false;
		    switch ($source_image_type) {
		        case IMAGETYPE_GIF:  		 
		            $source_gd_image = imagecreatefromgif($source_image_path);
		            break;
		        case IMAGETYPE_JPEG:
		            $source_gd_image = imagecreatefromjpeg($source_image_path);
		            break;
		        case IMAGETYPE_PNG:
		            $source_gd_image = imagecreatefrompng($source_image_path);
		            break;
		    }
    		
    		if ($source_gd_image === false) {
        		return false;
    		}
		    
		    $source_aspect_ratio = $source_image_width / $source_image_height;
		    $thumbnail_aspect_ratio = $thumb_width / $thumb_height;
		    

			if ($thumb_width/$thumb_height > $source_aspect_ratio) {
			   $thumb_width = $thumb_height*$source_aspect_ratio;
			} else {
			   $thumb_height = $thumb_width/$source_aspect_ratio;
			}

		    $thumbnail_gd_image = imagecreatetruecolor($thumb_width, $thumb_height);
		    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $w, $h);
		    imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 100);
		    imagedestroy($source_gd_image);
		    imagedestroy($thumbnail_gd_image);
		    return true;
		} // end generate_image_thumbnail≤÷z<abs(number)'÷≤ xc1''1'1'11't

  		function displayMenu($baseurl, $usedb = false) {
  			if ($usedb == false) {
  				$menuArray = ['index','gallery','upload','login','userprofile'];
  				$main_menu = '<nav id="mainmenu">
  								<ul>';
  				$allow_public = Config::read('allow_public');
  				$isloggedin = Config::read('isloggedin');
  					foreach ($menuArray as $key => $value) {
  						$menutext = pathinfo($value);
  							$useurl = $menutext['basename'];
  						if ($allow_public == true && !$isloggedin && $key != 4) {
  						// $useurl = ($menutext['basename'] == 'index') ? $baseurl.$menutext['basename'] : $baseurl_page.$menutext['filename'];

  						$main_menu .= '<li><a href="'.$useurl.'">'.ucfirst((($menutext['filename'] == 'index') ? 'home' : $menutext['filename'])).'</a></li>';
  						} elseif ($isloggedin && $key != 3) {
	  						// $useurl = ($menutext['basename'] == 'index') ? $baseurl.$menutext['basename'] : $baseurl_page.$menutext['filename'];
	  						$main_menu .= '<li><a href="'.$useurl.'">'.ucfirst((($menutext['filename'] == 'index') ? 'home' : $menutext['filename'])).'</a></li>';  							
  						}
  					}
  				$main_menu .= '</ul>
  							</nav>';
  			}
  			return $main_menu;
  		}
  		function updateCurrentUploads($filename, $adduploadfile) {
  			$handle = file_put_contents($filename, "'".$adduploadfile."'".PHP_EOL, FILE_APPEND);
  		}
  		function isEnabled($func) {
 		   return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
		}

		function onlyValidChar($filename) {
		    $filename = htmlentities($filename, ENT_QUOTES, 'UTF-8');
		    $filename = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $filename);
		    $filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');
		    $filename = preg_replace(array('~[^0-9a-z.]~i', '~[ -]+~'), ' ', $filename);
		    $filename = str_replace(' ','_',$filename);
		    return trim($filename, ' -');
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
		    $results = [];     // create an array to hold directory list
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

		function foldersize($path) {
			$total_size = 0;
			$files = scandir($path);
			$cleanPath = rtrim($path, '/'). '/';

			foreach($files as $t) {
				if ($t <> '.' && $t <> '..') {
					$currentFile = $cleanPath . $t;
					if (is_dir($currentFile)) {
						$total_size += foldersize($currentFile);
						// $total_size += $size;
					} else {
						$total_size += filesize($currentFile);
						// $total_size += $size;
					}
				}  	 
			}
			return $total_size;
		}

		function format_size($size) {
			$units = Config::read('filesize_units');			
			$mod = 1024;
			for ($i = 0; $size > $mod; $i++) {
				$size /= $mod;
			}
			$endIndex = strpos($size, '.')+3;
			return substr( $size, 0, $endIndex).' '.$units[$i];
		}





?>