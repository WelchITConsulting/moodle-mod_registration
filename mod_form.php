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
 * Filename : mod_form
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 24 Jan 2015
 */

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/registration/locallib.php');

class mod_registration_mod_form extends moodleform_mod
{
    public function definition()
    {
        global $COURSE;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'registration'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->add_intro_editor(true, get_string('description', 'registration'));
        $mform->addHelpButton('introeditor', 'description', 'registration');

        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'registration'));
        $mform->addRule('starttime', null, 'required', null, 'client');
        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'registration'));
        $mform->addRule('endtime', null, 'required', null, 'client');

        // Add number of places
        $mform->addElement('text', 'places', get_string('numberofplaces', 'registration'));
        $mform->setType('places', PARAM_INT);
        $mform->addHelpButton('places', 'numberofplaces', 'registration');

        // Add event location
        $mform->addElement('text', 'location', get_string('location', 'registration'), array('size' => '64'));
        $mform->setType('location', PARAM_TEXT);
        $mform->addRule('location', null, 'required', null, 'client');
        $mform->addHelpButton('location', 'location', 'registration');

        $mform->addElement('header', 'timinghdr', get_string('period', 'registration'));

        $mform->addElement('date_time_selector', 'opendate', get_string('opendate', 'registration'));
        $mform->addHelpButton('opendate', 'opendate', 'registration');
        $mform->addElement('date_time_selector', 'closedate', get_string('closedate', 'registration'));
        $mform->addHelpButton('closedate', 'closedate', 'registration');

        $mform->addElement('header', 'emails', get_string('emailconfirmations', 'registration'));

        $mform->addElement('text', 'acceptsubject', get_string('acceptsubject', 'registration'), array('size' => '64'));
        $mform->setType('acceptsubject', PARAM_TEXT);
        $mform->addRule('acceptsubject', null, 'required', null, 'client');
        $mform->addHelpButton('acceptsubject', 'acceptsubject', 'registration');

        $mform->addElement('textarea', 'acceptemail', get_string('acceptemail', 'registration'), array('wrap' => 'virtual', 'rows' => '10', 'cols' => '50'));
        $mform->setType('acceptemail', PARAM_RAW);
        $mform->addRule('acceptemail', null, 'required', null, 'client');
        $mform->addHelpButton('acceptemail', 'acceptemail', 'registration');

        $mform->addElement('text', 'rejectsubject', get_string('rejectsubject', 'registration'), array('size' => '64'));
        $mform->setType('rejectsubject', PARAM_TEXT);
        $mform->addRule('rejectsubject', null, 'required', null, 'client');
        $mform->addHelpButton('rejectsubject', 'rejectsubject', 'registration');

        $mform->addElement('textarea', 'rejectemail', get_string('rejectemail', 'registration'), array('wrap' => 'virtual', 'rows' => '10', 'cols' => '50'));
        $mform->setType('rejectemail', PARAM_RAW);
        $mform->addRule('rejectemail', null, 'required', null, 'client');
        $mform->addHelpButton('rejectemail', 'rejectemail', 'registration');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
