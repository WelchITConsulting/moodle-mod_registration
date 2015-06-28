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
require_once($CFG->dirroot . '/mod/sbregistration/lib.php');
require_once($CFG->dirroot . '/mod/sbregistration/sbregistration.class.php');

if (!isset($SESSION->sbregistration)) {
    $SESSION->sbregistration = new stdClass();
}
$SESSION->sbregistration->current_tab = 'view';

$id = optional_param('id', 0, PARAM_INT);
$a = optional_param('a', 0, PARAM_INT);

if ($id) {
    if (!($cm = get_coursemodule_from_id('sbregistration', $id))) {
        print_error('invalidcoursemodule');
    }
    if (!($course = $DB->get_record('course', array('id' => $cm->course)))) {
        print_error('coursemisconf');
    }
    if (!($sbregistration = $DB->get_record('sbregistration', array('id' => $cm->instance)))) {
        print_error('invalidcoursemodule');
    }
} else {
    if (!($sbregistration = $DB->get_record('sbregistration', array('id' => $a)))) {
        print_error('invalidcoursemodule');
    }
    if (!($course = $DB->get_record('course', array('id' => $sbregistration->course)))) {
        print_error('coursemisconf');
    }
    if (!($cm = get_coursemodule_from_instance('sbregistration', $sbregistration->id, $course->id))) {
        print_error('invalidcoursemodule');
    }
}
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

$url = new moodle_url($CFG->wwwroot . '/mod/sbregistration/view.php');
if (!empty($id)) {
    $url->param('id', $id);
} else {
    $url->param('a', $a);
}
$PAGE->set_url($url);
$PAGE->set_context($context);

$sbregistration = new SmartBridgeRegistration($course, $cm, 0, $sbregistration);

$PAGE->set_title(format_string($sbregistration->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header()
   . $OUTPUT->heading(format_text($sbregistration->name));

if ($sbregistration->capabilities->manage) {

    echo $OUTPUT->box_start('generalbox boxalignright boxwidthwide');

    if ($sbregistration->has_submissions()) {
        $url = new moodle_url($CFG->wwwroot . '/mod/sbregistration/report.php',
                              array('instance' => $sbregistration->id));
        if (!empty($id)) {
            $url->param('id', $id);
        }
        echo html_writer::link($url, get_string('viewallresponses', 'sbregistration'))
           . html_writer::empty_tag('br');
        $url = new moodle_url($CFG->wwwroot . '/mod/sbregistration/print.php',
                              array('instance' => $sbregistration->id));
        if (!empty($id)) {
            $url->param('id', $id);
        }
        echo html_writer::link($url, get_string('printresponses', 'sbregistration'));
    }


    echo $OUTPUT->box_end();

//    if ($sbregistration->intro) {
//        echo $OUTPUT->box(format_module_intro('sbregistration', $sbregistration, $cm->id), 'generalbox', 'intro');
//    }
//    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
//
//    // Display link to the report
//    $responsesurl = new moodle_url($CFG->wwwroot . '/mod/sbregistration/report.php',
//                                   array('instance' => $sbregistration->id));
//    if (!empty($id)) {
//        $responsesurl->param('id', $id);
//    }
//    echo '<a href="' . $responsesurl->out() . '" class="viewalllink">'
//       . get_string('viewallresponses', 'sbregistration') . '</a>';
//
//} elseif ($sbregistration->opendate > time()) {
//    echo $OUTPUT->header()
//       . $OUTPUT->heading(format_text($sbregistration->name));
//
    if ($registration->opendate > 0) {
        $opendate = format_string($sbregistration->regstart);
    }
    if ($registration->closedate > 0) {
        $closedate = format_string($sbregistration->regend);
    }
    echo '<dl class="sbregistration-detail dl-horizontal"><dt>'
       . get_string('starttime', 'sbregistration')
       . '</dt><dd>'
       . format_string($sbregistration->eventstart)
       . '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;'
       . format_string($sbregistration->eventend)
       . '</dd><dt>'
       . get_string('period', 'sbregistration')
       . '</dt><dd>'
       . $opendate
       . (!empty($opendate) && !empty($closedate) ? '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;' : '')
       . $closedate
       . '</dd><dt>'
       . get_string('location', 'sbregistration')
       . '</dt><dd>'
       . format_string($sbregistration->location)
       . '</dd><dt>'
       . get_string('numberofplaces', 'sbregistration')
       . '</dt><dd>'
       . format_string($sbregistration->places)
       . '</dd></dl>';

    if ($sbregistration->intro) {
        echo $OUTPUT->box(format_module_intro('sbregistration', $sbregistration, $cm->id), 'generalbox', 'intro');
    }
//    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
//       . html_writer::div(get_string('sbregistrationnotopen', 'sbregistration'), 'message');
//
//} elseif ($sbregistration->closedate < time()) {
//    echo $OUTPUT->header()
//       . $OUTPUT->heading(format_text($sbregistration->name));
//
//    if ($sbregistration->intro) {
//        echo $OUTPUT->box(format_module_intro('sbregistration', $sbregistration, $cm->id), 'generalbox', 'intro');
//    }
//    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
//       . html_writer::div(get_string('sbregistrationclosed', 'sbregistration'), 'message');
//
//} elseif ($sbregistration->is_active()) {
//    echo $OUTPUT->header()
//       . $OUTPUT->heading(format_text($sbregistration->name));
//
//    if ($sbregistration->intro) {
//        echo $OUTPUT->box(format_module_intro('sbregistration', $sbregistration, $cm->id), 'generalbox', 'intro');
//    }
//    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
//       . html_writer::div(get_string('eventnotactive', 'sbregistration'), 'message');
//
//} elseif (!$sbregistration->submitted()) {
//
//    // Display the form
//    require_once($CFG->dirroot . '/mod/sbregistration/interest_form.php');
//    $mform = new sbregistration_interest_form();
//    $interest = new stdClass();
//    $interest->id = $id;
//    $interest->sbregistration = $sbregistration->id;
//    $interest->userid = $USER->id;
//    $interest->status = 1;
//
//    if ($mform->is_cancelled()) {
//        redirect($CFG->wwwroot . '/mod/register/view.php?id=' . $id);
//
//    } elseif ($data = $mform->get_data()) {
//        unset($interest->id);
//        $interest->notes = $data->notes;
//        $interest->timecreated = time();
//        $interest->timemodified = $interest->timecreated;
//
//        $DB->insert_record('sbregistration_submissions', $interest);
//        redirect($CFG->wwwroot . '/mod/sbregistration/view.php?id=' . $id);
//
//    } else {
//        echo $OUTPUT->header()
//           . $OUTPUT->heading(format_text($sbregistration->name));
//
//        if ($sbregistration->intro) {
//            echo $OUTPUT->box(format_module_intro('sbregistration', $sbregistration, $cm->id), 'generalbox', 'intro');
//        }
//        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
//
//        $mform->set_data($interest);
//        $mform->display();
//    }
//
//} else {
//    echo $OUTPUT->header()
//       . $OUTPUT->heading(format_text($sbregistration->name));
//
//    if ($sbregistration->intro) {
//        echo $OUTPUT->box(format_module_intro('sbregistration', $sbregistration, $cm->id), 'generalbox', 'intro');
//    }
//    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide')
//       . html_writer::div(get_string('submitted', 'sbregistration'), 'message')
//       . $OUTPUT->box_end();
}
//echo $OUTPUT->box_end()
echo $OUTPUT->footer();
