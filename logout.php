<?php

if ($isloggedin) {
	if ($_SESSION['usertype'] == 'admin') {
		echo '<i id="admin_menu" class="fa fa-gear"></i>
				<ul id="settings_list">
					<li><a href="admin">Admin</a></li>';
					if (Config::read('moderation_queue') == true) {
					echo '<li><a href="moderate">Moderate uploads</a></li>';	
					}
					echo '<li><a href="userprofile#settings">Usersettings</a></li>
				</ul>';
	} elseif ($_SESSION['usertype'] == 'user') {
		echo '<a id="user_menu" href="userprofile#settings"><i class="fa fa-gear"></i></a>';
	}
	echo '<form id="logoutform" method="post" action="login">
		<input type="submit" name="submit_logout" value="Log out">
	</form>';
}

?>