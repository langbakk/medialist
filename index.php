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
	<?php echo displayMenu($baseurl); ?>
		<div id="main">
<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

				$allempty = 0;
				$dir_array = array(1 => $userpath.$username.'/music', 2 => $userpath.$username.'/pictures/thumbs', 3 => $userpath.$username.'/video');
					foreach ($dir_array as $key => $folder) {
						if ($handle = opendir ($folder)) {
							$filelist = array();
							while (false !== ($file = readdir ($handle))) {
								$file = str_replace ("&", "&amp;",$file);
								$file = explode ("\n", $file);
								$file = $file[0];
								$filelist[] = $file;
							}
							natsort ($filelist);
							reset ($filelist);
							if (count($filelist) != 3) {
									$allempty = 1;
									if ($folder == $userpath.$username.'/pictures/thumbs') { $folder = 'pictures'; };
									echo '<div class="container">
									<h2>'.ucfirst($folder).'</h2>
									<ul>';
								//$allowed_extensions = array('jpg','jpeg','png','gif','avi','mpeg','mpg','mp3','wmv','mkv','flv');
								while (list ($key, $val) = each ($filelist)) {
									if ($val != "." && $val != ".." && in_array(getExtension($val),allowedExtensions('allowed_extensions.php'))) {
										$display = ($folder == 'pictures') ? '<img src="'.$userpath.$username.'/'.$folder.'/thumbs/'.$val.'">' : ucwords(removeExtension($val));
										$floatleft = ($folder == 'pictures') ? 'class="left pictures"' : '';

										echo '<li '.$floatleft.'><a href="'.$userpath.$username.'/'.$folder.'/'.$val.'">'.$display.'</a><span class="right"><a href="'.$baseurl.'deletefile.php"><img src="'.$baseurl.$webgfxpath.'delete_icon.png" alt="delete file"></a></span></li>';
									}
								}
							closedir ($handle);
							echo "</ul></div>";
							}
						}
					}
			?>
			<?php 
				if ($allempty == 0) {
					echo '<div class="container">
						<p>No files were found on the server matching the configured criteria. Choose files to upload below</p>
						</div>';
				}
				if ($show_quotes == true) { // this setting can be changed in config.php
					include 'quotes.php';
				}
			}
			?>
<?php
if ($use_login == true) {
		include 'login.php';
	}
?>


	</div>
</body>
</html>