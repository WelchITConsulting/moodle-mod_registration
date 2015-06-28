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
 * Filename : locallib
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
<<<<<<< HEAD
 * Created  : 25 Jan 2015
 */

require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->dirroot . '/calendar/lib.php');

// Max time before separate calendar events are created - 5 days
//define('REGISTRATION_MAX_EVENT_LENGTH', (5 * 24 * 60 * 60));

function sbregistration_load_capabilities($cmid)
{
    static $sbcb;

    if (empty($sbcb)) {
        $context = sbregistration_get_context($cmid);
        $sbcb = new object();
        $sbcb->view                 = has_capability('mod/sbregistration:view', $context);
        $sbcb->viewsingleresponse   = has_capability('mod/sbregistration:viewsingleresponse', $context);
        $sbcb->deleteresponses      = has_capability('mod/sbregistration:deleteresponses', $context);
        $sbcb->downloadresponses    = has_capability('mod/sbregistration:downloadresponses', $context);
        $sbcb->submit               = has_capability('mod/sbregistration:submit', $context);
        $sbcb->manage               = has_capability('mod/sbregistration:manage', $context);
    }
    return $sbcb;
}

function sbregistration_get_context($cmid)
{
    static $sbcontext;

    if (empty($sbcontext)) {
        if (!($sbcontext = context_module::instance($cmid))) {
            print_error('badcontext');
        }
    }
    return $sbcontext;
}

function sbregistration_create_events($sbregistration)
{
    global $DB;

    // Rmove any previously created event
    if ($events = $DB->get_records('event', array('modulename' => 'sbregistration', 'instance' => $sbregistration->id))) {
        foreach($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
    }

    // Add the event
    $event = new stdClass();
    $event->description     = $sbregistration->intro;
    $event->format          = $sbregistration->introformat;
    $event->courseid        = $sbregistration->course;
    $event->groupid         = 0;
    $event->userid          = 0;
    $event->modulename      = 'sbregistration';
    $event->instance        = $sbregistration->id;
    $event->eventtype       = 'open';
    $event->timestart       = $sbregistration->starttime;
    $event->visible         = instance_is_visible('sbregistration', $sbregistration);
    $event->timeduration    = ($sbregistration->endtime - $sbregistration->starttime);

    // Create a singke event for the whole time
    $event->name = $sbregistration->name;
    calendar_event::create($event);

    // If set create registration period in the calendar
    if (($sbregistration->closedate - $sbregistration->opendate > 0) &&
            ($sbregistration->closedate <= $sbregistration->starttime)) {
        $event = new stdClass();
        $event->format          = $sbregistration->introformat;
        $event->courseid        = $sbregistration->course;
        $event->courseid        = $sbregistration->course;
        $event->groupid         = 0;
        $event->userid          = 0;
        $event->modulename      = 'sbregistration';
        $event->instance        = $sbregistration->id;
        $event->eventtype       = 'open';
        $event->timestart       = $sbregistration->opendate;
        $event->visible         = instance_is_visible('sbregistration', $sbregistration);
        $event->timeduration    = ($sbregistration->closedate - $sbregistration->opendate);

        // Create a singke event for the whole time
        $event->name = get_string('registrationopen', 'sbregistration') . ' ' . $sbregistration->name;
        calendar_event::create($event);
    }
}

function sbregistration_get_status_codes()
{
    return array(0 => 'Faulty',
                 1 => 'Applied',
                 2 => 'Accepted',
                 3 => 'Rejected',
                 4 => 'Emailed');
}

function sbregistration_get_status($code = 1)
{
    $codes = registration_get_status_codes();
    if (($code > 0) && ($code < count($codes))) {
        return $codes[$code];
    }
    return $codes[0];
}

function sbregistration_get_status_dropdown($name = 'status', $val = 1)
{
    return html_writer::select(registration_get_status_codes(), $name, $val);
}

function sbregistration_process_emails($rid)
{
    global $DB;

    $sql = 'SELECT rs.id, rs.userid, rs.status, r.name, r.starttime, r.location, '
         . 'r.acceptsubject, r.acceptemail, r.rejectsubject, r.rejectemail, u.firstname '
         . 'FROM {sbregistration} r, {sbregistration_submissions} rs, {user} u '
         . 'WHERE r.id = rs.sbregistration AND rs.userid = u.id '
         . 'AND (rs.status = 2 OR rs.status = 3)';
    if (!$submissions = $DB->get_records_sql($sql, array($rid))) {
        return false;
    }

    // Iterate through each of the submissions
    foreach($submissions as $submission) {

        // Accepted
        if ($submission->status == 2) {

            // Define the email subject
            $subject = $submission->acceptsubject;

            // Select the correct text for the email bodymod_
            $messagetext = $submission->acceptemail;

        // Rejected
        } elseif ($submission->status == 3) {

            // Define the email subject
            $subject = $submission->rejectsubject;

            // Select the correct text for the email body
            $messagetext = $submission->rejectemail;
        }

        // Define the date and time from the
        $eventdate = DateTime::createFromFormat('U', $submission->starttime);

        // Replace placeholders with the relavant text
        $arr1 = array('###EVENT###',
                      '###NAME###',
                      '###DATE###',
                      '###TIME###',
                      '###LOCATION###');
        $arr2 =  array($submission->name,
                       $submission->firstname,
                       $eventdate->format('l d F Y'),
                       $eventdate->format('g:iA'),
                       $submission->location);
        $subject     = str_replace($arr1, $arr2, $subject);
        $messagetext = str_replace($arr1, $arr2, $messagetext);

        // If the subject is set get the users details and send the email
        if (!empty($subject)) {

            // Get the user to send the email to
            $user = core_user::get_user($submission->userid);

            // Send out the email
            if (email_to_user($user, core_user::get_noreply_user(), $subject, $messagetext, "", "", "", false)) {

                // Update the status for this user
                $DB->set_field('sbregistration_submissions', 'status', 4, array('id' => $submission->id));
            }
        }
    }
}

function sbregistration_get_editor_options($context)
{
    global $CFG;
    return array('subdirs'     => 1,
                 'maxbytes'    => $CFG->maxbytes,
                 'maxfiles'    => 1,
                 'chageformat' => 1,
                 'context'     => $context,
                 'noclean'     => 1,
                 'trusttext'   => 0);
}
