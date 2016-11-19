<?php
$getconfigvars = file_exists('conf/config.php') ? file('conf/config.php', FILE_IGNORE_NEW_LINES) : '';
$lines_in_configfile = file_exists('conf/config.php') ? count(file('conf/config.php')) : '';
$currentvars = '';
$list_of_setupnames = ['websitename','dbhost','dbport','dbname','dbusername','dbpassword','prefix','allow_public','allow_userlist','use_login','show_quotes','use_db','debug','rootfolder','main_support_email','unique_key'];

echo '<div class="container">
	<h2>Control panel</h2>
		<p class="messagebox visible info">Here you will find all the modifiable settings for the page. The settings you change here will be updated in the config-file. You might need to log out and back in for the changes to take effect.</p>
	<div class="content">';

echo '<h2>Setup the config-file</h2>
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
								break;
							case 'dbhost':
								$label = 'Database Hostname';
								$desc  = 'Enter the hostname (or IP) for your database';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;
								break;
							case 'dbport':
								$label = 'Database Port';
								$desc  = 'If your database is set up with a specific port, enter it here';
								$required = '';
								$content = $last;
								$checkbox = false;
								break;
							case 'dbname':
								$label = 'Database Name';
								$desc  = 'Enter your database name. If you don\'t know, you can try using \'localhost\'';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;								
								break;
							case 'dbusername':
								$label = 'Database Username';
								$desc  = 'Enter the username for your database';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;								
								break;
							case 'dbpassword':
								$label = 'Database Password';
								$desc  = 'Enter the password for your database';
								$required = '';
								$content = $last;
								$checkbox = false;								
								break;
							case 'prefix':
								$label = 'Database table-prefix';
								$desc  = 'Enter a prefix for the database tables';
								$required = '';
								$content = $last;
								$checkbox = false;								
								break;
							case 'allow_public':
								$label = 'Allow public access';
								$desc = 'Allow for non logged in users to upload and view public files';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								break;	
							case 'allow_userlist':
								$label = 'Allow showing userlist';
								$desc = 'Allow for showing userlist of users who hasn\'t turned this off in their settings';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								break;		
							case 'use_login':
								$label = 'Use login/registreing';
								$desc = 'Allow for users to register and log in';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								break;	
							case 'debug':
								$label = 'Show debug-messages';
								$desc = 'Shows debug messages on different pages, and logs to log-folder. Should not be used on production site';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								break;	
							case 'show_quotes':
								$label = 'Show quotes on Gallery-page';
								$desc = 'Shows short quotes by famous people on the Gallery page';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								break;	
							case 'use_db':
								$label = 'Use database';
								$desc = 'Shows setup-information for database, and allows for database backend';
								$required = 'required="required"';
								$content = $last;
								$checkbox = true;
								break;
							case 'rootfolder':
								$label = 'Installation folder on webhost';
								$desc  = 'If this is the root folder, leave it as /, if not, enter the folder in the following format: /installfolder/';
								$required = 'required="required"';
								$content = !empty($last) ? $last : '/';
								$checkbox = false;	
								break;
							case 'main_support_email':
								$label = 'Support Email';
								$desc  = 'Enter an email address for any support questions or similar - this is basically the founder account';
								$required = 'required="required"';
								$content = $last;
								$checkbox = false;	
								break;
							case 'unique_key':
								$label = 'Unique encryption key';
								$desc  = 'Enter a 16character or more random selection of keys. This is not something you need to remember.';
								$required = 'required="required"';
								$randomkey = generateRandomString(true,true,true,'',20);
								$content = !empty($last) ? $last : $randomkey;
								$checkbox = false;									
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
echo '		<p>
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
	</div>
</div>';

?>