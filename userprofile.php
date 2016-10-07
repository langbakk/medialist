<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
require_once('functions.php');

$username_readable = ucfirst(explode('/',$username)[0]);

$disk_used = foldersize($userpath.$username);
$disk_remaining = Config::read('total_filesize_limit') - $disk_used;

echo '<div class="container">
	<h2>'.$username_readable.'</h2>';
	
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
		foreach ($objects as $file) {
			if ($file->isDir()) {
				echo '<li class="heading">'.$file->getFileName().'</li>';
			} elseif (!$file->isDir()) {
				echo '<li>'.$file->getFileName().'</li>';
			}
		}
	}
	echo '</ul>
	</div>
	<p><b>Diskspace used:</b> '.format_size($disk_used).'<br><b>Diskspace left:</b> '.format_size($disk_remaining).'</p>
</div>';
?>