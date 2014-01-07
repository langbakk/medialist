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

// print_r(allowedMimeTypes('allowed_mimetypes.php','video'));

?>
<!DOCTYPE html>
<html lang="nb">
	<head>
		<meta charset="UTF-8">
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
				$("input[type=file]").on('change',function() {
					//console.log($(this));
					var thisContent = $(this).val();
					if (thisContent != 'No file selected') {
						$(this).removeClass('inactive').addClass('active');
						$.post('retrieve_folder.php',{ currentfile:thisContent }, function(data) { 
							if (data != '') {
								data = $.parseJSON(data);
								for (var key in data) {
									var optionText = ucfirst(data[key]);
									$("#folderchoicecontainer").show();
									$('#folderchoice').append('<option value="'+data[key]+'">'+optionText+'</option>').removeAttr('disabled');
								}
							}
						});
						$('#createfolder').removeClass('hidden');
					};
				})
				$("#uploadreset").click(function() {
					$("#file").val('');
					$("#file").removeClass('active').addClass('inactive');
				})
			});
			function ucfirst(text) {
 			   return text.substr(0, 1).toUpperCase() + text.substr(1);    
			}
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