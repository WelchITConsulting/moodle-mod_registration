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
 * Filename : backup_registration_stepslib
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 03 May 2015
 */

class backup_registration_activity_structure_step extends backup_activity_structure_step
{
    protected function define_structure()
    {
        $userinfo = $this->get_setting_value('userinfo');

        $registration = new backup_nested_element('registration', array('id'), array(
            'course', 'name', 'intro', 'introformat', 'places', 'location', 'starttime',
            'endtime', 'opendate', 'closedate', 'acceptsubject', 'acceptemail',
            'acceptemailformat', 'rejectsubject', 'rejectemail', 'rejectemailformat',
            'timecreated', 'timemodified'));

        $submissions = new backup_nested_element('submissions');

        $submission = new backup_nested_element('submission', array('id'), array(
            'registration', 'userid', 'notes', 'status', 'timecreated', 'timemodified'));

        $registration->add_child($submissions);
        $submission->add_child($submission);

        // Define sources
        $registration->set_source_table('registration', array('id' => backup::VAR_ACTIVITYID));
        $submission->set_source_table('registration_submissions', array('registration' => backup::VAR_PARENTID));
    }
}