<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Private page module utility functions
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com> made by Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

//require_once("$CFG->libdir/filelib.php");
//require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/glossaryfocus/lib.php");

function get_words_for_view($glossaryfocus) {
    global $DB;
    $res = array();
    $listWords = $DB->get_records_sql("
            SELECT ge.*
            FROM {glossaryfocus_entries} gfe INNER JOIN 
                 {glossary_entries} ge ON (gfe.idglossaryentrie = ge.id)
            WHERE gfe.idglossaryfocus = $glossaryfocus->id
            order by ge.concept
    ");

    if (empty($listWords) && $glossaryfocus->idglossarymaster > 0) {
        $listWords = $DB->get_records_sql("
            SELECT ge.*
            FROM {glossaryfocus} gf INNER JOIN 
                 {glossary_entries} ge ON (gf.idglossarymaster = ge.glossaryid)
            WHERE ge.glossaryid = $glossaryfocus->idglossarymaster
            order by ge.concept
        ");
    } else if (empty($listWords) && $glossaryfocus->idglossarymaster == 0) {
        $listWords = $DB->get_records_sql("
            SELECT ge.*
            FROM {glossary_entries} ge 
            order by ge.concept
        ");
    }

    return $listWords;
}

function get_words($idGlossaryfocus) {
    global $DB;
    $res = array();
    $listWords = $DB->get_records_sql("
            SELECT ge.id, ge.concept, g.name
            FROM {glossaryfocus_entries} gfe INNER JOIN 
                 {glossary_entries} ge ON (gfe.idglossaryentrie = ge.id) INNER JOIN 
                 {glossary} g ON (ge.glossaryid = g.id)
            WHERE gfe.idglossaryfocus = $idGlossaryfocus
    ");

    foreach ($listWords as $word) {
        $res[$word->id] = $word->concept.' ('.$word->name.')';
    }

    return $res;
}

function get_opt_glossarymaster() {
    global $DB;
    $res = array();
    $glossariesMaster = $DB->get_records("glossary");

    $res[0] = get_string("otp_all_master","glossaryfocus");
    foreach ($glossariesMaster as $glossary) {
        $res[$glossary->id] = $glossary->name;
    }

    return $res;
}