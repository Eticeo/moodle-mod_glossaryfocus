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
 * Library of interface functions and constants for module glossaryfocus
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // Course id.

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

// Trigger instances list viewed event.
$event = \mod_glossaryfocus\event\course_module_instance_list_viewed::create(
            array('context' => context_course::instance($course->id)));
$event->add_record_snapshot('course', $course);
$event->trigger();

$strpage         = get_string('modulename', 'glossaryfocus');
$strpages        = get_string('modulenameplural', 'glossaryfocus');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/glossaryfocus/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strpages);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strpages);
echo $OUTPUT->header();
echo $OUTPUT->heading($strpages);
if (!$glossaryfocuss = get_all_instances_in_course('glossaryfocus', $course)) {
    notice(get_string('thereareno', 'moodle', $strpages), $CFG->wwwroot."/course/view.php?id=".$course->id);
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($glossaryfocuss as $glossaryfocus) {
    $cm = $modinfo->cms[$glossaryfocus->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($glossaryfocus->section !== $currentsection) {
            if ($glossaryfocus->section) {
                $printsection = get_section_name($course, $glossaryfocus->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $glossaryfocus->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($glossaryfocus->timemodified)."</span>";
    }

    $class = $glossaryfocus->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.

    $table->data[] = array (
        $printsection,
        '<a '.$class.' href="view.php?id='.$cm->id.'">'.format_string($glossaryfocus->name).'</a>',
        format_module_intro('glossaryfocus', $glossaryfocus, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
