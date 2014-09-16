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
/*
 * Standard cron function
 */
require_once($CFG->libdir.'/adminlib.php');
require_capability('moodle/user:delete', context_system::instance());
function tool_inactive_user_cleanup_cron() {
    global $DB, $CFG;
    mtrace("Hey, admin tool inactive user cleanup is running");
    $inactivity = get_config('tool_inactive_user_cleanup', 'daysbeforedeletion');
    $beforedelete = get_config('tool_inactive_user_cleanup', 'daysofinactivity');
    $subject = get_config('tool_inactive_user_cleanup', 'emailsubject');
    $body = get_config('tool_inactive_user_cleanup', 'emailbody');
    $users = $DB->get_records('user', array('deleted' => '0'));
    $messagetext = html_to_text($body);
    foreach ($users as $usersdetails) {
        if (date("d-m-y", $usersdetails->lastlogin) - date("d-m-y") > $inactivity) {
            if ($mailresults = email_to_user($usersdetails, $users, $subject, $messagetext)) {
                mtrace('id');
                mtrace($usersdetails->id);
                mtrace('inactivity');
                mtrace(date("d-m-y", $usersdetails->lastlogin) - date("d-m-y"));
                mtrace('email sent');
            }
        }
        if ($beforedelete != 0) {
            if ((date("d-m-y", $usersdetails->lastlogin) - date("d-m-y")) >= ($inactivity + $beforedelete)) {
                delete_user($usersdetails);
                mtrace('delete user' . $usersdetails->id);
            }
        }
    }
    return true;
}

