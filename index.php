<?php
ob_start();
session_start();
	require_once('config.php');
	require_once('functions.php'); 
	require_once('language.php');



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
		<div id="main">
<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
?>
			<?php
				$allempty = 0;
				$dir_array = array(1 => 'music', 2 => 'pictures', 3 => 'video');
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
									echo '<div class="container">
									<h2>'.ucfirst($folder).'</h2>
									<ul>';

								//$allowed_extensions = array('jpg','jpeg','png','gif','avi','mpeg','mpg','mp3','wmv','mkv','flv');
								while (list ($key, $val) = each ($filelist)) {
									if ($val != "." && $val != ".." && in_array(getExtension($val),allowedExtensions('allowed_extensions'))) {
										echo '<li><a href="'.$folder.'/'.$val.'">'.ucwords(removeExtension($val)).'</a></li>';
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
				if ($allow_quotes == true) { // this setting can be changed in config.php
					include 'quotes.php';
				}
			?>

<form id="upload" action="index.php" method="post" enctype="multipart/form-data">
	<label for="file"><?php echo __UPLOADLABEL; ?>:</label>
	<input type="file" name="file" id="file">
	<input type="submit" name="submit" value="<?php echo __UPLOADSUBMIT; ?>">
</form>

<?php
	if (isset($_FILES['file'])) {
		echo $_FILES['file']['type'];
	if ($_FILES['file']['type'] == 'audio/mpeg' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'video/mpeg' || $_FILES['file']['type'] == 'video/avi' || $_FILES['file']['type'] == 'video/x-msvideo' || $_FILES['file']['type'] == 'video/x-ms-wmv') {
		if ($_FILES['file']['type'] == 'audio/mpeg') {
			$folder = 'music';
		} elseif ($_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/gif') {
			$folder = 'pictures';
		} elseif ($_FILES['file']['type'] == 'video/mpeg' || $_FILES['file']['type'] == 'video/avi' || $_FILES['file']['type'] == 'video/x-msvideo' || $_FILES['file']['type'] == 'video/x-ms-wmv') {
			$folder = 'video';
		}
  		if ($_FILES['file']['error'] > 0) {
    		echo '<p class=" messagebox error">Return Code: '.$_FILES["file"]["error"].'</p>';
    	} else {
    		if (file_exists(''.$folder.'/'. $_FILES["file"]["name"])) {
      			echo '<p class="messagebox error">'.$_FILES['file']['name'].' already exists</p>';
      		} else {
      			move_uploaded_file($_FILES['file']['tmp_name'],''.$folder.'/'.strtolower($_FILES['file']['name']));
      			echo '<p class="messagebox success">Du lastet opp: '.$_FILES['file']['name'].'</p>';
      			header('refresh: 5');
      		}
    	}
  	} else {
  		echo '<p class="messagebox warning">Du kan laste opp mp3, mpeg, avi og jpg-filer</p>';
  	}
  	}

} 

if ($use_login == true) {
		include 'login.php';
	}
?>

	</div>
</body>
</html>