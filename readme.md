[Uploadr.at](http://uploadr.at)
===================================

** A database-less gallery-app, where you can select what files and filetypes you want to allow for upload. You can add users, and even though its's not database-driven, you can use database as a backend, for instance to create and manage users **

This is a work in progress, there will be updates coming fairly often in the next few weeks

Installation:
-------------
* Download the repository (or clone it directly)
* Make sure you're using **PHP > 5.3**, and that you have **mod_xsendfile activated on the server**
* As soon as all the files are on your server, that's it - navigate to the folder where you copied the files, and start using it
* You will need to register a new user in the beginning, and change the user in [.userlist](conf/.userlist) to admin
* The $show_quotes is set to **1** (on) as default - change this to **0** (off) if you don't want to show quotes on the gallery-page

* Currently there is no setup, so the only way to create a new admin-user is to use the [register.php](register.php) and register a new user, and then change that user to admin (change the **user** to **admin**) in the [.userlist](conf/.userlist)

* Remove the .gitignore and .gitattributes-files (they won't do any harm, but they're not needed)

If you want to add or remove allowed files, do that in the [.allowed_mimetypes](conf/.allowed_mimetypes) - you can find the correct MIME-types here: [The Complete List of MIME-types](https://www.sitepoint.com/web-foundations/mime-types-complete-list/)
You can do this from within the admin-panel (it reads and writes the files as is, so if you muck them up, you might have to resort to very colorful language while you search for backups)