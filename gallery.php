<?php

if (isset($_GET['user'])) {
	$username = $_GET['user'].'/';
}

if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) || $allow_public == true) {
	$allempty = 0;
	$dir_array = [1 => 'music', 2 => 'pictures/thumbs', 3 => 'video', 4 => 'documents'];
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
				echo '<div class="container">
						<h2>'.ucfirst($folder).'</h2>
					<ul id="'.$folder.'_list" class="flexlist">';
				while (list ($key, $val) = each ($filelist)) {
					if ($val != "." && $val != ".." && in_array(getExtension(strtolower($val)),allowedExtensions(''))) {
						$usercontrols = '';
						$shared_content = '';
						if (is_link($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.$val)) {
							$shared_content = array_reverse(explode('/',readlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.$val)))[2];
						}
						if ($isloggedin) {
							$usercontrols = '<span class="usercontrols"><a href="'.$baseurl.'sharefile.php"><img src="'.$webgfxpath.'share.png" alt="share file"></a><a class="deletefile" href="'.$baseurl.'deletefile.php"><img src="'.$webgfxpath.'delete_icon.png" alt="delete file"></a><a class="make_public" href="'.$baseurl.'create_public_link.php">Public</a></span>';
						}
						$linkdisplay = (($folder == 'pictures') ? '<a href="'.$userpath.$username.$folder.'/'.$val.'"><img src="'.$userpath.$username.$folder.'/thumbs/'.$val.'"></a>'.$usercontrols.'' : (($folder == 'video') ? '<div class="tech-slideshow"><a href="'.$userpath.$username.$folder.'/'.$val.'"><div class="mover-1" style="background: url('.$userpath.$username.$folder.'/thumbs/'.$val.'.jpg);"></div><div class="mover-2" style="background: url('.$userpath.$username.$folder.'/thumbs/'.$val.'.jpg);"></div></a>'.$usercontrols.'</div>' : urldecode(ucwords(removeExtension($val)))));
						$floatleft = (($folder == 'pictures' && !empty($shared_content)) ? 'class="pictures shared"' : (($folder == 'pictures' && empty($shared_folder)) ? 'class="pictures"' : (($folder == 'video') ? 'class="video"' : '')));
						echo '<li '.$floatleft.'>'.$linkdisplay.'</li>';
					}
				}
				closedir ($handle);
				echo '</ul></div>';
			}
		}
	}
	echo '<div class="container '.(($allempty == 0) ? 'visible' : 'hidden').'"><p>No files were found on the server matching the configured criteria. <a href="upload">Upload files</a></p></div>';
	if ($show_quotes == true) { // this setting can be changed in config.php
		include 'quotes.php';
	}
}
//echo '</div>';
?>