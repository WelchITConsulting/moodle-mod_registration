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

require_once($CFG->dirroot . '/mod/sbregistration/locallib.php');

function sbregistration_add_instance($data, $mform = null)
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

    if (!($data->id = $DB->insert_record('sbregistration', $data))) {
        return false;
    }

    // Create the events in the calendar
    sbregistration_create_events($data);

    return $data->id;
}

function sbregistration_update_instance($data, $mform)
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
    sbregistration_create_events($data);

    return $DB->update_record('sbregistration', $date§);
}

function sbregistration_delete_instance($id)
{
    global $DB;

    if (!($sbregisration = $DB->get_record('sbregistration', array('id' => $id)))) {
        return false;
    }
    if (!$DB->delete_records('sbregistration_submissions', array('sbregistration' => $sbregisration->id))) {
        return false;
    }
    if (!$DB->delete_records('sbregistration', array('id' => $sbregisration->id))) {
        return false;
    }
    if ($events = $DB->get_records('event', array('modulename' => 'sbregistration', 'instance' => $sbregistration->id))) {
        foreach($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
    }
    return true;
}

function sbregistration_supports($feature)
{
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:            return false;
        case FEATURE_COMPLETION_HAS_RULES:      return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:   return false;
        case FEATURE_GRADE_HAS_GRADE:           return false;
        case FEATURE_GROUPINGS:                 return false;
        case FEATURE_GROUPS:                    return false;
        case FEATURE_GROUPMEMBERSONLY:          return false;
        case FEATURE_SHOW_DESCRIPTION:          return false;

        default:
            return null;
    }
}
