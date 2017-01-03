<?php
if (!session_id()) { session_start(); };
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/config.php');
require_once($processpath.'functions.php');
$returnmessage = json_encode(["content"=>"Error Error Error","infotype"=>"error"]);
$changereturnheader = 0;
$original_username = $username;
$moderation_queue = false;
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
			
			$directories = [1 => '/pictures', 2 => '/pictures/thumbs', 3 => '/video', 4 => '/video/thumbs', 5 => '/audio', 6 => '/documents', 7 => '/applications'];
			if (!is_dir($document_root.'/'.$userpath.$username)) {
				mkdir($document_root.'/'.$userpath.$username, 0744, true);
			}
			if (is_dir($document_root.'/'.$userpath.$username)) {
				$foldercreated = false;
				foreach ($directories as $key => $dir) {
					if (!is_dir($document_root.'/'.$userpath.$username.$dir)) {
						mkdir($document_root.'/'.$userpath.$username.$dir, 0744, true);
						file_put_contents($document_root.'/'.$userpath.$username.$dir.'/.gitignore','# Ignore everything in this directory'."\r\n".'*'."\r\n".'# Except this file'."\r\n".'!.gitignore');
						$foldercreated = true;
					}
				}
				$folderexist = true;
			} 
			if (Config::read('moderation_queue') == true) {
				$username = 'moderation/';
				if (!is_dir($document_root.'/'.$userpath.$username)) {
					mkdir($document_root.'/'.$userpath.$username, 0744, true);
				}
				if (is_dir($document_root.'/'.$userpath.$username)) {
					$foldercreated = false;
					foreach ($directories as $key => $dir) {
						if (!is_dir($document_root.'/'.$userpath.$username.$dir)) {
							mkdir($document_root.'/'.$userpath.$username.$dir, 0744, true);
							file_put_contents($document_root.'/'.$userpath.$username.$dir.'/.gitignore','# Ignore everything in this directory'."\r\n".'*'."\r\n".'# Except this file'."\r\n".'!.gitignore');
							$foldercreated = true;
						}
					}
					$folderexist = true;
				}
			}	

			if (isset($_FILES['file']) && $returnerror == false) {
				if (($_FILES['file']['size'] + foldersize($document_root.'/'.$userpath.$username) < $storage_limit)) {
					if (in_array($_FILES['file']['type'], allowedMimeAndExtensions('','mime'))) {
						if (in_array($_FILES['file']['type'], allowedMimeAndExtensions('audio','mime'))) {
							$folder = 'audio';
						} elseif (in_array($_FILES['file']['type'], allowedMimeAndExtensions('image','mime'))) {
							$folder = 'pictures';
						}
						 elseif (in_array($_FILES['file']['type'], allowedMimeAndExtensions('video','mime'))) {
							$folder = 'video';
						} elseif (in_array($_FILES['file']['type'], allowedMimeAndExtensions('documents','mime'))) { 
							$folder = 'documents';
						} elseif (in_array($_FILES['file']['type'], allowedMimeAndExtensions('application','mime'))) {
							$folder = 'applications';
						}
						$filename = $_FILES['file']['name'];
						if (file_exists(''.$document_root.'/'.$userpath.(((Config::read('moderation_queue') == true) && ($usertype != 'admin')) ? $username : $original_username).$folder.'/'.(((Config::read('moderation_queue') == true) && ($usertype != 'admin')) ? explode('/',$original_username)[0].'__' : '').onlyValidChar($_FILES['file']['name']))) {
							if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
								$returnmessage = json_encode(["content"=>"$filename already exist","infotype"=>"error"]);
							}						
						} else {
							if ((Config::read('moderation_queue') == true) && (Config::read('usertype') != 'admin')) {
								move_uploaded_file($_FILES['file']['tmp_name'],''.$document_root.'/'.$userpath.$username.$folder.'/'.explode('/',$original_username)[0].'__'.onlyValidChar($_FILES['file']['name']));
								$moderation_queue = true;
							} else {
								move_uploaded_file($_FILES['file']['tmp_name'],''.$document_root.'/'.$userpath.$original_username.$folder.'/'.onlyValidChar($_FILES['file']['name']));	
							}
							if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
								$returnmessage = json_encode(["content"=>"You uploaded $filename","infotype"=>"success"]);
							}
							$movedfile = pathinfo($_FILES['file']['name']);
							if (in_array(strtolower($movedfile['extension']),allowedMimeAndExtensions('extension')) && in_array($_FILES['file']['type'],allowedMimeAndExtensions('image','mime'))) {
								generate_image_thumbnail($document_root.'/'.$userpath.(((Config::read('moderation_queue') == true) && ($usertype != 'admin')) ? $username : $original_username).$folder.'/'.(((Config::read('moderation_queue') == true) && ($usertype != 'admin')) ? explode('/',$original_username)[0].'__' : '').onlyValidChar($_FILES['file']['name']),$document_root.'/'.$userpath.(((Config::read('moderation_queue') == true) && ($usertype != 'admin')) ? $username : $original_username).$folder.'/thumbs/'.(((Config::read('moderation_queue') == true) && ($usertype != 'admin')) ? explode('/',$original_username)[0].'__' : '').onlyValidChar($_FILES['file']['name']));
							}
							if (in_array(strtolower($movedfile['extension']),allowedMimeAndExtensions('extension')) && in_array($_FILES['file']['type'],allowedMimeAndExtensions('video','mime'))) {
								if ((Config::read('moderation_queue') == true) && (Config::read('usertype') != 'admin')) {
									$video = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.explode('/',$original_username)[0].'__'.onlyValidChar($_FILES['file']['name']);
									$still = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/thumbs/'.onlyValidChar($_FILES['file']['name'].'.jpg');									
									$thumbnail = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/thumbs/'.explode('/',$original_username)[0].'__'.onlyValidChar($_FILES['file']['name']).'.gif';
									$palette = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/thumbs/palette.png';
								} else {
									$video = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$original_username.$folder.'/'.onlyValidChar($_FILES['file']['name']);
									$still = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$original_username.$folder.'/thumbs/'.onlyValidChar($_FILES['file']['name'].'.jpg');
									$thumbnail = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$original_username.$folder.'/thumbs/'.onlyValidChar($_FILES['file']['name']).'.gif';
									$palette = $_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$original_username.$folder.'/thumbs/palette.png';
								}
								logThis('video_upload',$video."\r\n".$thumbnail,FILE_APPEND);
	    						// $get_frames = shell_exec("/usr/local/bin/ffmpeg -nostats -i $video -vcodec copy -f rawvideo -y /dev/null 2>&1 | grep frame | awk '{split($0,a,\"fps\")}END{print a[1]}' | sed 's/.*= *//'");
	    						// $stills_number = floor($get_frames / 200);
	    						// $output = shell_exec("/usr/local/bin/ffmpeg -y -i $video -frames 1 -q:v 1 -vf 'select=not(mod(n\,$stills_number)),scale=-1:120,tile=100x1' $thumbnail");
	    						
	    						// $output = shell_exec("/usr/local/bin/ffmpeg -i $video -vf scale=320:-1 -r 10 -f image2pipe -vcodec ppm - | /usr/local/Cellar/imagemagick/6.9.2-10/bin/convert -delay 5 -loop 0 - gif:- | /usr/local/Cellar/imagemagick/6.9.2-10/bin/convert -layers Optimize - $thumbnail");
	    						shell_exec("/usr/local/bin/ffmpeg -y -ss 30 -t 3 -i $video -vf 'fps=10,scale=320:-1:flags=lanczos,palettegen' $palette");
	    						$output1 = shell_exec("/usr/local/bin/ffmpeg -ss 00:00:05 -i $video -vf 'scale=220:-1:flags=lanczos' -vframes 1 -q:v 2 $still");
	    						$output = shell_exec("/usr/local/bin/ffmpeg -ss 30 -t 30 -i $video -i $palette -filter_complex 'fps=10,scale=220:-1:flags=lanczos[x];[x][1:v]paletteuse' $thumbnail");
	    						unlink($palette);
	    						// /usr/local/bin/ffmpeg -ss 30 -t 3 -i /Applications/MAMP/htdocs/uploadr/users/admin/video/Live_On_The_Bate_Anal_Dildo_Ramming_Pornhubcom.mp4 -i /Applications/MAMP/htdocs/uploadr/users/admin/video/thumbs/palette.png -filter_complex 'fps=10,scale=320:-1:flags=lanczos[x];[x][1:v]paletteuse' /Applications/MAMP/htdocs/uploadr/users/admin/video/thumbs/output.gif

							}
						}
					} else {
						if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {		
							$changereturnheader = 1;
							$returnmessage = json_encode(["content"=>"The filetype you tried to upload is not allowed","infotype"=>"error"]);
						}
					}
				} else {
					$returnmessage = json_encode(["content"=>"The file will exceed your available diskspace. Delete some of the files already uploaded to make room","infotype"=>"error"]);
				}
			} elseif ($returnerror == true) {
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$returnmessage = json_encode(["content"=>"$returnerrorcontent","infotype"=>"error"]);
				}
			} else {
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$changereturnheader = 1;
					$returnmessage = json_encode(["content"=>"The filetype you tried to upload is not allowed","infotype"=>"error"]);
				}
			}
		}
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			if ($changereturnheader == 1) {
				http_response_code(415);
			}
			echo $returnmessage;
		} else {
			header('location: upload');
		}
	}
?>