<?php

$original_username = $username;
$username = 'moderation/';

if ($isadmin) {
	echo '<div class="container">
	<p id="moderationinfo" class="messagebox visible info remove_box">Here you can view files uploaded that is still in the moderation-queue. Approve the ones that should be allowed, delete the others, or just leave them here</p>';
	if (!is_dir($userpath.$username)) {
		mkdir($userpath.$username, 0744, true);
	}
	$directories = [1 => '/pictures', 2 => '/pictures/thumbs', 3 => '/video', 4 => '/video/thumbs', 5 => '/audio', 6 => '/documents', 7 => '/applications'];
	if (is_dir($userpath.$username)) {
		$foldercreated = false;
		foreach ($directories as $key => $dir) {
			if (!is_dir($userpath.$username.$dir)) {
				mkdir($userpath.$username.$dir, 0744, true);
				file_put_contents($userpath.$username.$dir.'/.gitignore','# Ignore everything in this directory'."\r\n".'*'."\r\n".'# Except this file'."\r\n".'!.gitignore');
				$foldercreated = true;
			}
		}
		$folderexist = true;
	} 

	$allempty = 0;
	$dir_array = [1 => 'audio', 2 => 'pictures/thumbs', 3 => 'video', 4 => 'documents', 5 => 'applications'];
	foreach ($dir_array as $key => $folder) {
		if ($handle = opendir ($userpath.$username.$folder)) {
			$filelist = [];
			while (false !== ($file = readdir ($handle))) {
				$file = str_replace ('&', '&amp;',$file);
				$file = explode ('\n', $file);
				$file = $file[0];
				$filelist[] = $file;
			}
			natsort ($filelist);
			$remove_from_filelist = ['.','..','.DS_Store','index.html','.htaccess','.gitignore','Thumbs.db','thumbs.db','thumbs'];
			foreach ($remove_from_filelist as $key => $value) {
				if (($key_rff = array_search($value, $filelist)) !== false) {
 					unset($filelist[$key_rff]);
				}
			}
			if (!empty($filelist)) {
				$allempty = 1;
				if ($folder == 'pictures/thumbs') { $folder = 'pictures'; };
				echo '
					<h2>'.ucfirst($folder).'</h2>
					<ul id="'.$folder.'_list"'.(($folder == 'pictures' || $folder == 'video') ? ' class="flexlist"' : '').'>';
					$id_number = 0;
					while (list ($key, $val) = each ($filelist)) {
						if ($val != "." && $val != ".." && in_array(getExtension(strtolower($val)),allowedMimeAndExtensions('extension'))) {
							++$id_number; 
							$usercontrols = '';
							$shared_content = '';
							if (is_link($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.$val)) {
								$shared_content = array_reverse(explode('/',readlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.$val)))[2];
							}
							$document_name = ((strpos($val,'__') == true) ? explode('__',urldecode(ucwords(removeExtension($val)))) : urldecode(ucwords(removeExtension($val))));
							$usercontrols = '<div class="usercontrols">
								<a class="deletefile" href="'.$baseurl.'deletefile.php">
									<i class="fa fa-remove" title="Delete file"></i>
								</a>
								<a class="approvefile" href="'.$processpath.'approvefile.php">
									<i class="fa fa-check" title="Approve file"></i>
								</a>
							</div>';
							if (getExtension($val) && ($folder == 'documents' || $folder == 'audio' || $folder == 'applications')) {
								$fileext = getExtension($val);
								$extension = (($fileext == 'txt') ? 'text-o' : (($fileext == 'xls' || $fileext == 'xlsx') ? 'excel-o' : (($fileext == 'doc' || $fileext == 'docx') ? 'word-o' : (($fileext == 'mp3' || $fileext == 'webm') ? 'audio-o' : (($fileext == 'dmg') ? 'o' : $fileext.'-o')))));
								$fileicon = '<i class="dark-background fa fa-file-'.$extension.'"></i>';
							}
							$document_name = ((is_array($document_name) && $folder == 'documents') ? '<span class="public_sharename">(Uploaded by '.$document_name[0].') - '.$document_name[1].'</span>' : (is_array($document_name) ? '<span class="public_sharename">(Uploaded by '.$document_name[0].')</span>' : '<span class="public_sharename">'.rtrim(trim($document_name),'_-').'</span>'));
							if ($folder == 'video') {
								$getvidfile = 'showfile.php?vidfile='.$val.'';
							}
							$linkdisplay = (($folder == 'pictures') ?
								'<div class="imagecontainer"><a class="lightbox" href="showfile?imgfile='.$val.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').'">
									<img src="showfile.php?imgfile='.$val.'&thumbs=true'.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').'" alt="Thumbnail for '.$val.'">
								</a>'.$usercontrols.$document_name.'</div>' : 
								(($folder == 'video') ? 
								'<div class="tech-slideshow">
									<a class="lightbox" href="'.$getvidfile.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').'">
										<div class="mover-1" style="background: url('.$getvidfile.'.jpg&thumbs=true'.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').');"></div>
										<div class="mover-2" style="background: url('.$getvidfile.'.jpg&thumbs=true'.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').');"></div>
									</a>'.$usercontrols.$document_name.
								'</div>' : 
								(($folder == 'documents' || $folder == 'audio' || $folder == 'applications') ? 
								'<a href="showfile.php?docfile='.$val.'">'.$fileicon.' '.$document_name.'</a>'.$usercontrols : '')));
							$floatleft = (($folder == 'pictures' && !empty($shared_content)) ? 'class="pictures shared"' : (($folder == 'pictures' && empty($shared_folder)) ? 'class="pictures"' : (($folder == 'video') ? 'class="video"' : '')));
							echo '<li '.$floatleft.'>'.$linkdisplay.'</li>';
						}
					}
					closedir ($handle);
					echo '</ul></div>';
				}
			}
		}
		if ($allempty == 0) {
			echo '<p class="messagebox visible warning">There are no files to moderate</p>';
		}
} else {
	echo '<div class="container"><p class="messagebox visible error">You don\'t have access to this page</p></div>';
}
?>