<?php

$getconfigvars = file_exists('conf/config.php') ? file('conf/config.php', FILE_IGNORE_NEW_LINES) : '';
$lines_in_configfile = file_exists('conf/config.php') ? count(file('conf/config.php')) : '';
$currentvars = '';
$list_of_setupnames = ['websitename','dbhost','dbport','dbname','dbusername','dbpassword','prefix','allow_public','allow_userlist','use_login','show_quotes','use_db','debug','moderation_queue','rootfolder','main_support_email','unique_key'];
$list_of_settings = ['allow_public','allow_userlist','use_login','show_quotes','debug','moderation_queue'];
$menu_array = 	[0 => ['href'=>'#user_management_container','menutext'=>'User management'],
				 1 => ['href'=>'#change_settings_container','menutext'=>'Change settings'],
				 2 => ['href'=>'#setup_container','menutext'=>'Setup config file'],
				 3 => ['href'=>'#htaccess_container','menutext'=>'Modify .htaccess']
				];

if (isset($_POST['settingschange']) || isset($_POST['configcreation'])) {
	$_POST['allow_public'] = isset($_POST['allow_public']) ? $_POST['allow_public'] : 0;
	$_POST['allow_userlist'] = isset($_POST['allow_userlist']) ? $_POST['allow_userlist'] : 0;
	$_POST['use_login'] = isset($_POST['use_login']) ? $_POST['use_login'] : 0;
	$_POST['debug'] = isset($_POST['debug']) ? $_POST['debug'] : 0;
	$_POST['show_quotes'] = isset($_POST['show_quotes']) ? $_POST['show_quotes'] : 0;
	$_POST['use_db'] = isset($_POST['use_db']) ? $_POST['use_db'] : 0;
	$_POST['moderation_queue'] = isset($_POST['moderation_queue']) ? $_POST['moderation_queue'] : 0;

	$writevars = '';
	for ($i=0; $i < $lines_in_configfile; $i++) {
		if (strstr($getconfigvars[$i], '=')) {
			$first = strstr($getconfigvars[$i], '=', true);
			$last = strstr($getconfigvars[$i], '=');
			if (substr($first,0,1) == '$') {
				$name = str_replace('$','',rtrim($first));
				$writevar = isset($_POST[$name]) ? $_POST[$name] : '';
				if (isset($_POST[$name])) {
					$writevar = (((in_array($name,$list_of_settings) === true) && $writevar === 'on') ? 1 : (((in_array($name,$list_of_settings) === true) && ($writevar == '' || $writevar == 0)) ? 0 : $writevar));
					if ($writevar == 1 || $writevar == 0) {
						$getconfigvars[$i] = '$'.$name.' = '.$writevar.';';
					} else {
						$getconfigvars[$i] = '$'.$name.' = \''.$writevar.'\';';	
					}
				}
			}
		}
		// if (isset($_POST['configcreation'])) {
			$writevars .= $getconfigvars[$i]."\n";
			file_put_contents('conf/config.php', $writevars);
		// }
	}
}

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

echo '<div id="change_settings_container" class="admincontainer">
	<h2>Change settings</h2>
		<form method="post" id="settingsform" class="configform" action="#change_settings_container">';
		for ($i=0; $i < $lines_in_configfile; $i++) {
			if (strstr($getconfigvars[$i], '=')) {
				$first = strstr($getconfigvars[$i], '=', true);
				$remove_content = array('=',' ','\'',';');
				$replace_content = array('','','','');
				$last = str_replace($remove_content,$replace_content,strstr($getconfigvars[$i], '='));
				$settingsname = str_replace('$','',rtrim($first));
				if (in_array($settingsname,$list_of_settings)) {
					switch ($settingsname) {
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
							$label = 'Write log-files';
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
						case 'moderation_queue':
							$label = 'Use moderation queue';
							$desc = 'If this is on, every upload (apart from those by admins) are queued for moderation before posted on the page';
							$required = 'required="required"';
							$content = $last;
							$checkbox = true;
							$hidden = '';
							break;
					};
					$setvar = (isset($_POST['settingschange'])) ? $_POST[$settingsname] : '';
					if ($getconfigvars[$i] == '$'.$settingsname.' = \'\';') {
						$getconfigvars[$i] = '$'.$settingsname.' = \''.$setvar.'\';';
					}
					if (!file_exists('conf/config.php')) {
						if ($settignsname == 'inactive') {
							$content = '600';
						} elseif ($settingsname == 'rootfolder') {
							$content = '/';
						} else {
							$content = '';
						}
					}
echo '		<p class="'.$hidden.'">
						<label class="left'.(($checkbox == true) ? ' checkboxlabel' : '').'" for="'.$settingsname.'"><i class="tooltiphover fa fa-question-circle"><span data-tooltip="'.$desc.'"></span></i> '.$label.'</label>';
						if ($checkbox == true) {
							echo '<span class="slider-frame"><span id="'.$settingsname.'_slider" class="'.(($content == 1) ? 'on' : '').' slider-button slider_checkbox">'.(($content == 1) ? 'YES' : 'NO').'</span></span>';
							echo '<input class="slider_checkbox" type="checkbox" name="'.$settingsname.'" id="'.$settingsname.'" '.(($content == 1) ? 'checked' : '').'>';
						} else {
						echo '<input class="configinput" type="text" id="'.$name.'" name="'.$settingsname.'" value="'.$content.'" '.$required.' tabindex="'.$i.'">';
						}
						echo '<span class="infobox right"><span class="left"></span></span>
					</p>';
					}
				}
			}
			echo '<p class="buttoncontainer">
					<input class="success button" type="submit" id="settingschange" name="settingschange" value="Change settings" tabindex="'.($i + 1).'">
				</p>
			</form>
</div>
<div id="setup_container" class="admincontainer">
		<h2>Setup the config-file</h2>
			<form method="post" id="configform" class="configform" action="#setup_container">';

			for ($i=0; $i < $lines_in_configfile; $i++) {
				if (strstr($getconfigvars[$i], '=')) {
					$first = strstr($getconfigvars[$i], '=', true);
					$remove_content = array('=',' ','\'',';');
					$replace_content = array('','','','');
					$last = str_replace($remove_content,$replace_content,strstr($getconfigvars[$i], '='));
					$configname = str_replace('$','',rtrim($first));
					if (in_array($configname,$list_of_setupnames)) {
						switch ($configname) {
							case 'websitename':
								$label = 'Website name';
								$desc = 'Enter the name used for the website';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;
								$hidden = '';
								$class = '';
								break;
							case 'dbhost':
								$label = 'Database Hostname';
								$desc  = 'Enter the hostname (or IP) for your database';
								$required = ($use_db == 1) ? 'required="required"' : '';
								$content = $last;
								$checkbox = false;
								$hidden = ($use_db == 1) ? '' : 'hidden';
								$class = 'dbinput';
								break;
							case 'dbport':
								$label = 'Database Port';
								$desc  = 'If your database is set up with a specific port, enter it here';
								$required = '';
								$content = $last;
								$checkbox = false;
								$hidden = ($use_db == 1) ? '' : 'hidden';
								$class = 'dbinput';
								break;
							case 'dbname':
								$label = 'Database Name';
								$desc  = 'Enter your database name. If you don\'t know, you can try using \'localhost\'';
								$required = ($use_db == 1) ? 'required="required"' : '';
								$content = $last;
								$checkbox = false;								
								$hidden = ($use_db == 1) ? '' : 'hidden';								
								$class = 'dbinput';
								break;
							case 'dbusername':
								$label = 'Database Username';
								$desc  = 'Enter the username for your database';
								$required = ($use_db == 1) ? 'required="required"' : '';
								$content = $last;
								$checkbox = false;								
								$hidden = ($use_db == 1) ? '' : 'hidden';	
								$class = 'dbinput';															
								break;
							case 'dbpassword':
								$label = 'Database Password';
								$desc  = 'Enter the password for your database';
								$required = '';
								$content = $last;
								$checkbox = false;	
								$hidden = ($use_db == 1) ? '' : 'hidden';
								$class = 'dbinput';								
								break;
							case 'prefix':
								$label = 'Database table-prefix';
								$desc  = 'Enter a prefix for the database tables';
								$required = '';
								$content = $last;
								$checkbox = false;								
								$hidden = ($use_db == 1) ? '' : 'hidden';
								$class = 'dbinput';								
								break;
							case 'allow_public':
								$label = 'Allow public access';
								$desc = 'Allow for non logged in users to upload and view public files';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';
								$class = '';																
								break;	
							case 'allow_userlist':
								$label = 'Allow showing userlist';
								$desc = 'Allow for showing userlist of users who hasn\'t turned this off in their settings';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';								
								$class = '';								
								break;		
							case 'use_login':
								$label = 'Use login/registreing';
								$desc = 'Allow for users to register and log in';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';
								$class = '';																
								break;	
							case 'debug':
								$label = 'Write log-files';
								$desc = 'Shows debug messages on different pages, and logs to log-folder. Should not be used on production site';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';	
								$class = '';															
								break;	
							case 'show_quotes':
								$label = 'Show quotes on Gallery-page';
								$desc = 'Shows short quotes by famous people on the Gallery page';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';
								$class = '';																
								break;	
							case 'use_db':
								$label = 'Use database';
								$desc = 'Shows setup-information for database, and allows for database backend';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';	
								$class = '';															
								break;
							case 'moderation_queue':
								$label = 'Use moderation queue';
								$desc = 'If this is on, every upload (apart from those by admins) are queued for moderation before posted on the page';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								$hidden = '';
								$class = '';								
								break;								
							case 'rootfolder':
								$label = 'Installation folder on webhost';
								$desc  = 'If this is the root folder, leave it as /, if not, enter the folder in the following format: /installfolder/';
								$required = 'required="required"';
								$content = !empty($last) ? $last : '/';
								$checkbox = false;	
								$hidden = '';	
								$class = '';															
								break;
							case 'main_support_email':
								$label = 'Support Email';
								$desc  = 'Enter an email address for any support questions or similar - this is basically the founder account';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;	
								$hidden = '';
								$class = '';																
								break;
							case 'unique_key':
								$label = 'Unique encryption key';
								$desc  = 'Enter a 16character or more random selection of keys. This is not something you need to remember.';
								$required = 'required="required"';
								$randomkey = generateRandomString(true,true,true,'',20);
								$content = !empty($last) ? $last : $randomkey;
								$checkbox = false;	
								$hidden = '';	
								$class = '';																							
						};
						// $_POST['allow_public'] = isset($_POST['allow_public']) ? $_POST['allow_public'] : 0;
						// $_POST['allow_userlist'] = isset($_POST['allow_userlist']) ? $_POST['allow_userlist'] : 0;
						// $_POST['use_login'] = isset($_POST['use_login']) ? $_POST['use_login'] : 0;
						// $_POST['debug'] = isset($_POST['debug']) ? $_POST['debug'] : 0;
						// $_POST['show_quotes'] = isset($_POST['show_quotes']) ? $_POST['show_quotes'] : 0;
						// $_POST['use_db'] = isset($_POST['use_db']) ? $_POST['use_db'] : 0;
						// $_POST['moderation_queue'] = isset($_POST['moderation_queue']) ? $_POST['moderation_queue'] : 0;
					$setvar = (isset($_POST['configcreation'])) ? $_POST[$configname] : '';

					if ($getconfigvars[$i] == '$'.$configname.' = \'\';') {
						$getconfigvars[$i] = '$'.$configname.' = \''.$setvar.'\';';
					}
					if (!file_exists('conf/config.php')) {
						if ($configname == 'inactive') {
							$content = '600';
						} elseif ($configname == 'rootfolder') {
							$content = '/';
						} else {
							$content = '';
						}
					}
echo '		<p class="'.$hidden.' '.$class.'">
						<label class="left'.(($checkbox == true) ? ' checkboxlabel' : '').'" for="'.$configname.'"><i class="tooltiphover fa fa-question-circle"><span data-tooltip="'.$desc.'"></span></i> '.$label.'</label>';
						if ($checkbox == true) {
							echo '<span class="slider-frame"><span id="'.$configname.'_slider" class="'.(($content == 1) ? 'on' : '').' slider-button slider_checkbox">'.(($content == 1) ? 'YES' : 'NO').'</span></span>';
							echo '<input class="slider_checkbox" type="checkbox" name="'.$configname.'" id="'.$configname.'" '.(($content == 1) ? 'checked' : '').'>';

							// echo '<input class="configinput" type="checkbox" id="'.$configname.'" name="'.$configname.'" '.$required.' '.(($content == 'true') ? 'checked' : '').'>';
						} else {
						echo '<input class="configinput" type="text" id="'.$configname.'" name="'.$configname.'" value="'.$content.'" '.$required.' tabindex="'.$i.'">';
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
		<h2>User management</h2>
			<ul>';
			$usertypes = ['admin','user'];
			// $newuserlist = '#username // password // userrole // allow userlist link // diskspace-setting'."\r\n";
			$c = 0;
			foreach ($user_array as $uakey => $uavalue) {
				$c++;
				echo '<li><form method="post" action="'.$processpath.'update_userlist.php" class="removeuser">
				 	<label><i class="tooltiphover fa fa-remove"><span data-tooltip="Delete user"></span></i></label>
				 	</form>';
				echo '<form method="post" action="'.$processpath.'update_userlist.php" class="user_management_form">';
				$user = explode('//',$uavalue);
				// if (isset($_POST['submit_userchanges']) && ($_POST['username'] == trim($user[0]))) {
				// 	$newuserlist .= '';
				// } elseif ($c == count($user_array)) {
				// 	$newuserlist .= $uavalue;
				// } else  {
				// 	$newuserlist .= $uavalue."\r\n";	
				// }
				
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
				// var_dump($c % 6 == false);
				echo '<label '.((($c != 1) && (($c % 6) !== 0)) ? 'style="height: 2.5em;"' : '').' for="username_'.$formusername.'"><span class="labeltext '.((($c != 1) && (($c % 6) !== 0)) ? 'hidden' : '').'">Username<br></span><input id="username_'.$formusername.'" type="text" disabled value="'.$formusername.'"></label><input type="hidden" name="username" value="'.$formusername.'"><label '.((($c != 1) && (($c % 6) !== 0)) ? 'style="height: 2.5em;"' : '').' for="password_'.$formusername.'"><span class="labeltext '.((($c != 1) && (($c % 6) !== 0)) ? 'hidden' : '').'">Password<br></span><input id="password_'.$formusername.'" name="password" type="text" disabled placeholder="New password"></label><label '.((($c != 1) && (($c % 6) !== 0)) ? 'style="height: 2.5em;"' : '').' for="usertype_'.$formusername.'"><span class="labeltext '.((($c != 1) && (($c % 6) !== 0)) ? 'hidden' : '').'">Usertype<br></span><select id="usertype_'.$formusername.'" name="usertype" autocomplete="off">';
						foreach ($usertypes as $utkey => $utvalue) {
							$setvalue = (isset($_POST['usertype']) && ($_POST['username'] == $formusername)) ? $_POST['usertype'] : $utvalue;
							$selected = ((isset($_POST['usertype']) && ($_POST['username'] == $formusername) && $_POST['usertype'] == $utvalue) ? 'selected' : (((!isset($_POST['usertype']) || ($_POST['username'] != $formusername)) && trim($user[2]) == $utvalue) ? 'selected' : '')); 
							echo '<option value="'.$utvalue.'" '.$selected.'>'.ucfirst($utvalue).'</option>';
						}
					echo '</select></label><label '.((($c != 1) && (($c % 6) !== 0)) ? 'style="height: 2.5em;"' : '').' for="userlistlink_'.$formusername.'"><span class="labeltext '.((($c != 1) && (($c % 6) !== 0)) ? 'hidden' : '').'">Userlist<br></span><input id="userlistlink_'.$formusername.'" type="checkbox" name="userlistlink" '.(($user[3] == 1) ? 'checked' : '').'></label><label '.((($c != 1) && (($c % 6) !== 0)) ? 'style="height: 2.5em;"' : '').' for="userdiskspace_'.$formusername.'"><span class="labeltext '.((($c != 1) && (($c % 6) !== 0)) ? 'hidden' : '').'">Disk space<br></span><input id="userdiskspace_'.$formusername.'" type="text" name="userdiskspace" value="'.(!empty($user[4]) ? $user[4] : $defaultsize).'"></label><label '.((($c != 1) && (($c % 6) !== 0)) ? 'style="height: 2.5em;"' : '').' for="userstartpage_'.$formusername.'"><span class="labeltext '.((($c != 1) && (($c % 6) !== 0)) ? 'hidden' : '').'">Preferred startpage<br></span><select id="userstartpage_'.$formusername.'" name="userstartpage" autocomplete="off">';
					foreach (Config::read('menu_array') as $menukey => $menuvalue) {
						if ($menuvalue != 'login' &&  $menuvalue != 'register' && $menuvalue != 'home') {
							$setvalue = (isset($_POST['userstartpage']) && ($_POST['username'] == $formusername)) ? $_POST['userstartpage'] : $_SESSION['userstartpage'];
							$selected = ((isset($_POST['userstartpage']) && ($_POST['username'] == $formusername) && $_POST['userstartpage'] == $menuvalue) ? 'selected' : (((!isset($_POST['userstartpage']) || ($_POST['username'] != $formusername)) && trim($user[5]) == $menuvalue) ? 'selected' : (((empty($user[5]) && $menuvalue == 'upload') ? 'selected' : '')))); 
							echo '<option value="'.$menuvalue.'" '.$selected.'>'.ucfirst($menuvalue).'</option>';
						}
					}
					echo '</select></label><input type="submit" name="submit_userchanges" value="Save" '.((($c != 1) && (($c % 6) !== 0)) ? 'style="margin-top: -2.5em;"' : '').'>
				</form></li>';
			}
			echo '</ul><button id="add_user" name="add_user">Add user</button>';
			// file_put_contents('conf/.userlist', $newuserlist);
			if (isset($_POST['submit_userchanges'])) {
				echo '<p class="messagebox success visible">You updated the user</p>';
			}
		echo '<!-- end user_management_container --></div>
		<div class="admincontainer" id="htaccess_container">
			<h2>Modify .htaccess-file</h2>
			<p class="messagebox warning visisble">You should not modify this unless you experience trouble with links or redirects, or other issues that might be considered problems with server-config (Apache)</p>';
			if (isset($_POST['submit_htaccessupdate'])) {
				rename($_SERVER['DOCUMENT_ROOT'].'/.htaccess',$_SERVER['DOCUMENT_ROOT'].'/.htaccess_old');
				file_put_contents($_SERVER['DOCUMENT_ROOT'].'/.htaccess',$_POST['htaccesscontent'],LOCK_EX);
				echo '<p class="messagebox success visible">You\'ve updated the .htaccess-file</p>';
			}
			echo '<form method="post" action="#htaccess_container" class="htaccessform">';
				$gethtaccess = (file_exists($_SERVER['DOCUMENT_ROOT'].'/.htaccess') ? file($_SERVER['DOCUMENT_ROOT'].'/.htaccess', FILE_IGNORE_NEW_LINES) : '');
				$lines_in_htaccess = (file_exists($_SERVER['DOCUMENT_ROOT'].'/.htaccess') ? count(file($_SERVER['DOCUMENT_ROOT'].'/.htaccess', FILE_IGNORE_NEW_LINES)) : '');
				$content = '';
				$height = $lines_in_htaccess * 1.3;
			    for ($i=0; $i < $lines_in_htaccess; $i++) {
					$content .= $gethtaccess[$i]."\r\n";
			    }
		echo '<label>Modify / change .htaccess</label><br>
		<textarea id="htaccesscontent" name="htaccesscontent" style="min-height: '.$height.'em;">'.$content.'</textarea>
		<input type="submit" name="submit_htaccessupdate" value="Save .htaccess"></form>
		</div>
	</div>';
} else {
	echo '<p class="messagebox error visible">You do not have access to this page</p>';
}

echo '</div>';
?>