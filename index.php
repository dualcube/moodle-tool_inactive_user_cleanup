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
 * Inactive User cleanup tool.
 *
 * @package    tool
 * @subpackage Inactive User cleanup
 * @copyright  2014 dualcube {@link http://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/inactive_user_cleanup/email_form.php');

require_login();
admin_externalpage_setup('toolinactive_user_cleanup');

echo $OUTPUT->header();

$emailform = new admin_email_form();

$emailform->display();
$fromdata = $emailform->get_data();

if ($emailform->is_submitted()) {
    set_config('daysbeforedeletion', $fromdata->config_daysbeforedeletion, 'tool_inactive_user_cleanup');
    set_config('daysofinactivity', $fromdata->config_daysofinactivity, 'tool_inactive_user_cleanup');
    set_config('emailsubject', $fromdata->config_subjectemail, 'tool_inactive_user_cleanup');
    set_config('emailbody', $fromdata->config_bodyemail['text'], 'tool_inactive_user_cleanup');
}

echo $OUTPUT->footer();
