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
 * Defines the renderer for the quiz module.
 *
 * @package   mod_glossaryfocus
 * @copyright 2021 Eticeo <https://eticeo.com> made by Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * The renderer for the glossaryfocus module.
 *
 * @copyright  2021 Eticeo <https://eticeo.com> made by Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_glossaryfocus_renderer extends plugin_renderer_base {

    public function render_glossaryfocus_words($entries, $cmid) {
        $res = "";

        $res .= html_writer::start_div("listWords");

        foreach ($entries as $entrie) {
            $res .= $this->render_word($entrie, $cmid);
        }

        $res .= html_writer::end_div();

        return $res;
    }

    public function render_word($entrie, $cmid) {
        //we need to find module context for this word.
        $cmid = get_coursemodule_from_instance("glossary",$entrie->glossaryid)->id;
        //var_dump($cmid);
        //die;
        $context = context_module::instance($cmid);

        $res = "";

        $res .= html_writer::start_div("word");
        $res .= html_writer::tag("h3",$entrie->concept,array("class"=>"bold"));

        $res .= html_writer::start_div("def",array("class"=>"ml-4"));

        $def = file_rewrite_pluginfile_urls($entrie->definition, 'pluginfile.php', $context->id, 'mod_glossary', 'entry', $entrie->id);

        $def = format_text($def);
        $res .= $def;
        $res .= html_writer::end_div();
        $res .= html_writer::end_div("word");
        return $res;
    }
}