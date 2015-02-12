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
 * Filename : report
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 12 Feb 2015
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/registration/locallib.php');

$instance = optional_param('instance', false, PARAM_INT);
$action   = optional_param('action', 'all', PARAM_ALPHA);


if ($instance === false) {
    if (!empty($SESSION->instance)) {
        $instance = $SESSION->instance;
    } else {
        print_error('requiredparameter', 'registration');
    }
}
$SESSION->instance = $instance;

if (!$registration = $DB->get_record('registration', array('id' => $instance))) {
    print_error('incorrectregistration', 'registration');
}
if (!$course = $DB->get_record('course', array('id' => $registration->course))) {
    print_error('coursemisconf');
}
if (!$cm = get_coursemodule_from_instance('registration', $registration->id, $course->id)) {
    print_error('invalidcoursemodule');
}
require_course_login($course, true, $cm);






$url = new moodle_url($CFG->wwwroot . '/mod/registration/report.php');
if ($instance) {
    $url->param('instance', $instance);
}
if ($action) {
    $url->param('action', $action);
} else {
    $url->param('action', 'all');
}




$PAGE->set_url($url);
$PAGE->set_context($context);

// Tab setup
if (!isset($SESSION->registration)) {
    $SESSION->registration = new stdClass();
}
$SESSION->registration->current_tab = 'allreport';
