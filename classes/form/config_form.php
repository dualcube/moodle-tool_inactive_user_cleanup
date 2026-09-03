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
 * tool_inactive_user_cleanup setting form
 *
 * @package    tool_inactive_user_cleanup
 * @copyright  DualCube (https://dualcube.com)
 * @author     DualCube <admin@dualcube.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_inactive_user_cleanup\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * settings form for tool_inactive_user_cleanup
 *
 * @copyright DualCube (https://dualcube.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_form extends \moodleform {
    /**
     * Definition.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'configheader', get_string('setting', 'tool_inactive_user_cleanup'));
        $mform->addElement('text', 'config_daysofinactivity', get_string('daysofinactivity', 'tool_inactive_user_cleanup'));
        $mform->addElement('text', 'config_daysbeforedeletion', get_string('daysbeforedeletion', 'tool_inactive_user_cleanup'));
        $mform->addElement('static', 'description', '', get_string('deletiondescription', 'tool_inactive_user_cleanup'));
        $mform->setDefault('config_daysofinactivity', '365');
        $mform->setType('config_daysofinactivity', PARAM_INT);
        $mform->setDefault('config_daysbeforedeletion', '10');
        $mform->setType('config_daysbeforedeletion', PARAM_INT);
        $mform->addElement('header', 'config_headeremail', get_string('emailsetting', 'tool_inactive_user_cleanup'));
        $mform->addElement('text', 'config_subjectemail', get_string('emailsubject', 'tool_inactive_user_cleanup'));
        $editoroptions = ['trusttext' => true, 'subdirs' => true, 'maxfiles' => 1,
        'maxbytes' => 1024];
        $mform->addElement(
            'editor',
            'config_bodyemail',
            get_string('emailbody', 'tool_inactive_user_cleanup'),
            null,
            $editoroptions
        );
        $mform->setType('config_subjectemail', PARAM_TEXT);
        $mform->setDefault('config_subjectemail', 'subject');
        $mform->setType('config_bodyemail', PARAM_RAW);
        $mform->setDefault('config_bodyemail', 'body');

        $mform->addElement('header', 'config_headerscope', get_string('scopesetting', 'tool_inactive_user_cleanup'));
        $mform->addElement(
            'advcheckbox',
            'config_includeneverloggedin',
            get_string('includeneverloggedin', 'tool_inactive_user_cleanup')
        );
        $mform->setType('config_includeneverloggedin', PARAM_INT);
        $mform->setDefault('config_includeneverloggedin', 0);
        $mform->addElement(
            'select',
            'config_restrictcourseid',
            get_string('restrictcourseid', 'tool_inactive_user_cleanup'),
            $this->get_course_options()
        );
        $mform->setType('config_restrictcourseid', PARAM_INT);
        $mform->setDefault('config_restrictcourseid', 0);
        $mform->addElement(
            'select',
            'config_excludecohortid',
            get_string('excludecohortid', 'tool_inactive_user_cleanup'),
            $this->get_cohort_options()
        );
        $mform->setType('config_excludecohortid', PARAM_INT);
        $mform->setDefault('config_excludecohortid', 0);
        $roleselect = $mform->addElement(
            'select',
            'config_excludedroles',
            get_string('excludedroles', 'tool_inactive_user_cleanup'),
            $this->get_role_options()
        );
        $roleselect->setMultiple(true);
        $mform->setType('config_excludedroles', PARAM_SEQUENCE);
        $mform->addElement('static', 'excludedrolesdesc', '', get_string('excludedroles_desc', 'tool_inactive_user_cleanup'));

        $this->add_action_buttons();
    }

    /**
     * Get the list of courses to offer in the "restrict to course" selector.
     *
     * @return array id => fullname, with 0 meaning site-wide
     */
    private function get_course_options(): array {
        global $DB;
        $options = [0 => get_string('allcourses', 'tool_inactive_user_cleanup')];
        $courses = $DB->get_records_select(
            'course',
            'id <> :siteid',
            ['siteid' => SITEID],
            'fullname ASC',
            'id, fullname'
        );
        foreach ($courses as $course) {
            $options[$course->id] = format_string($course->fullname);
        }
        return $options;
    }

    /**
     * Get the list of cohorts to offer in the "exclude cohort" selector.
     *
     * @return array id => name, with 0 meaning no cohort excluded
     */
    private function get_cohort_options(): array {
        global $DB;
        $options = [0 => get_string('nocohort', 'tool_inactive_user_cleanup')];
        $cohorts = $DB->get_records('cohort', null, 'name ASC', 'id, name');
        foreach ($cohorts as $cohort) {
            $options[$cohort->id] = format_string($cohort->name);
        }
        return $options;
    }

    /**
     * Get the list of roles to offer in the "exclude roles" selector.
     *
     * @return array id => localised name
     */
    private function get_role_options(): array {
        $roles = role_get_names(\context_system::instance());
        $options = [];
        foreach ($roles as $role) {
            $options[$role->id] = $role->localname;
        }
        return $options;
    }
}
