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
 * Filename : interest_form
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 08 Feb 2015
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class registration_interest_form extends moodleform
{
    public function definition()
    {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'yesnofield', get_string('yesnofield', 'sbregistration'));
        $mform->setType('yesnofield', PARAM_BOOL);
        $mform->addRule('yesnofield', null, 'required', null, 'client');

        $mform->addElement('textarea', 'notes', get_string('notes', 'sbregistration'), array('wrap' => 'virtual', 'rows' => '4', 'cols' => '50'));
        $mform->setType('notes', PARAM_RAW);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('submit', 'submitbutton', get_string('submitbutton', 'sbregistration'));
    }
}
