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
 * Filename : backup_sbregistration_activity_task
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 03 May 2015
 */

require_once($CFG->dirroot . '/mod/sbregistration/backup/moodle2/backup_sbregistration_stepslib.php');

class backup_sbregistration_activity_task extends backup_activity_task
{
    protected function define_my_settings()
    {
    }

    protected function define_my_steps()
    {
        $this->add_step(new backup_sbregistration_activity_structure_step('sbregistration_structure', 'sbregistration.xml'));
    }

    static public function encode_content_links($content)
    {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of registrations
        $search = '/(' . $base . "\/mod\/sbregistration\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@QUESTIONNAIREINDEX*$2@$', $content);

        // Link to registration view by moduleid
        $search = '/(' . $base . "\/mod\/sbregistration\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@QUESTIONNAIREVIEWBYID*$2@$', $content);

        return $content;
    }
}