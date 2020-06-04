<?php

require_once('../../config.php');
require_once('./locallib.php');

if (!empty($_GET) && 'on' == $_GET['_updateem']) {
    if (!can_enrol()) {
        header('Location: ' . $uri . '/moodle/mod/enrolmatrix');
        die();
    }
    if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
        $uri = 'https://';
    } else {
        $uri = 'http://';
    }
    $uri .= $_SERVER['HTTP_HOST'];

    unset($_GET['_updateem']);
    $oldmatrix = get_enrolmatrix();
    $newmatrix = array();
    $courses = get_courses_names();
    foreach ($oldmatrix as $key => $row) {
        $newmatrix[$key] = array();
    }
    foreach ($_GET as $key => $val) {
        $arr = explode('-', $key);
        $cohortid = (int) $arr[0];
        $courseid = (int) $arr[1];
        $newmatrix[$cohortid][$courseid] = true;
    }
    foreach ($oldmatrix as $cohortid => $row) {
        foreach ($courses as $courseid => $course) {
            if (!!$oldmatrix[$cohortid][$courseid] != !!$newmatrix[$cohortid][$courseid]) {
                if ($newmatrix[$cohortid][$courseid]) {
                    enrol_cohort($cohortid, $courseid);
                } else {
                    unenrol_cohort($cohortid, $courseid);
                }
            }
        }
    }

    header('Location: ' . $uri . '/moodle/mod/enrolmatrix');
    die();
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pagetitle', 'enrolmatrix'));
$PAGE->set_heading(get_string('pagetitle', 'enrolmatrix'));
$PAGE->set_url(new moodle_url('/mod/enrolmatrix/index.php'));

echo $OUTPUT->header();

$cohorts = get_cohorts_names();
$courses = get_courses_names();
$enrolmatrix = get_enrolmatrix();

echo
    '<form action="index.php" method="get">
    <table id="enrolmatrix" class="table">
    <thead>
    <tr><th scope="col">#</th>';
foreach ($courses as $course) {
    echo '<th scope="col">' . $course->coursename . '</th>';
}
echo
    '</tr>
    </thead>
    <tbody>';

foreach ($cohorts as $cohortid => $cohort) {
    echo '<tr><th scope="row">' . $cohort->cohortname . '</th>';
    foreach ($courses as $courseid => $course) {
        $inputname = $cohortid . '-' . $courseid;
        $checked = '';
        if ($enrolmatrix[$cohortid][$courseid]) {
            $checked = 'checked="true"';
        }
        echo '<td><input type="checkbox" ' . $checked . ' id="' . $inputname . '" name="' . $inputname . '"></td>';
    }
    echo '</tr>';
}
echo
    '</tbody>
    </table>
    <input type="hidden" value=on id="_updateem" name="_updateem">';

if (can_enrol()) {
    echo '<input type="submit" value="' . get_string('enrolbutton', 'enrolmatrix') . '">';
}

echo '</form>';

echo $OUTPUT->footer();
