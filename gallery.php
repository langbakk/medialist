<?php

$username = (isset($_GET['user'])) ? $_GET['user'].'/' : $username;

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
					<ul id="'.$folder.'_list"'.(($folder == 'pictures' || $folder == 'video') ? ' class="flexlist"' : '').'>';
				while (list ($key, $val) = each ($filelist)) {
					if ($val != "." && $val != ".." && in_array(getExtension(strtolower($val)),allowedExtensions(''))) {
						$usercontrols = '';
						$shared_content = '';
						if (is_link($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.$val)) {
							$shared_content = array_reverse(explode('/',readlink($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username.$folder.'/'.$val)))[2];
						}
						$usercontrols = '<div class="usercontrols">
											<a href="'.$baseurl.'sharefile.php">
												<img src="'.$webgfxpath.'share.png" alt="share file">
											</a>'.((isset($_GET['user']) != 'public' && $username != 'public/') ? '<a class="deletefile" href="'.$baseurl.'deletefile.php">
												<img src="'.$webgfxpath.'delete_icon.png" alt="delete file">
											</a><form method="post" action="create_public_link.php"><input type="checkbox" class="make_public" title="Make public" '.((is_link($userpath.'public/'.$folder.'/'.explode('/',$username)[0].'__'.$val) ? 'checked' : '')).' value="'.((is_link($userpath.'public/'.$folder.'/'.explode('/',$username)[0].'__'.$val) ? 1 : 0)).'"></form>':'').'</div>';
						$document_name = ((strpos($val,'__') == true) ? explode('__',urldecode(ucwords(removeExtension($val)))) : urldecode(ucwords(removeExtension($val))));;
						$document_name = ((is_array($document_name) && $folder == 'documents') ? '<span class="public_sharename">(Uploaded by '.$document_name[0].') - '.$document_name[1].'</span>' : (is_array($document_name) ? '<span class="public_sharename">(Uploaded by '.$document_name[0].')</span>' : '<span class="public_sharename">'.$document_name.'</span>'));
						if ($folder == 'video') {
							$getvidfile = 'showfile.php?vidfile='.$val.'';
						}
						$linkdisplay = (($folder == 'pictures') ?
							'<div class="imagecontainer"><a class="lightbox" href="showfile?imgfile='.$val.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').'">
								<img src="showfile.php?imgfile='.$val.'&thumbs=true'.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').'" alt="Thumbnail for '.$val.'">
							</a>'.$usercontrols.$document_name.'</div>' : 
							(($folder == 'video') ? 
							'<div class="tech-slideshow">
								<a href="'.$getvidfile.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').'">
									<div class="mover-1" style="background: url('.$getvidfile.'.jpg&thumbs=true'.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').');"></div>
									<div class="mover-2" style="background: url('.$getvidfile.'.jpg&thumbs=true'.(isset($_GET['user']) ? '&user='.$_GET['user'].'' : '').');"></div>
								</a>'.$usercontrols.$document_name.
							'</div>' : 
							'<a href="showfile.php?docfile='.$val.'">'.$document_name.'</a>'.$usercontrols));
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