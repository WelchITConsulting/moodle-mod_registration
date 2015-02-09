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
require_once($CFG->dirroot . '/mod/registration/registration.class.php');

if (!isset($SESSION->registration)) {
    $SESSION->registration = new stdClass();
}
$SESSION->registration->current_tab = 'view';

$id = optional_param('id', 0, PARAM_INT);
$a = optional_param('a', 0, PARAM_INT);

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
    if (!($registration = $DB->get_record('registration', array('id' => $a)))) {
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
if (!empty($id)) {
    $url->param('id', $id);
} else {
    $url->param('a', $a);
}
$PAGE->set_url($url);
$PAGE->set_context($context);

$registration = new SmartBridgeRegistration($course, $cm, 0, $registration);

$PAGE->set_title(format_string($registration->name));
$PAGE->set_heading(format_string($course->fullname));

/*if ($registration->capabilities->manage) {
    echo $OUTPUT->header()
       . $OUTPUT->heading(format_text($registration->name));

    if ($registration->intro) {
        echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
    }
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
       . '<p>Display the admin console</p>';

} else*/

if ($registration->opendate > time()) {
    echo $OUTPUT->header()
       . $OUTPUT->heading(format_text($registration->name));

    if ($registration->intro) {
        echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
    }
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
       . html_writer::div(get_string('registrationnotopen', 'registration'), 'message');

} elseif ($registration->closedate < time()) {
    echo $OUTPUT->header()
       . $OUTPUT->heading(format_text($registration->name));

    if ($registration->intro) {
        echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
    }
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
       . html_writer::div(get_string('registrationclosed', 'registration'), 'message');

} elseif ($registration->is_active()) {
    echo $OUTPUT->header()
       . $OUTPUT->heading(format_text($registration->name));

    if ($registration->intro) {
        echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
    }
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
       . html_writer::div(get_string('eventnotactive', 'registration'), 'message');

} elseif (!$registration->submitted()) {

    // Display the form
    require_once($CFG->dirroot . '/mod/registration/interest_form.php');
    $mform = new registration_interest_form();
    $interest = new stdClass();
    $interest->id = $id;
    $interest->registration = $registration->id;
    $interest->userid = $USER->id;
    $interest->status = 1;

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot . '/mod/register/view.php?id=' . $id);

    } elseif ($data = $mform->get_data()) {
        unset($interest->id);
        $interest->notes = $data->notes;
        $interest->timecreated = time();
        $interest->timemodified = $interest->timecreated;

        $DB->insert_record('registration_submissions', $interest);
        redirect($CFG->wwwroot . '/mod/registration/view.php?id=' . $id);

    } else {
        echo $OUTPUT->header()
           . $OUTPUT->heading(format_text($registration->name));

        if ($registration->intro) {
            echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
        }
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

        $mform->set_data($interest);
        $mform->display();
    }

} else {
    echo $OUTPUT->header()
       . $OUTPUT->heading(format_text($registration->name));

    if ($registration->intro) {
        echo $OUTPUT->box(format_module_intro('registration', $registration, $cm->id), 'generalbox', 'intro');
    }
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
       . html_writer::div(get_string('submitted', 'registration'), 'message');
}
echo $OUTPUT->box_end()
   . $OUTPUT->footer();
