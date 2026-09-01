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
 * Defines the capabilities of admin to use admin tools
 *
 * @package    tool_inactive_user_cleanup
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */

function xmldb_enrol_stripepayment_upgrade($oldversion) {
  global $DB;
  $dbman = $DB->get_manager();
  if ($oldversion < 2026090101) {
    //add a new column notification_count
    $table = new xmldb_table('tool_inactive_user_cleanup');
     $field = new xmldb_field('notification_count', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
     if (!$dbman->field_exists($table, $field)) {
     $dbman->add_field($table, $field);
     
  }
upgrade_plugin_savepoint(true, 2026090101, 'tool', 'inactive_user_cleanup');
  }
   return true;

}