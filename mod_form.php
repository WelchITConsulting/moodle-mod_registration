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

        $this->standard_intro_elements(get_string('description', 'registration'));
        $mform->addHelpButton('introeditor', 'description', 'registration');
        $mform->addRule('introeditor', null, 'required', null, 'client');

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
        $mform->setDefault('acceptsubject', get_string('acceptsubject_default', 'registration'));

        $mform->addElement('editor', 'acceptbody', get_string('acceptemail', 'registration'), array('wrap' => 'virtual', 'rows' => '10', 'cols' => '50'));
        $mform->setType('acceptbody', PARAM_RAW);
        $mform->addRule('acceptbody', null, 'required', null, 'client');
        $mform->addHelpButton('acceptbody', 'acceptemail', 'registration');
        $mform->setDefault('acceptbody', get_string('acceptemail_default', 'registration'));

        $mform->addElement('text', 'rejectsubject', get_string('rejectsubject', 'registration'), array('size' => '64'));
        $mform->setType('rejectsubject', PARAM_TEXT);
        $mform->addRule('rejectsubject', null, 'required', null, 'client');
        $mform->addHelpButton('rejectsubject', 'rejectsubject', 'registration');
        $mform->setDefault('rejectsubject', get_string('rejectsubject_default', 'registration'));

        $mform->addElement('editor', 'rejectbody', get_string('rejectemail', 'registration'));
        $mform->setType('rejectbody', PARAM_RAW);
        $mform->addRule('rejectbody', null, 'required', null, 'client');
        $mform->addHelpButton('rejectbody', 'rejectemail', 'registration');
        $mform->setDefault('rejectbody', get_string('rejectemail_default', 'registration'));

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values)
    {
        if ($this->current->instance) {

            $acceptitemid = file_get_submitted_draft_itemid('acceptbody');
            $default_values['acceptbody']['format'] = $default_values['acceptemailformat'];
            $defeult_values['acceptbody']['text']   = file_prepare_draft_area($acceptitemid,
                                                                              $this->context->id,
                                                                              'mod_registration',
                                                                              'acceptemail',
                                                                              0,
                                                                              registration_get_editor_options($this->context),
                                                                              $default_values['acceptemail']);
            $default_values['acceptbody']['itemid'] = $acceptitemid;

            $rejectitemid = file_get_submitted_draft_itemid('rejectbody');
            $default_values['rejectbody']['format'] = $default_values['rejectemailformat'];
            $defeult_values['rejectbody']['text']   = file_prepare_draft_area($rejectitemid,
                                                                              $this->context->id,
                                                                              'mod_registration',
                                                                              'rejecttemail',
                                                                              0,
                                                                              registration_get_editor_options($this->context),
                                                                              $default_values['rejectemail']);
            $default_values['rejectbody']['itemid'] = $rejectitemid;
        }
    }
}
