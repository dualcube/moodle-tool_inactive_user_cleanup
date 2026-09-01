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
 * The Inactive user cleanup
 *
 * @package    tool_inactive_user_cleanup
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_inactive_user_cleanup\task;
use core\message\message;
/**
 * Scheduled task for Inactive user cleanup.
 *
 * @copyright DualCube (https://dualcube.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        mtrace(get_string('taskstart', 'tool_inactive_user_cleanup'));
        $beforedelete = get_config('tool_inactive_user_cleanup', 'daysbeforedeletion');
        $inactivity = get_config('tool_inactive_user_cleanup', 'daysofinactivity');
        

        if ($inactivity == 0) {
            mtrace(get_string('invalaliddayofinactivity', 'tool_inactive_user_cleanup'));
            return;
        }
        $subject = get_config('tool_inactive_user_cleanup', 'emailsubject');
        $body = get_config('tool_inactive_user_cleanup', 'emailbody');
         
        $users = $DB->get_records('user', ['deleted' => '0']);
        $messagetext = html_to_text($body);
        $mainadminuser = get_admin();
        foreach ($users as $usersdetails) {
            if($usersdetails->lastaccess > 0){
                mtrace('not never');
            $minus = round((time() - $usersdetails->lastaccess) / 60 / 60 / 24);
            }else{
                mtrace('never'. $usersdetails->id);
                $minus = round((time() - $usersdetails->timecreated) / 60 / 60 / 24);
             
               
            }
            
            $ischeck = $DB->get_record('tool_inactive_user_cleanup', ['userid' => $usersdetails->id]);
            $ischeckroll = $DB->get_record('role_assignments', ['userid' => $usersdetails->id]);

            $record = new \stdClass();
            $record->userid = $usersdetails->id;
           
             mtrace('Here thr tell is email possible:' . $CFG->messaging);
            if ($minus > $inactivity && !$ischeck && $ischeckroll->roleid === '5'  && $usersdetails->username != 'guest' ) {
                if($CFG->messaging!=0){
                    email_to_user($usersdetails, $mainadminuser, $subject, $messagetext);
                }else{
                    mtrace('mail did not send');
            }
                
               //sending message notification
               $message = new \core\message\message();
            
            $message->component = 'tool_inactive_user_cleanup';
            $message->name = 'inactiveuserwarning';
            $message->userfrom = $mainadminuser;
            $message->userto = $usersdetails;
            $message->subject = $subject;
            $message->fullmessage = $messagetext;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = '';
            $message->smallmessage = 'Your acount is inactivated';
            $message->notification = 1;
           

            if (!message_send($message)) {
                debugging("Failed to send stripepayment enrolment notification to user: {$usersdetails->id}", DEBUG_DEVELOPER);
            }

                mtrace('mail is value: ' . $usersdetails->email);
               
               
                mtrace(get_string('userid', 'tool_inactive_user_cleanup'));
                mtrace($usersdetails->id. '---' .$usersdetails->email);
                mtrace(get_string('userinactivtime', 'tool_inactive_user_cleanup') . $minus);
                
                $record->emailsent = 1;
                $record->date = time();
                $DB->insert_record('tool_inactive_user_cleanup', $record, false);
            }
             
           
            if ($beforedelete != 0 &&  $usersdetails->lastaccess != 0) {
                $deleteuserafternotify = $DB->get_record('tool_inactive_user_cleanup', ['userid' => $usersdetails->id]);
                
                if($deleteuserafternotify) {
                    $beforedelete = get_config('tool_inactive_user_cleanup', 'daysbeforedeletion');
                    $mailssent = $deleteuserafternotify->date;
                    $last=$usersdetails->lastaccess;
                    //for who login after recive email
                    if($last > $mailssent){
                    $acssdiff=round(($last - $mailssent) / 60 / 60 / 24);
                     if($acssdiff<$beforedelete){
                        $DB->delete_records('tool_inactive_user_cleanup', ['userid' => $usersdetails->id]);
                        mtrace('User acces so user deleted from mdl_tool_inactive_user_cleanup table');
                        continue;
                     }

                    }
                    mtrace('deleted user i am'.$usersdetails->id);
                    
                      mtrace('date  is'.$mailssent);
                    $diff = round((time() - $mailssent) / 60 / 60 / 24);
                    
                    mtrace('diff  is'.$diff);
                    if (!empty($deleteuserafternotify) && $diff > $beforedelete && !isguestuser($usersdetails->id)) {
                        delete_user($usersdetails);
                        mtrace(get_string('deleteduser', 'tool_inactive_user_cleanup') . $usersdetails->id);
                        mtrace(get_string('detetsuccess', 'tool_inactive_user_cleanup'));
                    }
                }
            }
        }
        mtrace(get_string('taskend', 'tool_inactive_user_cleanup'));
    } // End of function execute()
}// End of class