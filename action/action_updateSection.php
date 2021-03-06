<?php

require_once '../Database.php';
require_once '../Section.php';


$sectionId = isset($_POST['sectionId']) ? $_POST['sectionId'] : "not entered";
$sectionCourse = isset($_POST['sectionCourse']) ? $_POST['sectionCourse'] : "not entered";
$sectionProfessor = isset($_POST['sectionProfessor']) ? $_POST['sectionProfessor'] : "not entered";
$sectionClassroom = isset($_POST['sectionClassroom']) ? $_POST['sectionClassroom'] : "not entered";
$sectionDays = isset($_POST['sectionDays']) ? $_POST['sectionDays'] : "not entered";
$sectionStartTime = isset($_POST['sectionStartTime']) ? $_POST['sectionStartTime'] : "not entered";
$sectionEndTime = isset($_POST['sectionEndTime']) ? $_POST['sectionEndTime'] : "not entered";
$sectionIsOnline = isset($_POST['sectionIsOnline']) ? $_POST['sectionIsOnline'] : "not entered";
$sectionBlock = isset($_POST['sectionBlock']) ? $_POST['sectionBlock'] : "not entered";
$sectionCapacity = isset($_POST['sectionCapacity']) ? $_POST['sectionCapacity'] : "not entered";
$sectionSemester = isset($_POST['sectionSemester']) ? $_POST['sectionSemester'] : "not entered";
$action = isset($_POST['action']) ? $_POST['action'] : "not entered";

foreach ($_POST as $item){
    strip_tags($item);
}

$sectionStartTime = $sectionIsOnline == 1 ? "00:00:00" : $sectionStartTime;
$sectionEndTime = $sectionIsOnline == 1 ? "00:00:00" : $sectionEndTime;

$database = new Database();
$dbh = $database->getdbh();

$message = "";


if ($action == "update"){
    $updateStmt = $dbh->prepare(
        "  UPDATE W01143557.Section
        SET course_id           = $sectionCourse,
            prof_id             = $sectionProfessor,
            classroom_id        = $sectionClassroom,
            sem_id              = $sectionSemester,
            section_days        = '$sectionDays',
            section_start_time  = '$sectionStartTime',
            section_end_time    = '$sectionEndTime',
            section_is_online   = $sectionIsOnline,
            section_block       = $sectionBlock,
            section_capacity    = $sectionCapacity
        WHERE section_id = $sectionId");
    try{
        $updateStmt->execute();
        $message = "success";
    }catch(Exception $e){
        $message = "action_updateSection: ".$e->getMessage();
        echo $message;
    }
}else if ($action == "delete"){
    $deleteStmt = $dbh->prepare(
        "  DELETE FROM W01143557.Section
        WHERE section_id = $sectionId
    ");

    try{
        $deleteStmt->execute();
        $message = "success";
    }catch(Exception $e){
        $message = "action_deleteSection: ".$e->getMessage();
    }
}


$selectSections = $dbh->prepare(
    "SELECT * FROM W01143557.Section");
$selectSections->execute();
$sections = $selectSections->fetchAll();
$sections_json = [];

foreach($sections as $section){
    $sections_json[] = array(
        'id'=>$section['section_id'],
        'course'=>$section['course_id'],
        'prof'=>$section['prof_id'],
        'room'=>$section['classroom_id'],
        'sem'=>$section['sem_id'],
        'days'=>$section['section_days'],
        'start'=>$section['section_start_time'],
        'end'=>$section['section_end_time'],
        'online'=>$section['section_is_online'],
        'block'=>$section['section_block'],
        'cap'=>$section['section_capacity']
    );
};



echo json_encode($sections_json);  // to use in refreshing dropdown lists inside modals
