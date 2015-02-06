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
 * Filename : upgrade
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 24 Jan 2015
 */

function xmldb_registration_upgrade($oldversion = 0)
{
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2015020600) {

        // Process the database schema updates for the registration table
        $table = new xmldb_table('registration');

        // Add the events end time field
        $field = new xmldb_field('endtime');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'eventdate');
        $dbman->add_field($table, $field);

        // Rename the eventdate field
        $dbman->rename_field($table, 'eventdate', 'starttime');

        // Process the database schema updates for the registration_submissions table
        $table = new xmldb_table('registration_submissions');

        // Add the notes fields
        $field = new xmldb_field('notes');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'userid');
        $dbman->add_field($table, $field);

        // Add the status fields
        $field = new xmldb_field('status');
        $field->set_attributes(XMLDB_TYPE_CHAR, 1, null, null, null, null, 'notes');
        $dbman->add_field($table, $field);

        // Remove the mailed field
        $dbman->drop_field($table, 'mailed');

        // Registration savepoint reached
        upgrade_mod_savepoint(true, 2015020600, 'registration');
    }

    return true;
}
