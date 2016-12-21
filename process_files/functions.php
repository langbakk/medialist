<?php
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

function generateRandomString($alpha = true, $nums = true, $usetime = false, $string = '', $length = 120) {
    $alpha = ($alpha == true) ? 'abcdefghijklmnopqrstuvwxyz' : '';
    $nums = ($nums == true) ? '1234567890' : '';

    if ($alpha == true || $nums == true || !empty($string)) {
        if ($alpha == true) {
            $alpha = $alpha;
            $alpha .= strtoupper($alpha);
        }
    }
    $randomstring = '';
    $totallength = $length;
        for ($na = 0; $na < $totallength; $na++) {
                $var = (bool)rand(0,1);
                if ($var == 1) {
                    $randomstring .= $alpha[(rand() % mb_strlen($alpha))];
                } else {
                    $randomstring .= $nums[(rand() % mb_strlen($nums))];
                }
        }
    if ($usetime == true) {
        $randomstring = $randomstring.time();
    }
    return($randomstring);
} // end generateRandomString

function mb_ucfirst($string, $encoding='UTF-8') {
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, mb_strlen($string, $encoding)-1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}

function allowedMimeAndExtensions($type, $mimetype = '', $filename = '.allowed_mimetypes') {
	$list = file(Config::read('confpath').$filename, FILE_IGNORE_NEW_LINES);
	$allowed_types = ['image','video','audio','application','text','documents'];
	$allowed_files = [];
	$allowedfiles_replace = [];
	foreach ($list as $key => $value) {
		if (stripos($value, "//") === false && !empty($value)) {
			$allowed_files[] = 	trim(str_replace('\'','',$value));
		}
	}
	if ((!empty($type) && $type == 'extension') && (!empty($mimetype) && $mimetype == 'mime')) {
		foreach ($list as $key => $value) {
			if (stripos($value,"//") === false && !empty($value)) {
				$value = explode(' ',$value);
				$allowedfiles_replace[$value[0]] = $value[1];
			}
		}
		$allowed_files = $allowedfiles_replace;
	} elseif (!empty($type) && $type == 'extension') {
		foreach ($list as $key => $value) {
			if (stripos($value, "//") === false && !empty($value)) {
				$value = trim(explode(' ',$value)[0]);
				$allowedfiles_replace[] = $value;
			}
		}
		$allowed_files = $allowedfiles_replace;
	} elseif ((!empty($type) && $type == 'documents') && (!empty($mimetype) && $mimetype == 'mime')) {
		$document_array = ['doc','docx','pdf','xls','xlsx','ppt','txt'];
		foreach ($list as $key => $value) {
			if (stripos($value, "//") === false && !empty($value)) {
				$value = explode(' ',$value);
				if (in_array(trim($value[0]),$document_array)) {
					$allowedfiles_replace[] = str_replace('\'','',trim($value[1]));
				}
			}
		}	
		$allowed_files = $allowedfiles_replace;		
	} elseif ((!empty($type) && !in_array($type, $allowed_types)) && $mimetype == 'mime') {
		foreach ($list as $key => $value) {
			if (stripos($value, "//") === false && !empty($value)) {
				$value_ext = explode(' ',$value)[0];
				logThis('allowedmimetypes','This is the value '.$value_ext."\r\n",FILE_APPEND);
				if ($type == $value_ext) {
					$allowedfiles_replace[] = str_replace('\'','',explode(' ',$value)[1]);
				}
			}
		}
		$allowed_files = $allowedfiles_replace;
	} elseif (empty($type) && ((!empty($mimetype) && $mimetype == 'mime'))) {
		foreach ($list as $key => $value) {
			if (stripos($value, "//") === false && !empty($value)) {
				$value = str_replace('\'','',trim(explode(' ',$value)[1]));
				$allowedfiles_replace[] = $value;
			}
		}
		$allowed_files = $allowedfiles_replace;
	} elseif ((!empty($type) && in_array($type,$allowed_types)) && empty($mimetype)) {
		foreach ($list as $key => $value) {
			if (stripos($value, "//") === false && !empty($value)) {
				$value_mime = explode('/',explode(' ',str_replace('\'','',$value))[1]);
				if ($type == $value_mime[0]) {
					$value = trim(explode(' ',$value)[0]);
					$allowedfiles_replace[] = $value;
				}
			}
		}
		$allowed_files = $allowedfiles_replace;
	} elseif ((!empty($type) && in_array($type,$allowed_types)) && !empty($mimetype)) {
		foreach ($list as $key => $value) {
			if (stripos($value, "//") === false && !empty($value)) {
				$value_mime = explode('/',explode(' ',str_replace('\'','',$value))[1]);
				if ($type == $value_mime[0]) {
					$value = str_replace('\'','',trim(explode(' ',$value)[1]));
					$allowedfiles_replace[] = $value;
				}
			}
		}
		$allowed_files = $allowedfiles_replace;
	}
		$joined_array = join(' ,',$allowed_files);
		logThis('allowedmimetypes','This is the allowed array '.$joined_array."\r\n",FILE_APPEND);
	return $allowed_files;
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
    if (in_array(strtolower($info['extension']),allowedMimeAndExtensions('image')))  {
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

function generate_image_thumbnail($source_image_path, $thumbnail_image_path, $thumb_width = 150, $thumb_height = 150) {
	list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
	
	$w = $source_image_width;
	$h = $source_image_height;
 
	$thumb_width = ($w < $thumb_width) ? $w : $thumb_width;
	$thumb_height = ($h < $thumb_height) ? $h : $thumb_height;

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
        case IMAGETYPE_BMP:
        	$source_gd_image = imagecreatefrombmp($source_image_path);
        	break;
    }
    // $source_gd_image = imagecreatefromstring(file_get_contents($source_image_path));
	
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
} // end generate_image_thumbnail

function imagecreatefrombmp($filename) {
	if (!$f1 = fopen($filename,"rb")) {
		return false;
	}

	$file = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
	if ($file['file_type'] != 19778) {
		return false;
	}

	$bmp = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	$bmp['colors'] = pow(2,$bmp['bits_per_pixel']);
	if ($bmp['size_bitmap'] == 0) {
		$bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
	}
	$bmp['bytes_per_pixel'] = $bmp['bits_per_pixel']/8;
	$bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
	$bmp['decal'] = ($bmp['width']*$bmp['bytes_per_pixel']/4);
	$bmp['decal'] -= floor($bmp['width']*$bmp['bytes_per_pixel']/4);
	$bmp['decal'] = 4-(4*$bmp['decal']);
	if ($bmp['decal'] == 4) {
		$bmp['decal'] = 0;	
	} 
	
	$palette = [];
	if ($bmp['colors'] < 16777216) {
		$palette = unpack('V'.$bmp['colors'], fread($f1,$bmp['colors']*4));
	}

	$img = fread($f1,$bmp['size_bitmap']);
	$vide = chr(0);

	$res = imagecreatetruecolor($bmp['width'],$bmp['height']);
	$p = 0;
	$y = $bmp['height']-1;
	while ($y >= 0) {
    	$x=0;
    	while ($x < $bmp['width']) {
     		if ($bmp['bits_per_pixel'] == 24) {
     			$color = unpack('V',substr($img,$p,3).$vide);		
     		} elseif ($bmp['bits_per_pixel'] == 16) { 
        		$color = unpack('n',substr($img,$p,2));
        		$color[1] = $palette[$color[1]+1];
     		} elseif ($bmp['bits_per_pixel'] == 8) { 
        		$color = unpack('n',$vide.substr($img,$p,1));
        		$color[1] = $palette[$color[1]+1];
     		} elseif ($bmp['bits_per_pixel'] == 4) {
        		$color = unpack('n',$vide.substr($img,floor($p),1));
        		if (($p*2)%2 == 0) {
        			$color[1] = ($color[1] >> 4);
        		} else {
        			$color[1] = ($color[1] & 0x0F);
        		}
        		$color[1] = $palette[$color[1]+1];
     		} elseif ($bmp['bits_per_pixel'] == 1) {
        		$color = unpack("n",$vide.substr($img,floor($p),1));
        		if (($p*8)%8 == 0) {
        			$color[1] = $color[1]>>7;
        		} elseif (($p*8)%8 == 1) {
        			$color[1] = ($color[1] & 0x40)>>6;
        		} elseif (($p*8)%8 == 2) {
        			$color[1] = ($color[1] & 0x20)>>5;
        		} elseif (($p*8)%8 == 3) {
        			$color[1] = ($color[1] & 0x10)>>4;
        		} elseif (($p*8)%8 == 4) {
        			$color[1] = ($color[1] & 0x8)>>3;	
        		} elseif (($p*8)%8 == 5) {
        			$color[1] = ($color[1] & 0x4)>>2;
        		} elseif (($p*8)%8 == 6) {
        			$color[1] = ($color[1] & 0x2)>>1;
        		} elseif (($p*8)%8 == 7) {
        			$color[1] = ($color[1] & 0x1);
        		}
        		$color[1] = $palette[$color[1]+1];
     		} else {
     			return false;		
     		}
			imagesetpixel($res,$x,$y,$color[1]);
     		$x++;
     		$p += $bmp['bytes_per_pixel'];
    	}
    	$y--;
		$p+=$bmp['decal'];
	}
	fclose($f1);
	return $res;
}

function displayMenu($baseurl, $usedb = false) {
	if ($usedb == false) {
		$menuArray = Config::read('menu_array');
		$allow_public = Config::read('allow_public');
		$allow_userlist = Config::read('allow_userlist');
		$moderate = Config::read('moderation_queue');
		$isloggedin = Config::read('isloggedin');
		$isadmin = (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'admin') ? true : false;	
		$main_menu = '<label for="mainmenu_button" class="menulistbutton"></label><input type="checkbox" id="mainmenu_button">';
		$main_menu .= '<ul id="mainmenu" class="flexlist">';
		foreach ($menuArray as $key => $value) {
			$menutext = pathinfo($key);
			$useurl = $menutext['basename'];
			$page = (isset($_GET['page']) && !empty($_GET['page'])) ? $_GET['page'] : 'index';

			if (!$isloggedin) {
				if ($key != 'userlist' && $key != 'userprofile' && $key != 'moderate') {
					if ($allow_public == 0 && ($key != 'gallery' && $key != 'upload')) {
						$main_menu .= '<li'.(($page == strtolower($menutext['filename'])) ? ' class="active"' : '').'><a href="'.(($useurl == 'index') ? '/' : $useurl).'">'.$value.'<span class="activearrow">&nbsp;</span></a></li>';
					} elseif ($allow_public == 1) {
						$main_menu .= '<li'.(($page == strtolower($menutext['filename'])) ? ' class="active"' : '').'><a href="'.(($useurl == 'index') ? '/' : $useurl).'">'.$value.'<span class="activearrow">&nbsp;</span></a></li>';
					}
				} 
			} else {
				if ($key != 'login' && $key != 'register') {
					if ($allow_userlist == 0 && $key != 'userlist') {
						$main_menu .= '<li'.(($page == strtolower($menutext['filename'])) ? ' class="active"' : '').'><a href="'.(($useurl == 'index') ? '/' : $useurl).'">'.$value.'<span class="activearrow">&nbsp;</span></a></li>';
					} elseif ($allow_userlist == 1 || $isadmin) {
						$main_menu .= '<li'.(($page == strtolower($menutext['filename'])) ? ' class="active"' : '').'><a href="'.(($useurl == 'index') ? '/' : $useurl).'">'.$value.'<span class="activearrow">&nbsp;</span></a></li>';
					}				
				}
			}
		}
		$main_menu .= '</ul>';
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
    $filename = preg_replace(['~[^0-9a-z.]~i', '~[ -]+~'], ' ', $filename);
    $filename = str_replace(' ','_',$filename);
    return trim($filename, ' -_');
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

function logThis($filename, $logthis) {
    $date = new DateTime();
    $date = $date->format('Y m d H:i:s');
    if (Config::read('debug') == true) {
    	file_put_contents(LOG_FOLDER.$filename.'.txt',$date.' '.$logthis."\r\n",FILE_APPEND);
    }
}

function loadFiles($filetype, $path, $recursive = 0, $testfileallow = 0) {
	$results = [];     				// create an array to hold directory list
	$handler = opendir($path);		// create a handler for the directory

	while ($file = readdir($handler)) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);     // open directory and walk through the filenames
        $testfile = substr($file,0,4);
        if ($file != "." && $file != ".." && $ext == $filetype && $testfileallow == 1) {
            if ($testfile == 'new_') {
                $results[] = $file;  // if file is a test-file (prefixed with new_) add it to the results
            }
            $results[] = $file; // add the rest of the files, unless it's a directory
        } elseif ($file != "." && $file != ".." && $ext == $filetype && ($testfile != 'new_' && $testfileallow == 0)) {
                $results[] = $file; // add all results apart from test-files (prefixed with new_) to the results, unless it's a directory
        }
	}
	closedir($handler); // tidy up: close the handler
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
		if ($t <> '.' && $t <> '..' && $t <> '.DS_Store' && $t <> '.gitignore') {
			$currentFile = $cleanPath . $t;
			if (is_dir($currentFile)) {
				$total_size += foldersize($currentFile);
			} else {
				$total_size += filesize($currentFile);
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

function getcookie($name) {
    $cookies = [];
    $headers = headers_list();
    foreach($headers as $header) {
        if (strpos($header, 'Set-Cookie: ') === 0) {
            $value = str_replace('&', urlencode('&'), substr($header, 12));
            parse_str(current(explode(';', $value, 1)), $pair);
            $cookies = array_merge_recursive($cookies, $pair);
        }
    }
    return $cookies[$name];
}
?>