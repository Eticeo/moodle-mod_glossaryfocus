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
 * This is the external API for this tool.
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com> made by Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_glossaryfocus;
defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

require_once("$CFG->libdir/externallib.php");

/**
 * This is the external API for this tool.
 *
 * @copyright  2021 Eticeo <https://eticeo.com> made by Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends \external_api {
    /**
     * Returns get_words() parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_words_parameters() {
        return new \external_function_parameters(
            array(
                'query' => new external_value(PARAM_TEXT,
                    'Query string (full or partial word name)'),
                'idglossary' => new external_value(PARAM_INT, 'Glossary master id (0 if none)'),
            )
        );
    }

    /**
     * Handles return the element's HTML.
     *
     * @param  string|null $query
     * @param  int|null $idglossary
     * @return array of words
     */
    public static function get_words($query, $idglossary) {
        global $DB;
        $result = [];

        $condition ="";
        if ($idglossary > 0) {
            $condition .= " AND glossaryid = $idglossary";
        }
        $listWords = $DB->get_records_sql("
            SELECT ge.id, ge.concept, g.name
            FROM {glossary_entries} ge INNER JOIN {glossary} g ON (ge.glossaryid = g.id)
            WHERE concept like '%$query%' $condition
        ");

        foreach ($listWords as $word) {
            $result[] = ['id' => $word->id, 'name' => $word->concept.' ('.$word->name.')'];
        }

        return $result;
    }

    /**
     * Returns the get_words result value.
     *
     * @return \external_value
     */
    public static function get_words_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'    => new external_value(PARAM_INT, 'ID of the word'),
                'name'  => new external_value(PARAM_NOTAGS, 'The word name')
            ])
        );
    }
}
