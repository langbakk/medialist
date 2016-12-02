<?php

$getconfigvars = file_exists('conf/config.php') ? file('conf/config.php', FILE_IGNORE_NEW_LINES) : '';
$lines_in_configfile = file_exists('conf/config.php') ? count(file('conf/config.php')) : '';
$currentvars = '';
$list_of_setupnames = ['websitename','dbhost','dbport','dbname','dbusername','dbpassword','prefix','allow_public','allow_userlist','use_login','show_quotes','use_db','debug','rootfolder','main_support_email','unique_key'];
$menu_array = 	[0 => ['href'=>'#user_management_container','menutext'=>'User management'],
				 1 => ['href'=>'#change_settings_container','menutext'=>'Change settings'],
				 2 => ['href'=>'#setup_container','menutext'=>'Setup config file']
				];

echo '<div class="container">
	<h2>Control panel</h2>';
	if ($isloggedin && $_SESSION['usertype'] == 'admin') {
		echo '<p id="controlpanel_info" class="messagebox visible info remove_box">Here you will find all the modifiable settings for the page. The settings you change here will be updated in the config-file. You might need to log out and back in for the changes to take effect.</p>
	<div class="content">
		<ul id="adminmenu" class="flexlist">';
	foreach ($menu_array as $key => $value) {
		echo '<li><a href="'.$value['href'].'">'.$value['menutext'].'</a></li>';
	}
	echo '</ul>';

echo '<div id="setup_container" class="admincontainer">
		<h2>Setup the config-file</h2>
			<form method="post" id="configform" class="configform" action="/setup/process_setup.php">';

			for ($i=0; $i < $lines_in_configfile; $i++) {
				if (strstr($getconfigvars[$i], '=')) {
					$first = strstr($getconfigvars[$i], '=', true);
					$remove_content = array('=',' ','\'',';');
					$replace_content = array('','','','');
					$last = str_replace($remove_content,$replace_content,strstr($getconfigvars[$i], '='));
					$name = str_replace('$','',rtrim($first));
					if (in_array($name,$list_of_setupnames)) {
						switch ($name) {
							case 'websitename':
								$label = 'Website name';
								$desc = 'Enter the name used for the website';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;
								$hidden = '';
								break;
							case 'dbhost':
								$label = 'Database Hostname';
								$desc  = 'Enter the hostname (or IP) for your database';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;
								$hidden = (Config::read('use_db') == true) ? '' : 'hidden';
								break;
							case 'dbport':
								$label = 'Database Port';
								$desc  = 'If your database is set up with a specific port, enter it here';
								$required = '';
								$content = $last;
								$checkbox = false;
								$hidden = (Config::read('use_db') == true) ? '' : 'hidden';
								break;
							case 'dbname':
								$label = 'Database Name';
								$desc  = 'Enter your database name. If you don\'t know, you can try using \'localhost\'';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;								
								$hidden = (Config::read('use_db') == true) ? '' : 'hidden';								
								break;
							case 'dbusername':
								$label = 'Database Username';
								$desc  = 'Enter the username for your database';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;								
								$hidden = (Config::read('use_db') == true) ? '' : 'hidden';								
								break;
							case 'dbpassword':
								$label = 'Database Password';
								$desc  = 'Enter the password for your database';
								$required = '';
								$content = $last;
								$checkbox = false;	
								$hidden = (Config::read('use_db') == true) ? '' : 'hidden';															
								break;
							case 'prefix':
								$label = 'Database table-prefix';
								$desc  = 'Enter a prefix for the database tables';
								$required = '';
								$content = $last;
								$checkbox = false;								
								$hidden = (Config::read('use_db') == true) ? '' : 'hidden';								
								break;
							case 'allow_public':
								$label = 'Allow public access';
								$desc = 'Allow for non logged in users to upload and view public files';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';																
								break;	
							case 'allow_userlist':
								$label = 'Allow showing userlist';
								$desc = 'Allow for showing userlist of users who hasn\'t turned this off in their settings';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';								
								break;		
							case 'use_login':
								$label = 'Use login/registreing';
								$desc = 'Allow for users to register and log in';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';								
								break;	
							case 'debug':
								$label = 'Show debug-messages';
								$desc = 'Shows debug messages on different pages, and logs to log-folder. Should not be used on production site';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';								
								break;	
							case 'show_quotes':
								$label = 'Show quotes on Gallery-page';
								$desc = 'Shows short quotes by famous people on the Gallery page';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';								
								break;	
							case 'use_db':
								$label = 'Use database';
								$desc = 'Shows setup-information for database, and allows for database backend';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';								
								break;
							case 'rootfolder':
								$label = 'Installation folder on webhost';
								$desc  = 'If this is the root folder, leave it as /, if not, enter the folder in the following format: /installfolder/';
								$required = 'required="required"';
								$content = !empty($last) ? $last : '/';
								$checkbox = false;	
								$hidden = '';								
								break;
							case 'main_support_email':
								$label = 'Support Email';
								$desc  = 'Enter an email address for any support questions or similar - this is basically the founder account';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;	
								$hidden = '';								
								break;
							case 'unique_key':
								$label = 'Unique encryption key';
								$desc  = 'Enter a 16character or more random selection of keys. This is not something you need to remember.';
								$required = 'required="required"';
								$randomkey = generateRandomString(true,true,true,'',20);
								$content = !empty($last) ? $last : $randomkey;
								$checkbox = false;	
								$hidden = '';																
						};
					$setvar = (isset($_POST['configcreation'])) ? $_POST[$name] : '';
					if ($getconfigvars[$i] == '$'.$name.' = \'\';') {
						$getconfigvars[$i] = '$'.$name.' = \''.$setvar.'\';';
					}
					if (!file_exists('conf/config.php')) {
						if ($name == 'inactive') {
							$content = '600';
						} elseif ($name == 'rootfolder') {
							$content = '/';
						} else {
							$content = '';
						}
					}
echo '		<p class="'.$hidden.'">
						<label class="left" for="'.$name.'"><i class="fa fa-question-circle-o" title="'.$desc.'"></i> '.$label.'</label>';
						if ($checkbox == true) {
							echo '<input class="configinput" type="checkbox" id="'.$name.'" name="'.$name.'" '.$required.' '.(($content == 'true') ? 'checked' : '').'>';
						} else {
						echo '<input class="configinput" type="text" id="'.$name.'" name="'.$name.'" value="'.$content.'" '.$required.' tabindex="'.$i.'">';
						}
						echo '<span class="infobox right"><span class="left"></span></span>
					</p>';
					}
				}
			}

echo '			<p class="buttoncontainer">
							<input class="button error" type="submit" id="configdelete" name="configdelete" value="Delete existing config-file">
							<input class="success button" type="submit" id="configcreation" name="configcreation" value="Create config-file" tabindex="'.($i + 1).'"></p>
			</form>
		<!-- end setup_container --></div>
		<div id="user_management_container" class="admincontainer">
		<h2>User management</h2>';
			$usertypes = ['admin','user'];
			$newuserlist = '#username // password // userrole // allow userlist link // diskspace-setting'."\r\n";
			$c = 0;
			foreach ($user_array as $uakey => $uavalue) {
				$c++;
				echo '<form method="post" action="#user_management_container" class="user_management_form">';
				$user = explode('//',$uavalue);
				if (isset($_POST['submit_userchanges']) && ($_POST['username'] == trim($user[0]))) {
					$newuserlist .= '';
				} elseif ($c == count($user_array)) {
					$newuserlist .= $uavalue;
				} else  {
					$newuserlist .= $uavalue."\r\n";	
				}
				
				if (isset($_POST['submit_userchanges']) && ($_POST['username'] == trim($user[0]))) {
					$updated_username = $_POST['username'];
					$updated_password = isset($_POST['password']) ? $_POST['password'] : trim($user[1]);
					$updated_usertype = isset($_POST['usertype']) ? $_POST['usertype'] : trim($user[2]);
					$updated_userlistlink = isset($_POST['userlistlink']) ? 1 : 0;
					$updated_userdiskspace = (isset($_POST['userdiskspace']) && $_POST['userdiskspace'] != $defaultsize) ? $_POST['userdiskspace'] : $defaultsize;
					$updated_userstartpage = (isset($_POST['userstartpage'])) ? $_POST['userstartpage'] : trim($user[5]);
					$newuserlist .= $updated_username.' // '.$updated_password.' // '.$updated_usertype.' // '.$updated_userlistlink.' // '.$updated_userdiskspace.' // '.$updated_userstartpage."\r\n";
					// echo $updated_username.' '.$updated_password.' '.$updated_usertype.' '.$updated_userlistlink.' '.$updated_userdiskspace;
				}
				$formusername = trim($user[0]);
				echo '<label for="username_'.$formusername.'">Username<br>
					<input id="username_'.$formusername.'" type="text" disabled value="'.$formusername.'"></label>
					<input type="hidden" name="username" value="'.$formusername.'">
					<label for="password_'.$formusername.'">Password<br>
					<input id="password_'.$formusername.'" name="password" type="text" disabled placeholder="New password"></label>
					<label for="usertype_'.$formusername.'">Usertype<br>
					<select id="usertype_'.$formusername.'" name="usertype" autocomplete="off">';
						foreach ($usertypes as $utkey => $utvalue) {
							$setvalue = (isset($_POST['usertype']) && ($_POST['username'] == $formusername)) ? $_POST['usertype'] : $utvalue;
							$selected = ((isset($_POST['usertype']) && ($_POST['username'] == $formusername) && $_POST['usertype'] == $utvalue) ? 'selected' : (((!isset($_POST['usertype']) || ($_POST['username'] != $formusername)) && trim($user[2]) == $utvalue) ? 'selected' : '')); 
							echo '<option value="'.$utvalue.'" '.$selected.'>'.ucfirst($utvalue).'</option>';
						}
					echo '</select></label>
					<label for="userlistlink_'.$formusername.'">Show in userlist<br>
					<input id="userlistlink_'.$formusername.'" type="checkbox" name="userlistlink" '.(($user[3] == 1) ? 'checked' : '').'></label>
					<label for="userdiskspace_'.$formusername.'">Disk space<br>
					<input id="userdiskspace_'.$formusername.'" type="text" name="userdiskspace" value="'.(!empty($user[4]) ? $user[4] : $defaultsize).'"></label>
					<label for="userstartpage_'.$formusername.'">Preferred startpage<br>
					<select id="userstartpage_'.$formusername.'" name="userstartpage" autocomplete="off">';
					foreach (Config::read('menu_array') as $menukey => $menuvalue) {
						if ($menuvalue != 'login' &&  $menuvalue != 'register' && $menuvalue != 'home') {
							$setvalue = (isset($_POST['userstartpage']) && ($_POST['username'] == $formusername)) ? $_POST['userstartpage'] : $_SESSION['userstartpage'];
							$selected = ((isset($_POST['userstartpage']) && ($_POST['username'] == $formusername) && $_POST['userstartpage'] == $menuvalue) ? 'selected' : (((!isset($_POST['userstartpage']) || ($_POST['username'] != $formusername)) && trim($user[5]) == $menuvalue) ? 'selected' : (((empty($user[5]) && $menuvalue == 'upload') ? 'selected' : '')))); 
							echo '<option value="'.$menuvalue.'" '.$selected.'>'.ucfirst($menuvalue).'</option>';
						}
					}
					echo '</select></label>
					<input type="submit" name="submit_userchanges" value="Save">
				</form>';
			}
			file_put_contents('conf/.userlist', $newuserlist);
			if (isset($_POST['submit_userchanges'])) {
				echo '<p class="messagebox success visible">You updated the user</p>';
			}
		echo '<!-- end user_management_container --></div>
	</div>';
} else {
	echo '<p class="messagebox error visible">You do not have access to this page</p>';
}

echo '</div>';
?>