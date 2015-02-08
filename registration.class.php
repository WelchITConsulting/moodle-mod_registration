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
}
