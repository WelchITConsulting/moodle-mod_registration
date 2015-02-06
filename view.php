<?php
/*
 * Copyright (C) 2015 Welch IT Consulting
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Filename : view
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 24 Jan 2015
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/registration/lib.php');

if (!isset($SESSION->registration)) {
    $SESSION->registration = new stdClass();
}
$SESSION->registration->current_tab = 'view';

$id = optional_param('id', null, PARAM_INT);
$a  = optional_param('a',  null, PARAM_INT);

if ($id) {
    if (!($cm = get_coursemodule_from_id('registration', $id))) {
        print_error('invalidcoursemodule');
    }
    if (!($course = $DB->get_record('course', array('id' => $cm->course)))) {
        print_error('coursemisconf');
    }
    if (!($registration = $DB->get_record('registration', array('id' => $cm->instance)))) {
        print_error('invalidcoursemodule');
    }
} else {
    if (!($registration = $DB->get_record('registration', array('id' => $id)))) {
        print_error('invalidcoursemodule');
    }
    if (!($course = $DB->get_record('course', array('id' => $registration->course)))) {
        print_error('coursemisconf');
    }
    if (!($cm = get_coursemodule_from_instance('registration', $registration->id, $course->id))) {
        print_error('invalidcoursemodule');
    }
}
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

$url = new moodle_url($CFG->wwwroot . '/mod/registration/view.php');
if (isset($id)) {
    $url->param('id', $id);
} else {
    $url->param('a', $a);
}
$PAGE->set_url($url);
$PAGE->set_context($url);
$PAGE->set_title(format_string($registration->name));
$page->set_heading(format_string($course->fullname));
echo $OUTPUT->header()
   . $OUTPUT->heading(format_text($registration->name));

if ($registration->intro) {
    echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
}
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
