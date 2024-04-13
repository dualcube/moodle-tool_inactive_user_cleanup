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
 * @package   tool_inactive_user_cleanup
 * @author DualCube <admin@dualcube.com>
 * @copyright DualCube (https://dualcube.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_inactive_user_cleanup\privacy;
defined('MOODLE_INTERNAL') || die();
/*
 * Standard cron function
 */

use core_privacy\local\metadata\collection;
/**
 * Privacy Subsystem implementation for tool_inactive_user_cleanup.
 *
 * @copyright DualCube (https://dualcube.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_inactive_user_cleanup\privacy;
abstract class provider implements
        \core_privacy\local\metadata\provider,

        // The Enrolled Courses Block plugin contains user's enrolled courses.
        \core_privacy\local\request\plugin\provider,

        \core_privacy\local\request\core_userlist_provider {
     /**
      * Returns meta data about this system.
      *
      * @param collection $collection The initialised collection to add items to.
      * @return collection A listing of user data stored through this system.
      */
    public static function get_metadata(collection $collection) : collection {
        // echo("helooooooooooooooooooooooooo");
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
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $context->get_user()->id;
        $DB->delete_records('tool_inactive_user_cleanup', array('userid' => $userid));
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
        foreach ($contextlist->get_contexts() as $context) {
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

        $context = $userlist->get_context();
        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = $userinparams;
        $sql = "userid {$userinsql}";
        $DB->delete_records_select('tool_inactive_user_cleanup', $sql, $params);
    }

}
