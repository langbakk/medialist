Uploadr.io
==========

** A database-less gallery-app, where you can select what files and filetypes you want to allow for upload. You can add users, and even though its's not database-driven, you can use database as a backend, for instance to create and manage users **

Installation:
-------------
* Download the repository (or clone it directly)
* As soon as all the files are on your server, that's it - navigate to the folder where you copied the files, and start using it
* You might want to change the user(s) in the [config.php](conf/config.php) $user_array[]
* If you delete the one that is currently there without adding any new users, you will have no logins to the page - that doesn't matter, though, you can still use it as you want, but it might be useful to turn off the $use_login-setting in the [config.php](conf/config.php) (set it to false)
* The $show_quotes is set to true (on) as default - change this to false (off) if you don't want to show quotes on the gallery-page

* Remove the .gitignore and .gitattributes-files (they won't do any harm, but they're not needed)

If you want to add or remove allowed files, do that in the [.allowed_extensions](conf/.allowed_extensions) file. Add one extension per line, as those currently in the file. Remember that you also have to add the MIME-type to [.allowed_mimetypes](conf/.allowed_mimetypes) - you can find the correct MIME-types here: [The Complete List of MIME-types](https://www.sitepoint.com/web-foundations/mime-types-complete-list/)