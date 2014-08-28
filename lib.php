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
 * Inactive user cleanup library
 *
 * @package    tool
 * @subpackage inactive user cleanup
 * @copyright  2014 dualcube {@link http://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 defined('MOODLE_INTERNAL') || die;

/**
 * Standard cron function
 */
function tool_inactive_user_cleanup_cron() {
    global $DB, $CFG;
    mtrace("Hey, admin tool inactive user cleanup is running");
    $emailsetting = $DB->get_records('tool_inactive_user_cleanup');
    foreach ($emailsetting as $emailsettingdetails) {
        $inactivity = $emailsettingdetails->daysofinactivity;
        $beforedelete = $emailsettingdetails->daysbeforedeletion;
        $subject = $emailsettingdetails->emailsubject;
        $body = $emailsettingdetails->emailbody;
        mtrace($inactivity);
    }
    $users = $DB->get_records('user');
    foreach ($users as $usersdetails) {
        mtrace($subject);
        mtrace($body);
        mtrace($usersdetails->id);
        $messagetext = html_to_text($body);
        if (date("d-m-y", $usersdetails->lastlogin) - date("d-m-y") > $inactivity) {
            if ($mailresults = email_to_user($usersdetails, $users, $subject, $messagetext)) {
                mtrace('email sent');
            }
        }
        if ($beforedelete != 0) {
            $deleted = 1;
            $username = $usersdetails->email . '.' . time();
            $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $deleteemail = substr(str_shuffle($letters), 0, 32);
            if ((date("d-m-y", $usersdetails->lastlogin) - date("d-m-y")) >= ($inactivity + $beforedelete)) {
                $sql = 'update {user} set deleted = ?, username = ?, email = ? where id = ?';
                //$DB->execute($sql, array($deleted, $username, $deleteemail, $usersdetails->id));
                delete_user($user);
                mtrace('delete user' . $usersdetails->id);
            }
        }
    }
    return true;
}

