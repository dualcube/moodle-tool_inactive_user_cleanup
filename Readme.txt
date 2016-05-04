
This plugin deletes inactive user accounts. This cleanup process runs with Moodle cron job.

Admin Tool Inactive User Cleanup
Overview
This plugin deletes inactive user accounts. This cleanup process runs with Moodle cron job.


In the first step admin user of the site setup days of inactivity and drafting notification mail for all users from the Site administration > Reports > Inactive User Cleanup
. If an inactive user is found he/she gets a notification mail. 

In second step if the user still has not accessed the moodle site within the time span which is mentioned in the notification mail. 
Then the deletion process starts. The particular inactive user account entry is removed with next run of this cleanup process which is automatically or manually run by cron process.
Using


Some setting is require for this cleanup process.

•	Setting Panel
Days of Inactivity is set by the admin user.
Days Before Deletion is set with zero when admin just wants to notify the inactive user for access the site i.e. in first step. 
After that when user wants to run cleanup process then Days Before Deletion will set by the admin user.

•	Email setting

Admin user must set the subject and body text of the email.
Cron process
Admin user run cron job manually from http://<domainname>/<moodlename>/admin/cron.php
Uninstall
Admin can uninstall this admin tool from Site administration > Plugins >  Admin tools > Manage Admin Tools 



To install, place all files in /admin/tool/inactive_user_cleanup and visit /admin/index.php in your browser.

This block is written by Dualcube <admin@dualcube.com>.