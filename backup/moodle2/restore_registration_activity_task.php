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
 * Filename : restore_registration_activity_task
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 03 May 2015
 */

class restore_registration_activithy_task extends restore_activity_task
{
    protected function define_my_settings()
    {
    }

    protected function define_my_steps()
    {
        $this->add_step(new restore_registration_activity_structure_step('registration_structure', 'registration.xml'));
    }

    static public function define_decode_contents()
    {
        $contents = array(new restore_decode_content('registration', array('intro', 'acceptemailbody', 'rejectemailbody'), 'registration'));
        return $contents;
    }

    static public function define_decode_rules()
    {
        $rules = array(new restore_decode_rule('REGISTRATIONVIEWBYID', '/mod/registration/view.php?id=$1', 'course_module'),
                       new restore_decode_rule('REGISTRATIONINDEX', '/mod/registration/index.php?id=$1', 'course'));
        return $rules;
    }

    static public function define_restore_log_rules()
    {
        parent::define_restore_log_rules();
    }

    static public function define_restore_log_rules_for_course()
    {

    }
}
