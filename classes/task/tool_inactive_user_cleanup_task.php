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
 * @package    tool_inactive_user_cleanup
 * @copyright  2014 dualcube {@link https://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
/*
 * tool_inactive_user_cleanup is standard cron function
 */
namespace tool_inactive_user_cleanup\task;

/**
 * Scheduled task for Inactive user cleanup.
 *
 * @copyright  2014 dualcube {@link https://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_inactive_user_cleanup_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'tool_inactive_user_cleanup');
    }
    /**
     * Execute.
     */
    public function execute() {
        global $DB, $CFG;
        mtrace("Hey, admin tool inactive user cleanup is running");
        $beforedelete = get_config('tool_inactive_user_cleanup', 'daysbeforedeletion');
        $inactivity = get_config('tool_inactive_user_cleanup', 'daysofinactivity');
        if($inactivity>0){
            $subject = get_config('tool_inactive_user_cleanup', 'emailsubject');
            $body = get_config('tool_inactive_user_cleanup', 'emailbody');
            $users = $DB->get_records('user', array('deleted' => '0'));
            $messagetext = html_to_text($body);
            $mainadminuser = get_admin();
            foreach ($users as $usersdetails) {
                $minus = round((time() - $usersdetails->lastaccess) / 60 / 60 / 24);
                if ($minus > $inactivity) {
                    $ischeck = $DB->get_record('tool_inactive_user_cleanup', array('userid' => $usersdetails->id));
                    if (!$ischeck &&  $usersdetails->lastaccess != 0) {
                        $record = new \stdClass();
                        $record->userid = $usersdetails->id;
                        if (email_to_user($usersdetails, $mainadminuser, $subject, $messagetext)) {
                            mtrace('id');
                            mtrace($usersdetails->id. '---' .$usersdetails->email);
                            mtrace('minus'.$minus);
                            mtrace('email sent');
                            $record->emailsent = 1;
                            $record->date = time();
                            $lastinsertid = $DB->insert_record('tool_inactive_user_cleanup', $record, false);
                        }
                    }
                }
                if ($beforedelete != 0 &&  $usersdetails->lastaccess != 0) {
                    $deleteuserafternotify = $DB->get_record('tool_inactive_user_cleanup', array('userid' => $usersdetails->id));
                    if (!empty($deleteuserafternotify)) {
                        $mailssent = $deleteuserafternotify->date;
                        $diff = round((time() - $mailssent) / 60 / 60 / 24);
                        if ($diff > $beforedelete) {
                            if (!isguestuser($usersdetails->id)) {
                                delete_user($usersdetails);
                                mtrace('delete user' . $usersdetails->id);
                                mtrace('User_Delete Success');
                            }
                        }
                    }
                }
            }
        }else{
            mtrace('tool_inactive_user_cleanup disabled for putting invalid value in "Days Of Inactivity", the value should be greater than "0"');
        }
        

        mtrace("tool_inactive_user_cleanup task finished");
    }//end of function execute()
}// End of class
