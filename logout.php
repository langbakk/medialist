<?php

if ($isloggedin) {
	echo '<form id="logoutform" method="post" action="login">
		<input type="submit" name="submit_logout" value="Log out">
	</form>';
}

?>