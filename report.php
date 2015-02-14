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
require_once($CFG->dirroot . '/mod/registration/registration.class.php');
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
$registration = new SmartBridgeRegistration($course, $cm, 0, $registration);


$context = context_module::instance($cm->id);

// Check the user has the Capabilities required to access the report
if (!has_capability('mod/registration:viewsingleresponse', $context) &&
        !$registration->capabilites->view) {
    print_error('nopermissions', 'moodle', $CFG->wwwroot . '/mod/registration/view.php?id=' . $cm-id);
}
//$registration->canviewallgroups = has_capability('moodle/site:accessallgroups', $context);
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

$sql = 'SELECT rs.id, u.firstname, u.lastname, r.name, r.shortname, '
     . 'rn.name AS name_reassigned, rs.notes, rs.status '
     . 'FROM {user} u INNER JOIN {role_assignments} ra ON ra.userid = u.id '
     . 'INNER JOIN {context} ct ON ct.id = ra.contextid '
     . 'INNER JOIN {course} c ON c.id = ct.instanceid '
     . 'INNER JOIN {role} r ON r.id = ra.roleid '
     . 'LEFT JOIN {role_names} rn ON rn.roleid = r.id '
     . 'LEFT JOIN {registration_submissions} rs ON u.id = rs.userid '
     . 'WHERE rs.registration = ? '
     . 'ORDER BY rs.id ASC, r.shortname ASC, u.lastname ASC, u.firstname ASC';

if (!$respondants = $DB->get_records_sql($sql, array($registration->id))) {
    $respondants = array();
}
$processedresp = array();
foreach ($respondants as $respondant) {
    $processedresp[$respondant->id] = array($respondant->firstname . ' ' . $respondant->lastname,
                                            (empty($respondant->name) ? $respondant->name_reassigned : $respondant->name),
                                            $respondant->notes,
                                            registration_get_status($respondant->status));
}

$table = new html_table();
$table->head = array(get_string('firstname') . ' / ' . get_string('lastname'), //'First name / Last name',
                     get_string('role'),//'Role',
                     'Notes',
                     'Status',
                     'Action');
$table->align = array('left', 'left', 'left', 'left');

foreach($processedresp as $key => $respondant) {
    $table->data[] = $respondant;
}



$stregistrations = get_string('modulenameplural', 'registration');
$PAGE->navbar->add($stregistrations);
$PAGE->set_title($course->shortname . ': ' . $stregistrations);
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header()
   . html_writer::table($table)
   . $OUTPUT->footer();





//$sql = 'SELECT r.id, r.registration, r.userid, r.notes, r.status, u.firstname, u.lastname '
//     . 'FROM {user} u '
//     . 'LEFT JOIN {registration_submissions} r '
//     . 'ON u.id = r.userid '
//     . 'WHERE registration=? '
//     . 'ORDER BY u.lastname ASC, u.firstname ASC';

//switch ($action) {
//
//    // All submissions sorted by
//    case 'all':
//
//        break;
//
//    case 'rall':
//
//        break;
//

//}

// Tab setup
//if (!isset($SESSION->registration)) {
//    $SESSION->registration = new stdClass();
//}
//$SESSION->registration->current_tab = 'allreport';

//$sql = 'SELECT r.id, r.registration, r.userid, r.notes, r.status, u.firstname, u.lastname '
//     . 'FROM {registration_submissions} r, {user} u '
//     . 'WHERE r.userid = u.id AND registration=? '
//     . 'ORDER BY id';
//if (!$allpartisipants = $DB->get_records_sql($sql, array($registration->id))) {
//    $allpartisipants = array();
//}
//$SESSION->registration->numallpartisipants = count($allpartisipants);
//$SESSION->registration->numselectedresps = $SESSION->registration->numallpartisipants;
//$castsql = $DB->sql_cast_char2int('r.userid');




//Sourse c ON e.courseid = c.idELECT u.id, c.id
//FROM mdl_user u
//INNER JOIN mdl_user_enrolments ue ON ue.userid = u.id
//INNER JOIN mdl_enrol e ON e.id = ue.enrolid
//INNER JOIN mdl_course c ON e.courseid = c.id
//and
//
//SELECT u.id, c.id
//FROM mdl_user u
//INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
//INNER JOIN mdl_context ct ON ct.id = ra.contextid
//INNER JOIN mdl_course c ON c.id = ct.instanceid
//INNER JOIN mdl_role r ON r.id = ra.roleid
//WHERE r.id = 5
