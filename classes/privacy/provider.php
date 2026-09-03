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
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_inactive_user_cleanup\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;

/**
 * Privacy Subsystem implementation for tool_inactive_user_cleanup.
 *
 * The tool_inactive_user_cleanup table records are per-user data with no course or
 * module association, so the relevant context for every method below is CONTEXT_USER.
 *
 * @copyright DualCube (https://dualcube.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describing data stored in database tables
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'tool_inactive_user_cleanup',
            [
                'userid' => 'privacy:metadata:tool_inactive_user_cleanup:userid',
                'emailsent' => 'privacy:metadata:tool_inactive_user_cleanup:emailsent',
                'date' => 'privacy:metadata:tool_inactive_user_cleanup:date',
            ],
            'privacy:metadata:tool_inactive_user_cleanup'
        );
        return $collection;
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $DB->delete_records('tool_inactive_user_cleanup', ['userid' => $context->instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $DB->delete_records('tool_inactive_user_cleanup', ['userid' => $userid]);
    }

    /**
     * Delete all data for all users in the specified userlist.
     *
     * @param approved_userlist $userlist The specific userlist to delete data for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        [$userinsql, $userinparams] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = $userinparams;
        $sql = "userid {$userinsql}";
        $DB->delete_records_select('tool_inactive_user_cleanup', $sql, $params);
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        if ($DB->record_exists('tool_inactive_user_cleanup', ['userid' => $userid])) {
            $contextlist->add_user_context($userid);
        }
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        $records = $DB->get_records('tool_inactive_user_cleanup', ['userid' => $userid]);
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_USER) {
                continue;
            }
            foreach ($records as $record) {
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'tool_inactive_user_cleanup')],
                    $record
                );
            }
        }
    }

    /**
     * Get the list of users who have data within a context.
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        if ($DB->record_exists('tool_inactive_user_cleanup', ['userid' => $context->instanceid])) {
            $userlist->add_user($context->instanceid);
        }
    }
}
