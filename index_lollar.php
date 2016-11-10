<?php
require_once 'Database.php';
require_once 'modal_newSection.php';
require_once 'modal_newCourse.php';
require_once 'modal_newProfessor.php';
require_once 'modal_newClassroom.php';
require_once 'modal_newSemester.php';
require_once 'modal_newBuilding.php';
require_once 'modal_newCampus.php';
require_once 'modal_newDepartment.php';
$database = new Database();
$body = "
<!DOCTYPE html>
<html>
  <head>
    <link href='css/bootstrap.min.css' rel='stylesheet' />
    <link href='css/application.css' rel='stylesheet' />
    <link href='css/fullcalendar.css' rel='stylesheet' />

    <script src='js/jquery.min.js' charset='utf-8'></script>
    <script src='js/jquery-3.1.1.min.js' charset='utf-8'></script>
    <script src='js/bootstrap.min.js' charset='utf-8'></script>
    <script src='js/moment.min.js' charset='utf-8'></script>
    <script src='js/fullcalendar.min.js' charset='utf-8'></script>

    <script src='js/processForm.js' charset='utf-8'></script>
    <script src='js/calendar.js' charset='utf-8'></script>

    <title>Project ZAAMG</title>
  </head>
  <body>
    <div class='page-top-banner'>
      <img src='img/wsu-logo.png' class='banner-logo' />
    </div>
    <nav class='navbar navbar-default'>
      <div class='container-fluid'>
        <div class='navbar-header'>
          <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#bs-example-navbar-collapse-1' aria-expanded='false'>
<span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </button>
          <a class='navbar-brand' href='#'>ZAAMG</a>
        </div>
        <div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>
          <ul class='nav navbar-nav'>
            <li class='active'><a href='#'>Semester <span class='sr-only'>(current)</span></a></li>
            <li><a href='#'>Professor</a></li>
            <li><a href='#'>Classroom</a></li>
          </ul>
          <form class='navbar-form navbar-left'>
            <div class='form-group'>
              <input type='text' class='form-control' placeholder='Search'>
            </div>
            <button type='submit' class='btn btn-default'>Submit</button>
          </form>
          <ul class='nav navbar-nav navbar-right'>
            <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='true'>Create New <span class='caret'></span></a>
              <ul class='dropdown-menu'>
                <li><a href='#' data-toggle='modal' data-target='#newSectionModal'
                       class='newResourceLink' id='newSectionLink'>Section</a></li>
                        <li role='separator' class='divider'></li>
                <li><a href='#' data-toggle='modal' data-target='#newCourseModal'
                       class='newResourceLink' id='newCourseLink'>Course</a></li>
                <li><a href='#' data-toggle='modal' data-target='#newProfessorModal'
                       class='newResourceLink'id='newProfLink'>Professor</a></li>
                <li><a href='#' data-toggle='modal' data-target='#newClassroomModal'
                       class='newResourceLink' id='newClassroomLink'>Classroom</a></li>
                <li role='separator' class='divider'></li>
                <li><a href='#' data-toggle='modal' data-target='#newSemesterModal'
                       class='newResourceLink' id='newSemesterLink'>Semester</a></li>
                <li><a href='#' data-toggle='modal' data-target='#newBuildingModal'
                       class='newResourceLink' id='newBuildingLink'>Building</a></li>
                <li><a href='#' data-toggle='modal' data-target='#newCampusModal'
                       class='newResourceLink' id='newCampusLink'>Campus</a></li>
                <li role='separator' class='divider'></li>
                <li><a href='#' data-toggle='modal' data-target='#newDepartmentModal'
                       class='newResourceLink' id='newDepartmentLink'>Department</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class='container'>
      <div class='col-xs-12'>
        <div class='page-header'>
          <h1>Sections <small>for Spring 2017</small></h1>
        </div>
      </div>
    </div>
    <div class='container'>
      <div class='col-xs-12'>
        <table class='list-data'>
          <tr>
            <th colspan='3'>Course</th>
            <th>Professor</th>
            <th>Scheduled Time</th>
            <th>Location</th>
            <th>Actions</th>
          </tr>";
$allSections = $database->getAllSections(null);
foreach ($allSections as $section){
    $body .= addSection($section);
}
$body .= "</table>
<div id='calendar' ></div>
      </div>
    </div>
</div>

  </body>

</html>
";
function addSection(Section $section){
    $row = "<tr class='{$section->getSectionID()}'>
            <td>{$section->getSectionProperty('course_prefix', 'Course', 'course_id', 'courseID')}</td>"
        ."<td>{$section->getSectionProperty('course_number', 'Course', 'course_id', 'courseID')}</td>"
        ."<td> <i>{$section->getSectionProperty('course_title', 'Course', 'course_id', 'courseID')}</i></td>
            <td>{$section->getSectionProperty('prof_first', 'Professor', 'prof_id', 'profID')}"."
                {$section->getSectionProperty('prof_last', 'Professor', 'prof_id', 'profID')}<br>
                <small><em>{$section->getSectionProperty('prof_email', 'Professor', 'prof_id', 'profID')}</em></small>
            </td>
            <td><strong>{$section->getDayString()}:</strong>"."
            {$section->getStartTime()} - {$section->getEndTime()}<br/>
            <small><em>{$section->getBlock()}</em></small></td>
            <td><strong>
                {$section->getSectionProperty_Join_3('building_code', 'Classroom', 'Building',
                'classroom_id', 'building_id', 'classroomID')}"."
                {$section->getSectionProperty('classroom_number', 'Classroom', 'classroom_id', 'classroomID')}
                </strong><br/>
                <small>
                {$section->getSectionProperty_Join_4('campus_name', 'Classroom', 'Building', 'Campus',
                'classroom_id', 'building_id', 'campus_id', 'classroomID')}
                </small></td>
                <td><img src='img/pencil.png' class='action-edit'/><img src='img/close.png' class='action-delete'></td>
           </tr>";
    return $row;
}
echo $body;