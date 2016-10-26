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
	<ul class="alternate">';
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
				echo '<li>'.(($dir == 'pictures') ? '<img src="showfile.php?imgfile='.$file->getFileName().'&thumbs=true"> ' : (($dir == 'video') ? '<div class="tech-slideshow">
						<div class="mover-1" style="background: url(showfile.php?vidfile='.$file->getFileName().'.jpg&thumbs=true);"></div>
						<div class="mover-2" style="background: url(showfile.php?vidfile='.$file->getFileName().'.jpg&thumbs=true);"></div>
					</div>' : '')).'<span class="filename">'.$file->getFileName().'</span></li>';
			}
		}
	}
	echo '</ul>
	</div>';
	$average_size = format_size($filesize_total / count($filesize_array));
	echo '<p><b>Diskspace used:</b> '.format_size($disk_used).'<br><b>Diskspace left:</b> '.((format_size($disk_remaining) < ($average_size * 3)) ? '<span class="error">'.format_size($disk_remaining).'</span>' : format_size($disk_remaining)).'</p>';

echo '</div></div>';
?>