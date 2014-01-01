<?php

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
?>
<form id="upload" action="<?php echo $baseurl_page; ?>upload" method="post" enctype="multipart/form-data">
	<input type="file" name="file" id="file">
	<input type="submit" name="submit" value="<?php echo __UPLOADSUBMIT; ?>">
</form>

<?php
  echo returnCurrentUploads('current_uploads.php');

	if (isset($_FILES['file'])) {
      $allowed = '';
      $allowed_extensions = allowedExtensions('allowed_extensions.php');
      $totalentries = count(allowedExtensions('allowed_extensions.php')) -1;
      for ($i = 0; $i <= $totalentries; $i++) {
        $allowed .= (($i == $totalentries) ? $allowed_extensions[$i] : $allowed_extensions[$i].', ');
      }

		if ($_FILES['file']['type'] == 'audio/mpeg' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'video/mpeg' || $_FILES['file']['type'] == 'video/avi' || $_FILES['file']['type'] == 'video/x-msvideo' || $_FILES['file']['type'] == 'video/x-ms-wmv' || $_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/gif') {
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
    		if (file_exists(''.$userpath.$username.'/'.$folder.'/'. $_FILES["file"]["name"])) {
      			echo '<p class="messagebox error">'.$_FILES['file']['name'].' already exists</p>';
      		} else {
      			move_uploaded_file($_FILES['file']['tmp_name'],''.$userpath.$username.'/'.$folder.'/'.urlencode(strtolower($_FILES['file']['name'])));
      			echo '<p class="messagebox success">You uploaded: '.$_FILES['file']['name'].'</p>';
            $movedfile = pathinfo($_FILES['file']['name']);
            if (in_array($movedfile['extension'],allowedExtensions('allowed_image_extensions.php'))) {
      			 createThumbs($userpath.$username.'/'.$folder.'/',$_FILES['file']['name'],87);
            }
            updateCurrentUploads('current_uploads.php',$_FILES['file']['name']);
      			header('refresh: 3');
      		}
    	}
  	} else {
      echo '<p class="messagebox error">The filetype you tried to upload is not allowed</p>';
  		echo '<p class="messagebox warning">You can upload the following file-types: '.$allowed.'</p>';
  	}
  	}
}
?>