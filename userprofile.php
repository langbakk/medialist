<?php
if (!session_id()) { session_start(); };

if ($isloggedin) {

$original_username = $username;
$username = isset($_GET['user']) ? $_GET['user'] : $username;
$username_readable = ucfirst(explode('/',$username)[0]);

	$disk_used = foldersize($userpath.$username);
	$disk_remaining = $storage_limit - $disk_used;

if ($isloggedin) {
	echo '<span id="username_view">You\'re viewing: <i>'.explode('/',$username)[0].'</i></span>';
}

echo '<div class="container">
	<h2>'.$username_readable.'</h2>
	<div class="content">';
	if (!isset($_GET['user'])) {
		echo '	<p>Logged in with '.$usertype.' rights</p>';
	}
	
	$path = realpath($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$userpath.$username);

	$iterator = new RecursiveDirectoryIterator($path);
	$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
	$filter = new MyRecursiveFilterIterator($iterator);

	$objects  = new RecursiveIteratorIterator($filter,RecursiveIteratorIterator::SELF_FIRST);
	if ($disk_used != 0) {
	echo '<input type="button" id="showhidefilelist" value="Hide filelist">
	<div id="sortingcontainer">';
	$currentsorttype = '<span class="sortlinks"><span class="sortlinktext">Filelist is currently sorted by </span>'.((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbysize') ? 'size' : ((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbydate') ? 'date' : ((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbyname') || !isset($_COOKIE['setsort']) ? 'name' : ''))).'</span>';

	if (isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbydate') {
		$output = '<a href="'.$processpath.'update_cookie.php?setsort=sortbysize"><span class="sortlinktext">Sort by </span>size</a></span>'.$currentsorttype.'<span class="sortlinks"><a href="'.$processpath.'update_cookie.php?setsort=sortbyname"><span class="sortlinktext">Sort by </span>name</a>';
	} elseif (isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbysize') {
		$output = '<a href="'.$processpath.'update_cookie.php?setsort=sortbyname"><span class="sortlinktext">Sort by </span>name</a></span>'.$currentsorttype.'<span class="sortlinks"><a href="'.$processpath.'update_cookie.php?setsort=sortbydate"><span class="sortlinktext">Sort by </span>date</a>';
	} else {
		$output = '<a href="'.$processpath.'update_cookie.php?setsort=sortbysize"><span class="sortlinktext">Sort by </span>size</a></span>'.$currentsorttype.'<span class="sortlinks"><a href="'.$processpath.'update_cookie.php?setsort=sortbydate"><span class="sortlinktext">Sort by </span>date</a>';
	}
	echo '<span class="sortlinks">'.$output.'</span></div>';
}
	echo '<div id="filelist_'.strtolower($username_readable).'">
		<h3>Filelist</h3>
	<ul class="alternate" id="user_filelist">';
		$filesize_array = [];
		$filesize_total = 0;
	if ($disk_used == 0) {
		echo '<li class="messagebox warning">No files uploaded</li>';
	} else {
		$folder = '';
			foreach ($objects as $file) {
		$time = DateTime::createFromFormat('U',filemtime($file->getPathName()));
		if (!$file->isDir()) {
			$files[] = ['filename'=>$file->getPathName(),'time'=>$time->getTimestamp(),'size'=>$file->getSize()];
		} else {
			$folders[] = ['filename'=>$file->getPathName(),'time'=>$time->getTimestamp(),'size'=>$file->getSize()];
		}
	}

		usort($folders, function($a, $b) {
			return $a['filename'] - $b['filename'];
		});

		usort($files, function($a, $b) {
			if (isset($_COOKIE['setsort'])) {
					if ($_COOKIE['setsort'] == 'sortbysize') {
						return $a['size'] - $b['size'];
					} elseif ($_COOKIE['setsort'] == 'sortbydate') {
						return $a['time'] - $b['time'];
					} else {
 						return $a['filename'] - $b['filename'];
 					}
 			}
		});

		foreach ($folders as $key => $value) {
			$filesize_array[] = $value['size']; 
			$filesize_total = $filesize_total + $value['size'];
			$folder = array_reverse(explode(DIRECTORY_SEPARATOR,$value['filename']))[0];
			echo '<li class="heading">'.ucfirst($folder).'</li>'; 
			$id_number = 0;
			foreach ($files as $fkey => $fvalue) {
				++$id_number;
			    $time = date('Y-m-d H:m', $fvalue['time']);
				$filesize_array[] = $fvalue['size'];
				$filesize_total = $filesize_total + $fvalue['size'];
				$filename = array_reverse(explode(DIRECTORY_SEPARATOR,$fvalue['filename']))[0];
				if ($folder == array_reverse(explode(DIRECTORY_SEPARATOR,$fvalue['filename']))[1]) {
					$usercontrols = '<div class="usercontrols">
						<a class="sharefile" href="'.$baseurl.'sharefile.php">
							<i class="fa fa-share-alt" title="Share file"></i>
						</a>';
						if (($isloggedin && $isadmin) || ($isloggedin && $original_username == $username) || (is_array($document_name) && strtolower($document_name[0]) == strtolower(explode('/',$original_username)[0]))) {
							$usercontrols .= '<a class="deletefile" href="'.$baseurl.'deletefile.php">
								<i class="fa fa-remove" title="Delete file"></i>
							</a>';
						}
						if ((($isloggedin && $isadmin) || ($original_username == $username)) && $username != 'public') {
							$usercontrols .= '<form method="post" action="create_public_link.php"><input type="checkbox" id="'.$folder.'_'.$id_number.'" class="hidden make_public" title="Make public" '.((is_link($userpath.'public/'.$folder.'/'.explode('/',$username)[0].'__'.$filename) ? 'checked' : '')).' value="'.((is_link($userpath.'public/'.$folder.'/'.explode('/',$username)[0].'__'.$filename) ? 1 : 0)).'"><label title="'.(is_link($userpath.'public/'.$folder.'/'.explode('/',$username)[0].'__'.$filename) ? 'Undo &quot;make file public&quot;' : 'Make file public').'" for="'.$folder.'_'.$id_number.'"><i class="makepublic fa '.((is_link($userpath.'public/'.$folder.'/'.explode('/',$username)[0].'__'.$filename) ? 'fa-check-square' : 'fa-square')).'"></i></label></form>';
						}
					$usercontrols .= '</div>';
					echo '<li><span class="filename"'.(($folder == 'documents' || $folder == 'applications' || $folder == 'audio') ? ' style="max-width: initial; word-wrap: none;"':'').'>'.$filename.'</span>'.(($folder == 'pictures') ? '<span class="filelist_image"><img src="showfile.php?imgfile='.$filename.'&thumbs=true"></span> ' : (($folder == 'video') ? '<div class="tech-slideshow">
						<div class="mover-1" style="background: url(showfile.php?vidfile='.$filename.'.jpg&thumbs=true);"></div>
						<div class="mover-2" style="background: url(showfile.php?vidfile='.$filename.'.jpg&thumbs=true);"></div>
					</div>' : '')).'<span class="filesize">'.((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbydate') ? $time : format_size($fvalue['size'])).'</span>'.$usercontrols.'</li>';
				}
			}
		}
	}
	echo '</ul>
	</div>';
	$average_size = ($filesize_total != 0) ? format_size($filesize_total / count($filesize_array)) : 0;
	echo '<p class="diskspaceinfo">
			<span><span class="diskspacetitle">Diskspace used: </span>'.format_size($disk_used).'</span>
			<span><span class="diskspacetitle">Diskspace left: </span>'.(($disk_remaining < $average_size * 3) ? '<span class="error">'.format_size($disk_remaining).'</span>' : format_size($disk_remaining)).'</span></p>';

echo '</div>
	<div class="content" id="settings">
		<h3>Settings</h3>
			<input type="button" id="resetlocalstorage" value="Reset site-wide settings" title="This resets all removed info-containers and similar changes done to the website by the user">';
			if (Config::read('use_paypal') == 1) {
				echo '<form id="paypalform" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="'.$paypal_key.'">
					<input type="hidden" name="on0" value="Size selection">
					<label>Upgrade storage limit</label>
					<select name="os0">
						<option value="1 GB">1 GB : $2,99 USD - monthly</option>
						<option value="2 GB">2 GB : $4,99 USD - monthly</option>
						<option value="5 GB">5 GB : $12,99 USD - monthly</option>
					</select>
					<input type="hidden" name="currency_code" value="USD">
					<input type="submit" name="submit" value="Subscribe">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>';
			}
	echo '</div>
</div>';
} else {
	header('Location: login');
}
?>