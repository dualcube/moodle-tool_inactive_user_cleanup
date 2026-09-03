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
        mtrace(get_string('taskstart', 'tool_inactive_user_cleanup'));

        $inactivity = get_config('tool_inactive_user_cleanup', 'daysofinactivity');
        if ($inactivity == 0) {
            mtrace(get_string('invalaliddayofinactivity', 'tool_inactive_user_cleanup'));
            return;
        }

        $beforedelete = get_config('tool_inactive_user_cleanup', 'daysbeforedeletion');
        $subject = get_config('tool_inactive_user_cleanup', 'emailsubject');
        $emailbody = get_config('tool_inactive_user_cleanup', 'emailbody');
        $messagetext = html_to_text($emailbody);
        $includeneverloggedin = !empty(get_config('tool_inactive_user_cleanup', 'includeneverloggedin'));
        $courseid = (int) get_config('tool_inactive_user_cleanup', 'restrictcourseid');
        $excludecohortid = (int) get_config('tool_inactive_user_cleanup', 'excludecohortid');
        $excludedroles = $this->get_excluded_role_ids();

        $excludeduserids = $excludecohortid ? $this->get_cohort_member_ids($excludecohortid) : [];

        foreach ($this->get_candidate_users($courseid) as $user) {
            if ($this->is_excluded_user($user, $excludeduserids, $excludedroles)) {
                continue;
            }

            $lastaccess = $this->resolve_last_access($user, $courseid, $includeneverloggedin);
            if ($lastaccess === null) {
                continue;
            }

            $this->notify_inactive_user($user, $lastaccess, $inactivity, $subject, $messagetext, $emailbody);
            if ($beforedelete != 0) {
                $this->delete_notified_user($user, $lastaccess, $beforedelete);
            }
        }

        mtrace(get_string('taskend', 'tool_inactive_user_cleanup'));
    }

    /**
     * Check whether a user must be skipped entirely: guests, site admins, and configured cohort/role exclusions.
     *
     * @param \stdClass $user
     * @param int[] $excludeduserids user ids belonging to the excluded cohort
     * @param int[] $excludedroles role ids that exclude a user if held anywhere
     * @return bool
     */
    private function is_excluded_user(\stdClass $user, array $excludeduserids, array $excludedroles): bool {
        if (isguestuser($user) || is_siteadmin($user) || in_array($user->id, $excludeduserids)) {
            return true;
        }
        return $excludedroles && $this->has_any_role($user->id, $excludedroles);
    }

    /**
     * Resolve the timestamp to measure a user's inactivity from, or null if they should be skipped.
     *
     * @param \stdClass $user
     * @param int $courseid 0 for site-wide last access
     * @param bool $includeneverloggedin whether to fall back to account creation date
     * @return int|null
     */
    private function resolve_last_access(\stdClass $user, int $courseid, bool $includeneverloggedin): ?int {
        $lastaccess = $this->get_user_last_access($user, $courseid);
        if ($lastaccess != 0) {
            return $lastaccess;
        }
        return $includeneverloggedin ? (int) $user->timecreated : null;
    }

    /**
     * Get the users to consider for cleanup, optionally restricted to a single course's enrolment.
     *
     * @param int $courseid 0 to consider every user on the site, otherwise a course id
     * @return \stdClass[]
     */
    private function get_candidate_users(int $courseid): array {
        global $DB;
        if (!$courseid) {
            return $DB->get_records('user', ['deleted' => 0]);
        }
        $context = \context_course::instance($courseid, IGNORE_MISSING);
        if (!$context) {
            return [];
        }
        return get_enrolled_users($context, '', 0, 'u.*');
    }

    /**
     * Get the last access time to use for a user: site-wide, or within a specific course when restricted.
     *
     * @param \stdClass $user
     * @param int $courseid 0 for site-wide last access
     * @return int
     */
    private function get_user_last_access(\stdClass $user, int $courseid): int {
        global $DB;
        if (!$courseid) {
            return (int) $user->lastaccess;
        }
        $timeaccess = $DB->get_field('user_lastaccess', 'timeaccess', ['userid' => $user->id, 'courseid' => $courseid]);
        return (int) $timeaccess;
    }

    /**
     * Get the ids of users belonging to a cohort.
     *
     * @param int $cohortid
     * @return int[]
     */
    private function get_cohort_member_ids(int $cohortid): array {
        global $DB;
        return $DB->get_fieldset_select('cohort_members', 'userid', 'cohortid = :cohortid', ['cohortid' => $cohortid]);
    }

    /**
     * Get the role ids configured to be excluded from cleanup.
     *
     * @return int[]
     */
    private function get_excluded_role_ids(): array {
        $stored = get_config('tool_inactive_user_cleanup', 'excludedroles');
        if (empty($stored)) {
            return [];
        }
        return array_filter(array_map('intval', explode(',', $stored)));
    }

    /**
     * Check whether a user holds any of the given roles, in any context.
     *
     * @param int $userid
     * @param int[] $roleids
     * @return bool
     */
    private function has_any_role(int $userid, array $roleids): bool {
        global $DB;
        [$rolesql, $params] = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
        $params['userid'] = $userid;
        $sql = "userid = :userid AND roleid $rolesql";
        return $DB->record_exists_select('role_assignments', $sql, $params);
    }

    /**
     * Notify an inactive user and record that they were notified, unless already notified.
     *
     * @param \stdClass $user
     * @param int $lastaccess the timestamp to measure inactivity from
     * @param int $inactivity days of inactivity before a notification is due
     * @param string $subject
     * @param string $messagetext
     * @param string $messagehtml
     */
    private function notify_inactive_user($user, $lastaccess, $inactivity, $subject, $messagetext, $messagehtml) {
        global $DB;
        $inactivedays = round((time() - $lastaccess) / 60 / 60 / 24);
        if ($inactivedays <= $inactivity) {
            return;
        }
        if ($DB->get_record('tool_inactive_user_cleanup', ['userid' => $user->id])) {
            return;
        }
        if (!$this->send_notice($user, $subject, $messagetext, $messagehtml)) {
            return;
        }

        mtrace(get_string('userid', 'tool_inactive_user_cleanup'));
        mtrace($user->id . '---' . $user->email);
        mtrace(get_string('userinactivtime', 'tool_inactive_user_cleanup') . $inactivedays);
        mtrace('');

        $record = new \stdClass();
        $record->userid = $user->id;
        $record->emailsent = 1;
        $record->date = time();
        $DB->insert_record('tool_inactive_user_cleanup', $record, false);
    }

    /**
     * Send the inactivity notice through the core messaging API, so site messaging settings and each
     * user's own notification preferences are respected rather than emailing them unconditionally.
     *
     * @param \stdClass $user
     * @param string $subject
     * @param string $messagetext
     * @param string $messagehtml
     * @return bool true if the message was accepted for sending
     */
    private function send_notice($user, $subject, $messagetext, $messagehtml) {
        $message = new \core\message\message();
        $message->component = 'tool_inactive_user_cleanup';
        $message->name = 'inactivenotice';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = $subject;
        $message->fullmessage = $messagetext;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = $messagehtml;
        $message->smallmessage = $subject;
        $message->notification = 1;

        return (bool) message_send($message);
    }

    /**
     * Delete a previously notified user once the notice period has elapsed, unless they have
     * logged back in since being notified, in which case the pending deletion is cancelled.
     *
     * @param \stdClass $user
     * @param int $lastaccess the timestamp to measure inactivity from
     * @param int $beforedelete days to wait after notification before deletion
     */
    private function delete_notified_user($user, $lastaccess, $beforedelete) {
        global $DB;
        $notice = $DB->get_record('tool_inactive_user_cleanup', ['userid' => $user->id]);
        if (!$notice) {
            return;
        }

        if ($lastaccess > $notice->date) {
            // The user has been active since being notified; cancel the pending deletion.
            $DB->delete_records('tool_inactive_user_cleanup', ['userid' => $user->id]);
            return;
        }

        $sincenotice = round((time() - $notice->date) / 60 / 60 / 24);
        if ($sincenotice <= $beforedelete) {
            return;
        }

        delete_user($user);
        mtrace(get_string('deleteduser', 'tool_inactive_user_cleanup') . $user->id);
        mtrace(get_string('detetsuccess', 'tool_inactive_user_cleanup'));
    }
}
