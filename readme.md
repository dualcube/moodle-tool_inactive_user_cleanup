# Admin Tool: Inactive User Cleanup

## Installation
1. Go to **Site administration > Plugins > Install plugins** and upload or drag & drop the downloaded ZIP file.
2. To install, place all downloaded files in `/admin/tool/inactive_user_cleanup` and visit `/admin/index.php` in your browser.
3. This block is developed by DualCube <admin@dualcube.com>.

## Overview
This plugin deletes inactive user accounts after a certain number of days set by the admin, and sends a mail to the user for login before the deletion date. This cleanup process runs with Moodle cron job.

## Setup
1. Set the number of days of inactivity in the first step, which determines when users receive an email after that many days of inactivity. (If set to "0", the cleanup process will be disabled)
2. Next, set the "Days Before Deletion," which is the notice period for inactive users before deletion. (If set to "0", then deletion will be disabled)
3. Draft notification emails for all users from **Site administration > Reports > Inactive User Cleanup**.
4. If an inactive user is found, they receive the notification email of their removal.

## Clean up
1. After receiving the notification email, if the user still has not accessed the Moodle site, the deletion process starts.
2. The particular inactive user account entry is removed with the next run of this cleanup process.
3. This is automatically or manually run by the cron process.

## Setting Panel
If you want to directly access, you can use this link: [www.yoursitename/admin/tool/inactive_user_cleanup/index.php]

Or follow this procedure: **Site administration > Reports > Inactive User Cleanup**

- If settings are required for this cleanup process.
- Days of Inactivity is set by the admin user.
- Days Before Deletion is set to zero when the admin just wants to notify the inactive user to access the site in the first step. After that, when the user wants to run the cleanup process, Days Before Deletion will be set by the admin user.

## Email Setting
- Admin user must set the subject and body text of the email, which the notified inactive user can receive the mail with correct words.

## Cron Process
- Admin user runs the cron job manually after setting the password for manual cron on **Administration > Security > Site security settings > Cron password for remote access** from [https://site.example.com/admin/cron.php?password=opensesame](https://site.example.com/admin/cron.php?password=opensesame) (replace "opensesame" with your cron password)

## Uninstall
- Admin can uninstall this admin tool from **Site administration > Plugins > Admin tools > Manage Admin Tools [Inactive User Cleanup]**
