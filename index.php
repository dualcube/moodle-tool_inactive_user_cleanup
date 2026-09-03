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
 * setting form display and set config variables
 *
 * @package    tool_inactive_user_cleanup
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use tool_inactive_user_cleanup\form\config_form;

require_login();

admin_externalpage_setup('toolinactive_user_cleanup');

$settingsform = new config_form();

if ($fromform = $settingsform->get_data()) {
    set_config('daysbeforedeletion', $fromform->config_daysbeforedeletion, 'tool_inactive_user_cleanup');
    set_config('daysofinactivity', $fromform->config_daysofinactivity, 'tool_inactive_user_cleanup');
    set_config('emailsubject', $fromform->config_subjectemail, 'tool_inactive_user_cleanup');
    set_config('emailbody', $fromform->config_bodyemail['text'], 'tool_inactive_user_cleanup');
    set_config('includeneverloggedin', $fromform->config_includeneverloggedin, 'tool_inactive_user_cleanup');
    set_config('restrictcourseid', $fromform->config_restrictcourseid, 'tool_inactive_user_cleanup');
    set_config('excludecohortid', $fromform->config_excludecohortid, 'tool_inactive_user_cleanup');
    set_config('excludedroles', implode(',', (array) ($fromform->config_excludedroles ?? [])), 'tool_inactive_user_cleanup');

    redirect(
        new moodle_url('/admin/tool/inactive_user_cleanup/index.php'),
        get_string('changessaved'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$configdata = get_config('tool_inactive_user_cleanup');
if (!empty($configdata)) {
    $data = new stdClass();
    $data->config_daysbeforedeletion = $configdata->daysbeforedeletion ?? 10;
    $data->config_daysofinactivity = $configdata->daysofinactivity ?? 365;
    $data->config_subjectemail = $configdata->emailsubject ?? '';
    $data->config_bodyemail['text'] = $configdata->emailbody ?? '';
    $data->config_includeneverloggedin = $configdata->includeneverloggedin ?? 0;
    $data->config_restrictcourseid = $configdata->restrictcourseid ?? 0;
    $data->config_excludecohortid = $configdata->excludecohortid ?? 0;
    $data->config_excludedroles = !empty($configdata->excludedroles) ? explode(',', $configdata->excludedroles) : [];
    $settingsform->set_data($data);
}

echo $OUTPUT->header();
$settingsform->display();
echo $OUTPUT->footer();
