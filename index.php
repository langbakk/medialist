<?php
ini_set('display_errors',1); // this should be commented out in production environments
error_reporting(E_ALL); // this should be commented out in production environments
ob_start();
if (!session_id()) { session_start(); };

	require_once('conf/config.php');
	require_once('language.php');
	require_once('functions.php'); 
if ($current_page != 'upload') {
	file_put_contents('current_uploads.php','');
}

$display = new PageView();

echo '<!DOCTYPE html>
<html lang="nb">
	<head>
		<meta charset="UTF-8">
	<title>'.__MAINHEADING.'</title>

		<link type="text/css" href="style/screen.css" rel="stylesheet" media="screen, projection">';
		loadFiles('js', 'script/');
echo '</head>

<body>
	<header>
		<h1>'.__MAINHEADING.'</h1>
	</header>
	'.displayMenu($baseurl).'
	<div id="main">';
	if (!$isloggedin && $use_login == true && (empty($current_page) || $current_page == 'index' || $current_page == 'frontpage' || $current_page == 'login')) {
		echo $display->getLogin();
	} elseif ($allow_public == true || $isloggedin) {
		echo $display->getPage();
	} 
echo '
	</div>
	<div id="updateinfo" class="messagebox"></div>
	<footer>';
	if ($isloggedin && $use_login == true) {
		echo $display->getLogin();
	}
echo '
	</footer>
</body>
</html>';
?>