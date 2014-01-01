<?php
ini_set('display_errors',1); // this should be commented out in production environments
error_reporting(E_ALL); // this should be commented out in production environments
ob_start();
session_start();
	require_once('config.php');
	require_once('language.php');
	require_once('functions.php'); 
if ($current_page != 'upload') {
	file_put_contents('current_uploads.php','');
}

?>
<!DOCTYPE html>
<head>
	<title>Medialist</title>

		<link type="text/css" href="style/main.css" rel="stylesheet" media="screen, projection">
		<?php loadFiles('js', $scriptpath); ?>

		<script type="text/javascript">
			$(document).ready(function() {
				$(".deletefile").click(function(e) {
					$this = $(this);
					e.preventDefault();
					var thisFile = $(this).parents('li').find('a:first').attr('href').split('/').reverse();
					$.post('deletefile.php', { filename:thisFile[1]+'/'+thisFile[0] }, function(data) { alert(data); window.location.reload(true)});
					// alert(thisFile[1] + '/' + thisFile[0]);

				})
			})
		</script>
</head>

<body>
	<header>
		<h1><?php echo __MAINHEADING; ?></h1>
	</header>
	<?php echo displayMenu($baseurl, $baseurl_page); ?>
		<div id="main">
<?php
	$display = new PageView();
	if (!$isloggedin && $use_login == true) {
		echo $display->getLogin();
	} else {
		echo $display->getPage();
	}

?>


	</div>
	<footer>
		<?php 
			if ($isloggedin && $use_login == true) {
				echo $display->getLogin();
			}
		?>
	</footer>
</body>
</html>