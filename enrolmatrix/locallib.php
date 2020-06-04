<?php

defined('MOODLE_INTERNAL') || die();

function get_cohorts_names()
{
    global $DB;

    return $DB->get_records('cohort', null, '', 'id, name AS cohortname');
}

function get_courses_names()
{
    global $DB;

    $courses = $DB->get_records('course', null, '', 'id, shortname AS coursename');
    unset($courses[1]);  // Unset the course created by default
    return $courses;
}

function get_enrolmatrix()
{
    global $DB;

    $enrolmatrix = $DB->get_records('enrol', array('enrol' => 'cohort'), '', 'id, courseid, customint1 AS cohortid');
    $cohorts = get_cohorts_names();
    $courses = get_courses_names();

    $matrix = array();
    foreach ($cohorts as $cohortid => $cohort) {
        $matrix[$cohortid] = array();
    }
    foreach ($enrolmatrix as $id => $obj) {
        $matrix[$obj->cohortid][$obj->courseid] = true;
    }
    return $matrix;
}

function enrol_cohort($cohortid, $courseid)
{
    global $DB;

    $result = $DB->insert_record('enrol', array('enrol' => 'cohort', 'courseid' => $courseid,
		'sortorder' => 3, 'name' => '', 'roleid' => 5, 'customint1' => $cohortid, 'customint2' => 0));
    if ($result) {
        enrol_log($courseid, $cohortid, 1);
    }
    return result;
}

function unenrol_cohort($cohortid, $courseid)
{
    global $DB;

    $result = $DB->delete_records('enrol', array('enrol' => 'cohort', 'courseid' => $courseid,
		'customint1' => $cohortid));
    if ($result) {
        enrol_log($courseid, $cohortid, 0);
    }
    return result;
}

function can_enrol()
{
    $context = context_system::instance();

    return has_capability('enrol/cohort:config', $context);
}

function enrol_log($courseid, $cohortid, $value)
{
    global $CFG, $USER, $DB;

    if ($CFG->enable_enrolmatrix_log) {
        $userid = null;
        if ($USER) {
            $userid = $USER->id;
        }
        $instance = array('cohortid' => $cohortid, 'courseid' => $courseid,
            'value' => $value, 'timestamp' => time(), 'userid' => $userid);
        $DB->insert_record('enrol_matrix_history', $instance);
    }
}

