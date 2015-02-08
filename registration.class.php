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
 * Filename : registration
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 06 Feb 2015
 */

require_once($CFG->dirroot . '/mod/registration/locallib.php');

class SmartBridgeRegistration {

    // Max time before separate calendar events are created - 5 days (5 * 24 * 60 * 60)
    private $max_event_length = 432000;

    public function __construct( &$course, &$cm, $id = 0, $registration = null)
    {
        global $DB;

        if ($id) {
            $registration = $DB->get_record('registration', array('id' => $id));
        }

        if (is_object($registration)) {
            $properties = get_object_vars($registration);
            foreach($properties as $prop => $val) {
                $this->$prop = $val;
            }
        }

        $this->course = $course;
        $this->cm = $cm;

        // New regisrations will not have a context yet
        if (!empty($cm) && !empty($this->id)) {
            $this->context = context_module::instance($cm->id);
        } else {
            $this->context = null;
        }

        // Load the capabilities if not new
        if (!empty($this->id)) {
            $this->capabilities = registration_load_capailities($this->cm->id);
        }

        // Determine the periods for the event and the registration
        $this->eventavailable        = $this->endtime - $this->starttime;
        $this->registrationavailable = $this->closedate - $this->opendate;
    }

    public function is_active()
    {
        // Check if the event has passed
    }

    public function is_registration_open()
    {
        return ($this->opendate > 0) ? ($this->opendate < time()) : true;
    }

    public function is_registration_closed()
    {
        return ($this->closedate > 0) ? ($this->closedate < time()) : false;
    }

    public function user_is_eligible($userid)
    {
        return ($this->capabilites->view && $this->capabilities->submit);
    }

    public function create_events()
    {
        global $DB;

        // Rmove any previously created event
        if ($events = $DB->get_records('event', array('modulename' => 'registration', 'instance' => $this->id))) {
            foreach($events as $event) {
                $event = calendar_event::load($event);
                $event->delete();
            }
        }

        // Add the event
        $event = new stdClass();
        $event->description     = $this->intro;
        $event->format          = $this->introformat;
        $event->courseid        = $this->course;
        $event->groupid         = 0;
        $event->userid          = 0;
        $event->modulename      = 'registration';
        $event->instance        = $this->id;
        $event->eventtype       = 'open';
        $event->timestart       = $this->starttime;
        $event->visible         = instance_is_visible('registration', $this);
        $event->timeduration    = ($this->endtime - $this->starttime);

        if ($event <= $$this->max_event_length) {
            // Create a singke event for the whole time
            $event->name = $this->name;
            calendar_event::create($event);
        } else {
            // Create separate events for the start and end of the period
            $event->timeduration = 0;
            $event->name = $this->name . ' (' . get_string('eventopens', 'registration') . ')';
            calendar_event::create($event);
            unset($event->id);
            $event->name = $this->name . ' (' . get_string('eventcloses', 'registration') . ')';
            $event->eventtype = 'close';
            calendar_event::create($event);
        }

        // If set create registration period in the calendar
        if (($this->closedate - $this->opendate > 0) && ($this->closedate <= $this->starttime)) {
            $event = new stdClass();
            $event->format          = $this->introformat;
            $event->courseid        = $this->course;
            $event->courseid        = $this->course;
            $event->groupid         = 0;
            $event->userid          = 0;
            $event->modulename      = 'registration';
            $event->instance        = $this->id;
            $event->eventtype       = 'open';
            $event->timestart       = $this->opendate;
            $event->visible         = instance_is_visible('registration', $this);
            $event->timeduration    = ($this->closedate - $this->opendate);
            calendar_event::create($event);

            if ($event <= $$this->max_event_length) {
                // Create a singke event for the whole time
                $event->name = get_string('registrationopen', 'registration') . ' ' . $this->name;
                calendar_event::create($event);
            } else {
                // Create separate events for the start and end of the period
                $event->timeduration = 0;
                $event->name = get_string('registrationopens', 'registration') . ' ' . $this->name;
                calendar_event::create($event);
                unset($event->id);
                $event->name = get_string('registrationcloses', 'registration') . ' ' . $this->name;
                $event->timestart = $this->closedate;
                $event->eventtype = 'close';
                calendar_event::create($event);
            }
        }
    }
}
