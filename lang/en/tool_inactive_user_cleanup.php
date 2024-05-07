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
 * Data provider.
 *
 * @package    tool_inactive_user_cleanup
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Inactive User Cleanup';
$string['setting'] = 'Setting Panel';
$string['daysofinactivity'] = 'Days Of Inactivity';
$string['daysbeforedeletion'] = 'Days Before Deletion';
$string['deletiondescription'] = 'Put "0" to disable the cleanup option';
$string['emailsetting'] = 'Email Setting';
$string['emailsubject'] = 'Subject';
$string['emailbody'] = 'Body';
$string['runcron'] = 'Run Cron Manually';
$string['invalaliddayofinactivity'] = 'tool_inactive_user_cleanup disabled for putting invalid value in "Days Of Inactivity", the value should be greater than "0"';
$string['taskstart'] = 'Hey, admin tool inactive user cleanup is running';
$string['taskend'] = 'tool_inactive_user_cleanup task finished';
$string['detetsuccess'] = 'User_Delete Success';
$string['deleteduser'] = 'Deleted user ';
$string['emailsent'] = 'Email sent';
$string['userinactivtime'] = 'User is inactive for past day ';
$string['userid'] = 'USER ID ';
$string['privacy:metadata:tool_inactive_user_cleanup'] = 'Information about the inactive users';
$string['privacy:metadata:tool_inactive_user_cleanup:userid'] = 'Ids of the inactive users';
$string['privacy:metadata:tool_inactive_user_cleanup:emailsent'] = 'Information about the sent email who are cleaned up';
$string['privacy:metadata:tool_inactive_user_cleanup:date'] = 'The date when the user will be cleaned';
