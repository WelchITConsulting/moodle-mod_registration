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
 * Filename : lib
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 24 Jan 2015
 */

function registration_add_instance($registration)
{
    $registration->timemodified = time();
    $registration->timecreated  = $registration->timemodified;

    if (!($registration->id = $DB->insert_record('registration', $registration))) {
        return false;
    }
    return $registration->id;
}

function registration_update_instance($registration)
{
    $registration->timemodified = time();

    return $DB->update_record('registration', $registration);
}

function registration_delete_instance($id)
{
    global $DB;

    if (!($regisration = $DB->get_record('registration', array('id' => $id)))) {
        return false;
    }
    if (!$DB->delete_records('registration_submissions', array('registration' => $regisration->id))) {
        return false;
    }
    if (!$DB->delete_records('registration', array('id' => $regisration->id))) {
        return false;
    }
    if (!$DB->delete_records('event', array('module_name' => 'registration', 'instance' => $registration->id))) {
        return false;
    }
    return true;
}
