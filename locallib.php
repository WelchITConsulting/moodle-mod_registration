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
define('REGISTRATION_MAX_EVENT_LENGTH', (5 * 24 * 60 * 60));

function registration_load_capailities($cmid)
{
    static $sbcb;

    if (empty($sbcb)) {
        $context = registration_get_context($cmid);
        $sbcb = new object();
        $sbcb->view                 = has_capability('mod/registration:view', $context);
        $sbcb->viewsingleresponse   = has_capability('mod/registration:viewsingleresponse', $context);
        $sbcb->deleteresponses      = has_capability('mod/registration:deleteresponses', $context);
        $sbcb->downloadresponses    = has_capability('mod/registration:downloadresponses', $context);
        $sbcb->submit               = has_capability('mod/registration:submit', $context);
        $sbcb->manage               = has_capability('mod/registration:manage', $context);
    }
    return $sbcb;
}

function registration_get_context($cmid)
{
    static $sbcontext;

    if (empty($sbcontext)) {
        if (!($sbcontext = context_module::instance($cmid))) {
            print_error('badcontext');
        }
    }
    return $sbcontext;
}

function registration_create_events($registration)
{
    global $DB;

    // Rmove any previously created event
    if ($events = $DB->get_records('event', array('modulename' => 'registration', 'instance' => $registration->id))) {
        foreach($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
    }

    // Add the event
    $event = new stdClass();
    $event->description     = $registration->intro;
    $event->format          = $registration->introformat;
    $event->courseid        = $registration->course;
    $event->groupid         = 0;
    $event->userid          = 0;
    $event->modulename      = 'registration';
    $event->instance        = $registration->id;
    $event->eventtype       = 'open';
    $event->timestart       = $registration->starttime;
    $event->visible         = instance_is_visible('registration', $registration);
    $event->timeduration    = ($registration->endtime - $registration->starttime);

    if ($event->timeduration <= REGISTRATION_MAX_EVENT_LENGTH) {
        // Create a singke event for the whole time
        $event->name = $registration->name;
        calendar_event::create($event);
    } else {
        // Create separate events for the start and end of the period
        $event->timeduration = 0;
        $event->name = $registration->name . ' (' . get_string('eventopens', 'registration') . ')';
        calendar_event::create($event);
        unset($event->id);
        $event->name = $registration->name . ' (' . get_string('eventcloses', 'registration') . ')';
        $event->eventtype = 'close';
        calendar_event::create($event);
    }

    // If set create registration period in the calendar
    if (($registration->closedate - $registration->opendate > 0) && ($registration->closedate <= $registration->starttime)) {
        $event = new stdClass();
        $event->format          = $registration->introformat;
        $event->courseid        = $registration->course;
        $event->courseid        = $registration->course;
        $event->groupid         = 0;
        $event->userid          = 0;
        $event->modulename      = 'registration';
        $event->instance        = $registration->id;
        $event->eventtype       = 'open';
        $event->timestart       = $registration->opendate;
        $event->visible         = instance_is_visible('registration', $registration);
        $event->timeduration    = ($registration->closedate - $registration->opendate);

        if ($event->timeduration <= REGISTRATION_MAX_EVENT_LENGTH) {
            // Create a singke event for the whole time
            $event->name = get_string('registrationopen', 'registration') . ' ' . $registration->name;
            calendar_event::create($event);
        } else {
            // Create separate events for the start and end of the period
            $event->timeduration = 0;
            $event->name = get_string('registrationopens', 'registration') . ' ' . $registration->name;
            calendar_event::create($event);
            unset($event->id);
            $event->name = get_string('registrationcloses', 'registration') . ' ' . $registration->name;
            $event->timestart = $registration->closedate;
            $event->eventtype = 'close';
            calendar_event::create($event);
        }
    }
}
