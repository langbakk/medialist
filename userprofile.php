<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
require_once('functions.php');

$username_readable = ucfirst(explode('/',$username)[0]);

	$disk_used = foldersize($userpath.$username);
	$disk_remaining = Config::read('total_filesize_limit') - $disk_used;


echo '<div class="container">
	<h2>'.$username_readable.'</h2>
	<div class="content">
	<p>Logged in with '.$usertype.' rigths</p>';
	
	$path = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$userpath.$username);

	$iterator = new RecursiveDirectoryIterator($path);
	$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
	$filter = new MyRecursiveFilterIterator($iterator);

	$objects  = new RecursiveIteratorIterator($filter,RecursiveIteratorIterator::SELF_FIRST);

	echo '<input type="button" id="showhidefilelist" value="Show filelist">
	<div id="filelist_'.strtolower($username_readable).'" class="hidden">
		<h3>Filelist</h3>
	<ul class="alternate" id="user_filelist">';
	if ($disk_used == 0) {
		echo '<li class="messagebox warning">No files uploaded</li>';
	} else {
		// echo '<li class="heading">Filelist</li>';
		// $get_dirname = '';
		$dir = '';
		$filesize_array = [];
		$filesize_total = 0;
		foreach ($objects as $file) {
			if ($file->isDir()) {
				$dir = $file->getFileName();
				echo '<li class="heading">'.ucfirst($file->getFileName()).'</li>';
			} elseif (!$file->isDir()) {
				$filesize_array[] = $file->getSize();
				$filesize_total = $filesize_total + $file->getSize();
				$usercontrols = '<div class="usercontrols">
					<a href="'.$baseurl.'sharefile.php">
						<img src="'.$webgfxpath.'share.png" alt="share file">
					</a>'.((isset($_GET['user']) != 'public' && $username != 'public/') ? '<a class="deletefile" href="'.$baseurl.'deletefile.php">
						<img src="'.$webgfxpath.'delete_icon.png" alt="delete file">
					</a><form method="post" action="create_public_link.php"><input type="checkbox" class="make_public" title="Make public" '.((is_link($userpath.'public/'.$dir.'/'.explode('/',$username)[0].'__'.$file->getFileName()) ? 'checked' : '')).' value="'.((is_link($userpath.'public/'.$dir.'/'.explode('/',$username)[0].'__'.$file->getFileName()) ? 1 : 0)).'"></form>':'').'</div>';
				echo '<li><span class="filename"'.(($dir == 'documents') ? ' style="max-width: initial; word-wrap: none;"':'').'>'.$file->getFileName().'</span>'.(($dir == 'pictures') ? '<span class="filelist_image"><img src="showfile.php?imgfile='.$file->getFileName().'&thumbs=true"></span> ' : (($dir == 'video') ? '<div class="tech-slideshow">
						<div class="mover-1" style="background: url(showfile.php?vidfile='.$file->getFileName().'.jpg&thumbs=true);"></div>
						<div class="mover-2" style="background: url(showfile.php?vidfile='.$file->getFileName().'.jpg&thumbs=true);"></div>
					</div>' : '')).'<span class="filesize">'.format_size($file->getSize()).'</span>'.$usercontrols.'</li>';
			}
		}
	}
	echo '</ul>
	</div>';
	$average_size = format_size($filesize_total / count($filesize_array));
	echo '<p><b>Diskspace used:</b> '.format_size($disk_used).'<br><b>Diskspace left:</b> '.((format_size($disk_remaining) < ($average_size * 3)) ? '<span class="error">'.format_size($disk_remaining).'</span>' : format_size($disk_remaining)).'</p>';

echo '</div></div>';
?>