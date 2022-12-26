Admin Tool Inactive User Cleanup
================================

Installation
------------
Go to [ Site administration > Plugins(Plugins) > Install plugins ] and just upload or drag & drop downloaed ZIP file.
To install, place all downloaded files in /admin/tool/inactive_user_cleanup and visit /admin/index.php in your browser.
This block is written by DualCube<admin@dulacube.com>.

Overview
--------
This plugin deletes inactive user accounts after the days which admin decided on setting and send a mail to user for login before deletion date. This cleanup process runs with Moodle cron job.

Setup
-----
In the first step admin user of the site setup days of inactivity, which decided the users can get mail after that days of inactivity.(if it set to "0" the clean up process will be disable)
Next set "Days Before Deletion" which is the notice period for inactive user before deletion.(if it set to "0" then deletion will be disable)
Then drafting notification mail for all users from the Site administration > Reports > Inactive User Cleanup.
If an inactive user is found he/she gets the notification mail. 

Clean up
--------
After getting notification mail, if the user still has not accessed the moodle site,then the deletion process starts.
The particular inactive user account entry is removed with next run of this cleanup process.
This is automatically or manually run by cron process.

Setting Panel 
------------
[www.yoursitename/admin/tool/inactive_user_cleanup/index.php] / [Site administration > Reports(Reports) > Inactive User Cleanup
If setting is require for this cleanup process.
Days of Inactivity is set by the admin user.
Days Before Deletion is set with zero when admin just wants to notify the inactive user for access the site i.e. in first step. 
After that when user wants to run cleanup process then Days Before Deletion will set by the admin user.

Email setting
-------------
Admin user must set the subject and body text of the email. Which the notified inactive user can get the mail with correct words.

Cron process
------------
Admin user run cron job manually after set password for manual cron on [ Administration > Security > Site security settings > Cron password for remote access ] from https://site.example.com/admin/cron.php?password=opensesame (replace "opensesame" with your cron password)

Uninstall
---------
Admin can uninstall this admin tool from- Site administration > Plugins >  Admin tools > Manage Admin Tools [ Inactive User Cleanup ]




