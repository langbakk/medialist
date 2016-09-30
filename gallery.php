<?php
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
					<ul>';
				while (list ($key, $val) = each ($filelist)) {
					if ($val != "." && $val != ".." && in_array(getExtension($val),allowedExtensions(''))) {
						$display = (($folder == 'pictures') ? '<img src="'.$userpath.$username.$folder.'/thumbs/'.$val.'">' : (($folder == 'video') ? '<img src="'.$userpath.$username.$folder.'/thumbs/'.$val.'.jpg">' : urldecode(ucwords(removeExtension($val)))));
						$floatleft = (($folder == 'pictures') ? 'class="left pictures"' : ($folder == 'video') ? 'class="left video"' : '');
						echo '<li '.$floatleft.'><a href="'.$userpath.$username.$folder.'/'.$val.'">'.$display.'</a><span class="usercontrols"><a href="'.$baseurl.'sharefile.php"><img src="'.$webgfxpath.'share.png" alt="share file"></a><a class="deletefile" href="'.$baseurl.'deletefile.php"><img src="'.$webgfxpath.'delete_icon.png" alt="delete file"></a></span></li>';
					}
				}
				closedir ($handle);
				echo "</ul></div>";
			}
		}
	}

	if ($allempty == 0) {
		echo '<div class="container">
			<p>No files were found on the server matching the configured criteria. <a href="upload">Choose files to upload</a></p>
		</div>';
	}
	if ($show_quotes == true) { // this setting can be changed in config.php
		include 'quotes.php';
	}
}
?>