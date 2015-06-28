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

require_once($CFG->dirroot . '/mod/sbregistration/locallib.php');

class SmartBridgeRegistration {

    public function __construct( &$course, &$cm, $id = 0, $sbregistration = null)
    {
        global $DB;

        if ($id) {
            $sbregistration = $DB->get_record('sbregistration', array('id' => $id));
        }

        if (is_object($sbregistration)) {
            $properties = get_object_vars($sbregistration);
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
            $this->capabilities = sbregistration_load_capabilities($this->cm->id);
        }

        // Determine the periods for the event and the registration
        $this->eventavailable        = $this->endtime - $this->starttime;
        $this->registrationavailable = $this->closedate - $this->opendate;

        // Set the start and end days for the event and registration periods
        $this->eventstart = calendar_day_representation($this->starttime)
                          . ', '
                          . calendar_time_representation($this->starttime);
        $this->eventend   = calendar_day_representation($this->endtime)
                          . ', '
                          . calendar_time_representation($this->endtime);
        if ($this->opendate == 0) {
            $this->regstart = 0;
        } else {
            $this->regstart   = calendar_day_representation($this->opendate)
                              . ', '
                              . calendar_time_representation($this->opendate);
        }
        if ($this->closedate == 0) {
            $this->regend = 0;
        } else {
            $this->regend     = calendar_day_representation($this->closedate)
                              . ', '
                              . calendar_time_representation($this->closedate);
        }
    }

    public function is_active()
    {
        return ($this->endtime < time());
    }

    public function is_registration_open()
    {
        if (($this->opendate >= time()) && ($this->closedate <= time())) {
            return true;
        }
        return false;
    }

    public function user_is_eligible($userid)
    {
        return ($this->capabilites->view && $this->capabilities->submit);
    }

    public function submitted()
    {
        global $DB, $USER;
        $submissions = (int)$DB->count_records_sql('SELECT COUNT(*) FROM {sbregistration_submissions} WHERE sbregistration=? AND userid=?', array($this->id, $USER->id));
        return ($submissions > 0);
    }

    public function has_submissions()
    {
        global $DB;
        $submissions = (int) $DB->count_records_sql('SELECT COUNT(*) FROM {sbregistration_submissions} WHERE sbregistration=?',
                                                    array($this->id));
        return ($submissions > 0);
    }

    public function display_event()
    {
        global $DB;
    }
}
