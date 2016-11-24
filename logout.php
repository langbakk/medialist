<?php

if ($isloggedin) {
	if ($_SESSION['usertype'] == 'admin') {
		echo '<a id="admin_menu" href="admin"><i class="fa fa-gear"></i></a>';
	} elseif ($_SESSION['usertype'] == 'user') {
		echo '<a id="admin_menu" href="userprofile"><i class="fa fa-gear"></i></a>';
	}
	echo '<form id="logoutform" method="post" action="login">
		<input type="submit" name="submit_logout" value="Log out">
	</form>';
}

?>