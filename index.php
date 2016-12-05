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
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>'.mb_ucfirst(__MAINHEADING).'</title>

		<link type="text/css" href="style/screen.css" rel="stylesheet" media="screen, projection">';
		loadFiles('js', 'script/');
echo '</head>

<body id="'.$current_page.'_body">
	<header>
		<h1>'.mb_ucfirst(__MAINHEADING).'</h1>
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
	} elseif ($allow_public == true || $isloggedin) {
		echo $display->getPage();
		if (!isset($_GET['page']) || empty($_GET['page'])) {
			echo '<div class="container">
				<h2>Welcome to Uploadr</h2>
				<div class="content">
					<p>Hi. You\'ve found Uploadr. This is a file-storage/upload-whatever-you-want type of site, which enables you to '.(!$isloggedin ? '<a href="register">create a user</a> and' : '').' upload to your private account (and share publicly, if you want)'.(!$isloggedin ? ', or just go to <a href="upload">upload</a> and start uploading stuff.' : '.').' '.(!$isloggedin ? 'If you already have an account, you can <a href="login">log in here</a>.' : '').' There are some limits as to what <a href="allowed_filetypes">type of files</a> you can upload, but most video, image and documents should work without having to adjust the config.</p>'.
					(!$isloggedin ? 
					'<a href="register" class="button_register button">Register here</a>
					<a href="login" class="button_login button">Login here</a>' : '').'
				</div>
			</div>';
		}
	} elseif (!$isloggedin && $use_login == true && (empty($current_page) || $current_page == 'index' || $current_page == 'frontpage' || $current_page == 'login')) {
		echo $display->getLogin();
	} 
echo '
	</div>
	<footer>
	</footer>
		<div id="updateinfo" class="messagebox"></div>
	</div>
	<div id="lightbox_wrapper" class="hidden">
		<div id="lightbox_container">
			<span class="no-touch closebutton">X</span>
			<span class="no-touch nextbutton">&gt;</span>
			<span class="no-touch prevbutton">&lt;</span>
		</div>
	</div>
</body>
</html>';
?>