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
 * Filename : locallib
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
<<<<<<< HEAD
 * Created  : 25 Jan 2015
 */

require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->dirroot . '/calendar/lib.php');


function registration_load_capailities($cmid)
{
    static $sbcb;

    if (empty($sbcb)) {
        $context = registration_get_context($cmid);
        $sbcb = new object();
        $sbcb->view                 = has_capability('mod/registration:view', $context);
        $sbcb->viewsingleresponse   = has_capability('mod/registration:viewsingleresponse', $context);
        $sbcb->deleteresponses      = has_capability('mod/registration:deleteresponses', $context);
        $sbcb->downloadresponses    = has_capability('mod/registration:downloadresponses', $context);
        $sbcb->submit               = has_capability('mod/registration:submit', $context);
        $sbcb->manage               = has_capability('mod/registration:manage', $context);
    }
    return $sbcb;
}

function registration_get_context($cmid)
{
    static $sbcontext;

    if (empty($sbcontext)) {
        if (!($sbcontext = context_module::instance($cmid))) {
            print_error('badcontext');
        }
    }
    return $sbcontext;
}
