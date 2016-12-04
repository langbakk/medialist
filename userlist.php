<?php

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

	$original_username = $username;
	$username_exist = false;
	$allow_public_users = [];
	sort($user_array);
	
	for ($i = 0; $i < count($user_array); $i++) {
		$exploded_user_array = explode('//',$user_array[$i]);
		$allow_public_users[$exploded_user_array[0]] = (array_key_exists(3, $exploded_user_array) && $exploded_user_array[3] == 1) ? true : false;
	}

	echo '<div class="container">
		<h2>Userlist</h2>
		<div class="content">
			<ul class="alternate">
				<li class="heading">List of users<input id="searchusers" type="search" placeholder="Search users"></li>';
		foreach ($allow_public_users as $key => $value) {
			echo '<li>'.(($value == true && trim(explode('/',$username)[0]) != trim($key)) || ($_SESSION['usertype'] == 'admin' && explode('/',$username)[0] != trim($key)) ? '<a href="gallery?user='.$key.'">' : '').$key.(($value == true && trim(explode('/',$username)[0]) != trim($key)) || ($_SESSION['usertype'] == 'admin' && explode('/',$username)[0] != trim($key)) ? '</a>' : '').'</li>';
		}
	echo '</ul></div>';

	}

?>