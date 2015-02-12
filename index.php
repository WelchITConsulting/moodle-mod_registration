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
 * Filename : index
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 24 Jan 2015
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/registration/locallib.php');

$id = required_param('id', PARAM_INT);
$PAGE->set_url('/mod/registration/index.oho', array('id' => $id));
if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('incorrectcourseid', 'registration');
}
$coursecontext = context_course::instance($id);
require_login($course->id);
$PAGE->set_pagelayout('incourse');
add_to_log($course->id, 'registration', 'view all', 'index.php?id=' . $course->id, '');

// Output the header
$strregistrations = get_string('modulenameplural', 'registration');
$PAGE->navbar->add($strregistrations);
$PAGE->set_title($course->shortname . ': ' . $strregistrations);
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();

// Get the appropriate data
if (!$registrations = get_all_instances_in_course('registration', $course)) {
    notice(get_string('thereareno', 'moodle', $strregistrations), '../../course/view.php?id=' . $course->id);
    die();
}

// Check if we need the closing date header
$showclosingheader = false;
foreach($registrations as $registration) {
    if ($registration->closedate > $registration->opendate) {
        $showclosingheader = true;
        break;
    }
}

$headings = array(get_string('name'));
$align = array('left');

if ($showclosingheader) {
    array_push($headings, get_string('registationopens', 'registration'));
    array_push($align, 'left');
    array_push($headings, get_string('registationcloses', 'registration'));
    array_push($align, 'left');
}
array_unshift($headings, get_string('sectionname', 'format_' . $course->format));
array_unshift($align, 'left');
$showing = '';
if (  has_capability('mod/registration:viewsingleresponse', $coursecontext)) {
    array_push($headings, get_string('responses', 'registration'));
    array_push($align, 'center');
    $showing = 'stats';
    array_push($headings, get_string('realm', 'registration'));
    array_push($align, 'left');
} elseif (  has_capability('mod/registration:submit', $coursecontext)) {
    array_push($headings, get_string('status'));
    array_push($align, 'left');
    $showing = 'reponses';
}

$table = new html_table();
$table->head = $headings;
$table->align = $align;

// Populate the table with the list of instances
$currentsection = '';
$expiredevents = array();
$liveevents = array();
foreach($registrations as $registration) {
    $cmid = $registration->coursemodule;
    $data = array();
    if ($registration->endtime > time()) {
        // Compile a list of the expired events



        $expiredevents[] = $data;
    } else {
        // Compile a list of the active events
        $strsection = '';
        if ($registration->section != $currentsection) {
            $strsection = get_section_name($course, $registration->section);
            $currentsection = $registration->section;
        }
        $data[] = $strsection;
        // Show normal if the mod is visible
        $class = '';
        if (!$registration->visible) {
            $class = ' class="dimmed"';
        }
        $data[] = '<a href="view.php?id=' . $cmid . '"' . $class .'>' . $registration->name . '</a>';
        // Close date
        if ($registration->closedate > $registration->opendate) {
            $data[] = userdate($registration->opendate);
            $data[] = userdate($registration->closedate);
        } elseif ($showclosingheader) {
            $data[] = '';
            $data[] = '';
        }
        if ($showing == 'responses') {
            $status = '';
            if ($responses = registration_get_user_responses($registration->id, $USER->id, $complete = false)) {
                foreach($responses as $response) {
//                    if ($response)
                }
            }
        } elseif ($showing == 'stats') {
//            if ()
        }
        $liveevents[] = $data;
    }
}
$table->data = array_merge($expiredevents, $liveevents);
echo html_writer::table($table);

// Close the page
echo $OUTPUT->footer();
