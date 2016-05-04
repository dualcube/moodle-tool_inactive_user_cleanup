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
 * From for Inactive user cleanup email setting 
 *
 * @package    tool
 * @subpackage Inactive User cleanup
 * @copyright  2014 dualcube {@link http://dualcube.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');

class tool_inactive_user_cleanup_config_form extends moodleform {
    public function definition () {
        $mform = $this->_form;
        $mform->addElement('header', 'configheader', get_string('setting', 'tool_inactive_user_cleanup'));
        $mform->addElement('text', 'config_daysofinactivity', get_string('daysofinactivity', 'tool_inactive_user_cleanup'));
        $mform->addElement('text', 'config_daysbeforedeletion', get_string('daysbeforedeletion', 'tool_inactive_user_cleanup'));
        $mform->setDefault('config_daysofinactivity', '365');
        $mform->setType('config_daysofinactivity', PARAM_TEXT);
        $mform->setDefault('config_daysbeforedeletion', '10');
        $mform->setType('config_daysbeforedeletion', PARAM_TEXT);
        $mform->addElement('header', 'config_headeremail', get_string('emailsetting', 'tool_inactive_user_cleanup'));
        $mform->addElement('text', 'config_subjectemail', get_string('emailsubject', 'tool_inactive_user_cleanup'));
        $editoroptions = array('trusttext' => true, 'subdirs' => true, 'maxfiles' => 1,
        'maxbytes' => 1024);
        $mform->addElement('editor', 'config_bodyemail', get_string('emailbody', 'tool_inactive_user_cleanup'), $editoroptions);
        $mform->setType('config_subjectemail', PARAM_TEXT);
        $mform->setDefault('config_subjectemail', 'subject');
        $mform->setType('config_bodyemail', PARAM_RAW);
        $mform->setDefault('config_bodyemail', 'body');
        $this->add_action_buttons();
    }
}
