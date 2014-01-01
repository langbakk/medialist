<?php
ini_set('display_errors',1); // this should be commented out in production environments
error_reporting(E_ALL); // this should be commented out in production environments
file_put_contents('current_uploads.php','');
ob_start();
session_start();
	require_once('config.php');
	require_once('language.php');
	require_once('functions.php'); 

?>
<!DOCTYPE html>
<head>
	<title>Medialist</title>

		<link type="text/css" href="style/main.css" rel="stylesheet" media="screen, projection">

</head>

<body>
	<header>
		<h1><?php echo __MAINHEADING; ?></h1>
	</header>
	<?php echo displayMenu($baseurl, $baseurl_page); ?>
		<div id="main">
<?php
	$display = new PageView();
	if (!$isloggedin) {
		echo $display->getLogin();
	} else {
		echo $display->getPage();
	}

if ($use_login == true) {
		include 'login.php';
	}
?>


	</div>
</body>
</html>