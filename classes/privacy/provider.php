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
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;

/**
 * Privacy Subsystem implementation for tool_inactive_user_cleanup.
 *
 * @copyright DualCube (https://dualcube.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,

    // The Enrolled Courses Block plugin contains user's enrolled courses.
    \core_privacy\local\request\plugin\provider,

    \core_privacy\local\metadata\null_provider,

    \core_privacy\local\request\core_userlist_provider {

     /**
      * Returns meta data about this system.
      *
      * @param collection $collection The initialised collection to add items to.
      * @return collection A listing of user data stored through this system.
      */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

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

        // When we process user deletions and expiries, we always delete from the user context.
        // As a result the cohort role assignments would be deleted, which has a knock-on effect with courses
        // as roles may change and data may be removed earlier than it should be.
        $allowedcontextlevels = [
            CONTEXT_SYSTEM,
            CONTEXT_COURSECAT,
        ];
        if (!in_array($context->contextlevel, $allowedcontextlevels)) {
            return;
        }
        $userid = $context->get_user()->id;
        $DB->delete_records('tool_inactive_user_cleanup', ['userid' => $userid]);
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
        foreach ($contextlist->get_contexts() as $context) {
            $userid = $context->get_user()->id;
            $DB->delete_records('tool_inactive_user_cleanup', ['userid' => $userid]);
        }
    }

    /**
     * Delete all data for all users in the specified userlist.
     *
     * @param approved_userlist $userlist The specific userlist to delete data for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
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
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {user} u ON u.id = :userid
             LEFT JOIN {tool_inactive_user_cleanup} iu ON iu.userid = u.id
                 WHERE c.contextlevel = :contextlevel
                       AND c.instanceid = u.id
                       AND u.id = :userid";
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'userid'       => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $subcontext = $contextlist->get_contexts();
        $data = $DB->get_records('tool_inactive_user_cleanup');
        foreach ($subcontext as $context) {
            writer::with_context($context)->export_data($subcontext, $data);
        }
    }

    /**
     * Get the list of users who have data within a context.
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        // Get the instance ID from the context.
        $instanceid = $context->instanceid;

        // Define the SQL parameters.
        $params = [
            'instanceid' => $instanceid,
        ];

        // Query to get users who authored forum discussions.
        $sql = "SELECT userid 
                  FROM {tool_inactive_user_cleanup} 
                 WHERE instanceid = :instanceid";

        // Add users from tool_inactive_user_cleanup table to the userlist.
        $userlist->add_from_sql('userid', $sql, $params);
    }
}
