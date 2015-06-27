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

require_once($CFG->dirroot . '/mod/registration/locallib.php');

function registration_add_instance($data, $mform = null)
{
    global $DB;

    if ($mform) {
        $data->acceptemail       = $data->acceptbody['text'];
        $data->acceptemailformat = $data->acceptbody['format'];

        $data->rejectemail       = $data->rejectbody['text'];
        $data->rejectemailformat = $data->rejectbody['format'];
    }
    $data->timemodified = time();
    $date->timecreated  = $data->timemodified;

    if (!($data->id = $DB->insert_record('registration', $data))) {
        return false;
    }

    // Create the events in the calendar
    registration_create_events($data);

    return $data->id;
}

function registration_update_instance($data, $mform)
{
    global $DB;

    if ($mform) {
        $data->acceptemail       = $data->acceptbody['text'];
        $data->acceptemailformat = $data->acceptbody['format'];

        $data->rejectemail       = $data->rejectbody['text'];
        $data->rejectemailformat = $data->rejectbody['format'];
    }
    $data->timemodified = time();
    $date->id = $data->instance;

    // Create the events in the calendar
    registration_create_events($data);

    return $DB->update_record('registration', $date§);
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
    if ($events = $DB->get_records('event', array('modulename' => 'registration', 'instance' => $registration->id))) {
        foreach($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
    }
    return true;
}

function registration_supports($feature)
{
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:
            return false;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPMEMBERSONLY:
            return false;
        case FEATURE_SHOW_DESCRIPTION:
            return false;
        default:
            return null;
    }
}
