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
 * installation tool_inactive_user_cleanup tool caps.
 * @author Sandipa Mukherjee <sandipa@dualcube.com>
 * @copyright DUALCUBE {@link https://dualcube.com/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$tasks = array(
    array(
        'classname' => 'tool_inactive_user_cleanup\task\tool_inactive_user_cleanup_task',
        'blocking' => 0,
        'minute' => '59',
        'hour' => '23',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);