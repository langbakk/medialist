<?php
ob_start();
if (!session_id()) { session_start(); };

	require_once('conf/config.php');
	require_once('language.php');
	require_once('functions.php'); 
// if ($current_page != 'upload') {
// 	file_put_contents('.current_uploads','');
// }

$display = new PageView();

echo '<!DOCTYPE html>
<html lang="nb">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>'.__MAINHEADING.'</title>

		<link type="text/css" href="style/screen.css" rel="stylesheet" media="screen, projection">';
		loadFiles('js', 'script/');
echo '</head>

<body id="'.$current_page.'_body">
	<header>
		<h1>'.__MAINHEADING.'</h1>
		'.displayMenu($baseurl);
		if ($isloggedin && $use_login == true) {
			echo $display->getLogin();
		}
echo '</header>
	<div id="top">
	<div id="main">';
	if ($isloggedin) {
		echo '<span id="username_display"><span title="'.(($isloggedin) ? 'You\'re logged in as '.$username.'' : 'You\'re not logged in').'" class="'.(($isloggedin) ? 'isloggedin' : 'notloggedin').'">Current account: <i>'.(($_SESSION['usertype'] == 'admin') ? '<span class="usertype_admin">'.explode('/',$username)[0].'</span>' : '<span class="usertype_user">'.explode('/',$username)[0].'</span>').'</i></span></span>';
	}
	if (isset($_GET['imgfile']) || isset($_GET['vidfile']) || isset($_GET['docfile'])) {
		echo $display->getPage();
	} elseif (!$isloggedin && $use_login == true && (empty($current_page) || $current_page == 'index' || $current_page == 'frontpage' || $current_page == 'login')) {
		echo $display->getLogin();
	} elseif ($allow_public == true || $isloggedin) {
		echo $display->getPage();
		if (!isset($_GET['page']) || empty($_GET['page'])) {
			echo '<div class="container">';
					include_once('index.html');					
			echo '</div>';
		}
	} 
echo '
	</div>
	<div id="updateinfo" class="messagebox"></div>
	</div>
	<div id="lightbox_wrapper" class="hidden"><div id="lightbox_container"><span class="closebutton">X</span><span class="nextbutton">&gt;</span><span class="prevbutton">&lt;</span></div></div>
	<footer>
	</footer>
</body>
</html>';
?>