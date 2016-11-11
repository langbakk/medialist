<?php

if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) || $allow_public == true) {

echo '<div class="container">
	<h2>Upload files</h2>
	<form id="upload" action="process_upload.php" method="post" class="dropzone" enctype="multipart/form-data">
		<input type="file" name="file">
		<input type="submit" value="Upload file"></form>
		</div>';

}

?>