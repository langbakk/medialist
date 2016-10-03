<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
require_once('functions.php');
if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) || $allow_public == true) {
		if (isset($_FILES['file'])) {
			if (in_array('error', $_FILES['file'])) {
				switch ($_FILES['file']['error']) {
					case 0:
					$returnerror = false;
					$returnerrorcontent = '';
					break;
					case 1:
					$returnerror = true;
					$returnerrorcontent = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
					case 2:
					$returnerror = true;        
					$returnerrorcontent = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
					case 3:
					$returnerror = true;        
					$returnerrorcontent = 'The uploaded file was only partially uploaded';
					break;
					case 4:
					$returnerror = true;        
					$returnerrorcontent = 'No file was selected';
					break;
					case 6:
					$returnerror = true;        
					$returnerrorcontent = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3';
					break;
					case 7:
					$returnerror = true;      
					$returnerrorcontent = 'Failed to write file to disk. Introduced in PHP 5.1.0';
					break;
					case 8:
					$returnerror = true;        
					$returnerrorcontent = 'A PHP extension stopped the file upload. Introduced in PHP 5.2.0';
					break;
					default:
					$returnerror = false;
					$returnerrorcontent = '';
					break;
				}
			}
			if (isset($_FILES['file']) && $returnerror == false) {
				$allowed = '';
				$allowed_extensions = allowedExtensions('');
				$totalentries = count(allowedExtensions('')) -1;
				for ($i = 0; $i <= $totalentries; $i++) {
					$allowed .= (($i == $totalentries) ? $allowed_extensions[$i] : $allowed_extensions[$i].', ');
				}
				if (in_array($_FILES['file']['type'], allowedMimeTypes(''))) {
					if (in_array($_FILES['file']['type'], allowedMimeTypes('audio'))) {
						$folder = 'music';
					} elseif (in_array($_FILES['file']['type'], allowedMimeTypes('image'))) {
						$folder = 'pictures';
					} elseif (in_array($_FILES['file']['type'], allowedMimeTypes('video'))) {
						$folder = 'video';
					} elseif (in_array($_FILES['file']['type'], allowedMimeTypes('application'))) {
						$folder = 'documents';
					} elseif (in_array($_FILES['file']['type'], allowedMimeTypes('text'))) {
						$folder = 'documents';
					}
					$filename = $_FILES['file']['name'];
					if (file_exists(''.$userpath.$username.$folder.'/'.onlyValidChar($_FILES['file']['name']))) {
						echo json_encode(["content"=>"$filename already exist","infotype"=>"error"]);
					} else {
						move_uploaded_file($_FILES['file']['tmp_name'],''.$userpath.$username.$folder.'/'.onlyValidChar($_FILES['file']['name']));
						echo json_encode(["content"=>"You uploaded $filename","infotype"=>"success"]);
						$movedfile = pathinfo($_FILES['file']['name']);
						if (in_array(strtolower($movedfile['extension']),allowedExtensions('')) && in_array($_FILES['file']['type'],allowedMimeTypes('image'))) {
							// createThumbs($userpath.$username.$folder.'/',onlyValidChar($_FILES['file']['name']),200);
							generate_image_thumbnail($userpath.$username.$folder.'/'.onlyValidChar($_FILES['file']['name']),$userpath.$username.$folder.'/thumbs/'.onlyValidChar($_FILES['file']['name']));
						}
						if (in_array(strtolower($movedfile['extension']),allowedExtensions('')) && in_array($_FILES['file']['type'],allowedMimeTypes('video'))) {
							$video = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.onlyValidChar($_FILES['file']['name']);
							$thumbnail = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/thumbs/'.onlyValidChar($_FILES['file']['name']).'.jpg';
    						$get_frames = shell_exec("/usr/local/bin/ffmpeg -nostats -i $video -vcodec copy -f rawvideo -y /dev/null 2>&1 | grep frame | awk '{split($0,a,\"fps\")}END{print a[1]}' | sed 's/.*= *//'");
    						$stills_number = floor($get_frames / 200);
    						$output = shell_exec("/usr/local/bin/ffmpeg -y -i $video -frames 1 -q:v 1 -vf 'select=not(mod(n\,$stills_number)),scale=-1:120,tile=100x1' $thumbnail");
						}
						updateCurrentUploads('current_uploads.php',$_FILES['file']['name']);
						// header('refresh: 3');
					}
				} else {
					echo json_encode(["content"=>"The filetype you tried to upload is not allowed","infotype"=>"error"]);
				}
			} elseif ($returnerror == true) {
				echo json_encode(["content"=>"$returnerrorcontent","infotype"=>"error"]);
			} else {
				echo json_encode(["content"=>"The filetype you tried to upload is not allowed","infotype"=>"error"]);
			}
		}
	// echo returnCurrentUploads('current_uploads.php');
	}
?>