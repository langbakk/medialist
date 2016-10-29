<?php
if (!session_id()) { session_start(); };
require_once('conf/config.php');
require_once('functions.php');

if ($isloggedin) {

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

// foreach($files as $file) {
//     $time = DateTime::createFromFormat('U', filemtime($file->getPathname()));
//     // no need to explode the time, just make it a datetime object
//     if(in_array($file->getExtension(), $display) && $time > new DateTime('2014-01-15')) { // is PHP and is greater than jan 15 2014
//         $data[] = array('filename' => $file->getPathname(), 'time' => $time->getTimestamp()); // push inside
//     }

// }
// usort($data, function($a, $b){ // sort by time latest
//     return $b['time'] - $a['time'];
// });


// foreach ($data as $key => $value) {
//     $time = date('Y-m-d', $value['time']);
//     echo "
//         <span style='color: red;'>
//             <b style='color: green;'>$time</b>$value[filename]
//         </span>
//         <br/>
//     ";
// }


	echo '<input type="button" id="showhidefilelist" value="Show filelist">
	<span class="sortlinks">';

	if (isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbydate') {
		$output = '<a href="update_cookie.php?setsort=sortbysize">Sort by size</a><a href="update_cookie.php?setsort=sortbyname">Sort by name</a>';
	} elseif (isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbysize') {
		$output = '<a href="update_cookie.php?setsort=sortbyname">Sort by name</a><a href="update_cookie.php?setsort=sortbydate">Sort by date</a>';
	} else {
		$output = '<a href="update_cookie.php?setsort=sortbysize">Sort by size</a><a href="update_cookie.php?setsort=sortbydate">Sort by date</a>';
	}
	echo $output.'</span>
	<span class="sortlinks">Filelist is currently sorted by '.((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbysize') ? 'size' : ((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbydate') ? 'date' : ((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbyname') || !isset($_COOKIE['setsort']) ? 'name' : ''))).'</span>
	<div id="filelist_'.strtolower($username_readable).'" '.(((isset($_COOKIE['showuserfilelist']) && $_COOKIE['showuserfilelist'] == 1) || !isset($_COOKIE['showuserfilelist'])) ? '' : 'class="hidden"').'>
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

		// $files = [];
		// $dirs = [];
		// foreach ($objects as $file) {
		// 	if ($file->isDir()) {
		// 		$dirs[] = $file->getFileName();
		// 	}
		// 	if (!$file->isDir()) {
		// 		if (isset($_COOKIE['sortbysize']) && $_COOKIE['sortbysize'] == 1) {
		// 			$files[$file->getFileName()] = $file->getSize();
		// 		} elseif (isset($_COOKIE['sortbyname']) && $_COOKIE['sortbyname'] == 1) {
		// 			$files[$file->getSize()] = $file->getFileName();
		// 		} else {
		// 			$files[$file->getSize()] = $file->getFileName();
		// 		}
		// 	}
		// }
		// natcasesort($files);
		// $dirs = array_merge($dirs,$files);
		// print_r($dirs);
			foreach ($objects as $file) {
		$time = DateTime::createFromFormat('U',filemtime($file->getPathName()));
		if (!$file->isDir()) {
			$files[] = ['filename'=>$file->getPathName(),'time'=>$time->getTimestamp(),'size'=>$file->getSize()];
		} else {
			$dirs[] = ['filename'=>$file->getPathName(),'time'=>$time->getTimestamp(),'size'=>$file->getSize()];
		}
	}

		usort($dirs, function($a, $b) {
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

		foreach ($dirs as $key => $value) {
			$filesize_array[] = $value['size']; //file->getSize();
			$filesize_total = $filesize_total + $value['size']; //file->getSize();
			$dir = array_reverse(explode('/',$value['filename']))[0];//$file->getFileName();
			echo '<li class="heading">'.ucfirst($dir).'</li>'; 
			foreach ($files as $fkey => $fvalue) {
			    $time = date('Y-m-d H:m', $fvalue['time']);
				$filesize_array[] = $fvalue['size']; //file->getSize();
				$filesize_total = $filesize_total + $fvalue['size']; //file->getSize();
				$filename = array_reverse(explode('/',$fvalue['filename']))[0];
				if ($dir == array_reverse(explode('/',$fvalue['filename']))[1]) {
					$usercontrols = '<div class="usercontrols">
					<a href="'.$baseurl.'sharefile.php">
						<img src="'.$webgfxpath.'share.png" alt="share file">
					</a>'.((isset($_GET['user']) != 'public' && $username != 'public/') ? '<a class="deletefile" href="'.$baseurl.'deletefile.php">
						<img src="'.$webgfxpath.'delete_icon.png" alt="delete file">
					</a><form method="post" action="create_public_link.php"><input type="checkbox" class="make_public" title="Make public" '.((is_link($userpath.'public/'.$dir.'/'.explode('/',$username)[0].'__'.$filename) ? 'checked' : '')).' value="'.((is_link($userpath.'public/'.$dir.'/'.explode('/',$username)[0].'__'.$filename) ? 1 : 0)).'"></form>':'').'</div>';
					echo '<li><span class="filename"'.(($dir == 'documents') ? ' style="max-width: initial; word-wrap: none;"':'').'>'.$filename.'</span>'.(($dir == 'pictures') ? '<span class="filelist_image"><img src="showfile.php?imgfile='.$filename.'&thumbs=true"></span> ' : (($dir == 'video') ? '<div class="tech-slideshow">
						<div class="mover-1" style="background: url(showfile.php?vidfile='.$filename.'.jpg&thumbs=true);"></div>
						<div class="mover-2" style="background: url(showfile.php?vidfile='.$filename.'.jpg&thumbs=true);"></div>
					</div>' : '')).'<span class="filesize">'.((isset($_COOKIE['setsort']) && $_COOKIE['setsort'] == 'sortbydate') ? $time : format_size($fvalue['size'])).'</span>'.$usercontrols.'</li>';
				}
			}
		}
	}
	echo '</ul>
	</div>';
	$average_size = format_size($filesize_total / count($filesize_array));
	echo '<p><b>Diskspace used:</b> '.format_size($disk_used).'<br><b>Diskspace left:</b> '.((format_size($disk_remaining) < ($average_size * 3)) ? '<span class="error">'.format_size($disk_remaining).'</span>' : format_size($disk_remaining)).'</p>';

echo '</div></div>';
} else {
	header('Location: login');
}
?>