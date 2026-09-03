<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_inactive_user_cleanup', language 'en'.
 *
 * @package    tool_inactive_user_cleanup
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Inactive User Cleanup';
$string['setting'] = 'Setting panel';
$string['daysofinactivity'] = 'Days of inactivity';
$string['daysbeforedeletion'] = 'Days before deletion';
$string['deletiondescription'] = 'Put "0" to disable the cleanup option';
$string['emailsetting'] = 'Email setting';
$string['emailsubject'] = 'Subject';
$string['emailbody'] = 'Body';
$string['runcron'] = 'Run cron manually';
$string['invalaliddayofinactivity'] = 'Inactive user cleanup disabled for putting invalid value in "Days of inactivity", the value should be greater than "0"';
$string['taskstart'] = 'Hey, admin inactive user cleanup is running';
$string['taskend'] = 'Inactive user cleanup task finished';
$string['detetsuccess'] = 'User delete success';
$string['deleteduser'] = 'Deleted user';
$string['emailsent'] = 'Email sent';
$string['userinactivtime'] = 'User is inactive for past day ';
$string['userid'] = 'user id';
$string['scopesetting'] = 'Scope';
$string['includeneverloggedin'] = 'Include users who have never logged in (based on account creation date)';
$string['restrictcourseid'] = 'Restrict to course';
$string['allcourses'] = 'All courses (site-wide)';
$string['excludecohortid'] = 'Exclude cohort';
$string['nocohort'] = 'None';
$string['excludedroles'] = 'Exclude users with roles';
$string['excludedroles_desc'] = 'Users holding any of the selected roles (for example Manager or Teacher) will never be notified or deleted.';
$string['messageprovider:inactivenotice'] = 'Inactive account notice';
$string['privacy:metadata:tool_inactive_user_cleanup'] = 'Information about the inactive users';
$string['privacy:metadata:tool_inactive_user_cleanup:userid'] = 'Ids of the inactive users';
$string['privacy:metadata:tool_inactive_user_cleanup:emailsent'] = 'Information about the sent email who are cleaned up';
$string['privacy:metadata:tool_inactive_user_cleanup:date'] = 'The date when the user will be cleaned';
