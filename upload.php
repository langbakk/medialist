<?php
if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) || $allow_public == true) {

echo '<h3>Upload files:</h3>
	<form id="upload" action="process_upload.php" method="post" class="dropzone"></form>';

}

?>