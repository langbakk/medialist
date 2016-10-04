<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
require_once('functions.php');

$disk_used = foldersize($userpath.$username);
$disk_remaining = Config::read('total_filesize_limit') - $disk_used;

echo '<div class="container">
	<h2>'.ucfirst(explode('/',$username)[0]).'</h2>
	<p><b>Diskspace used:</b> '.format_size($disk_used).'<br><b>Diskspace left:</b> '.format_size($disk_remaining).'</p>
</div>';
?>