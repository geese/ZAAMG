<?php
require_once 'Section.php';

class Database
{
    private $host = "localhost";
    private $dbname  = "zaamg";
    private $username = "zaamg";
    private $dbh; //let's not expose the database

    public function __construct() {

        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname:$this->dbname", $this->username);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            #echo "<br>Success creating Database Object<br>";
        } catch (PDOException $e) {
            echo "Error creating Database Object";
            die(); //if the database fails, don't go on.
        }
    }


    /*  Returns:        an array of Section objects, one per Section record in database
     *  Args:
     *      $orderBy:   might need this for sorting the Sections different ways
     */
    public function getAllSections($orderBy){
        $allSections = [];
        $dbh = $this->getdbh();
        $stmtSelect = $dbh->prepare("SELECT * FROM ZAAMG.Section");
        try{
            $stmtSelect->execute();
            $result = $stmtSelect->fetchAll();
            foreach($result as $index=>$sectionRecord){
                $allSections[] = new Section(  //don't need to put an index number between those brackets, awesome
                    $sectionRecord['section_id'], $sectionRecord['course_id'], $sectionRecord['prof_id'], $sectionRecord['classroom_id'],
                    $sectionRecord['sem_id'],$sectionRecord['section_days'], $sectionRecord['section_start_time'], $sectionRecord['section_end_time'],
                    $sectionRecord['section_block'], $sectionRecord['section_capacity']);
            }
            return $allSections;
        }catch(Exception $e){
            echo "getAllSections: ".$e->getMessage();
        }
    }



    public function getAllProfessors($orderBy){
        $allProfessors = [];
        $dbh = $this->getdbh();
        $stmtSelect = $dbh->prepare("SELECT * FROM ZAAMG.Professor");
        try{
            $stmtSelect->execute();
            $result = $stmtSelect->fetchAll();
            foreach($result as $index=>$profRecord){
                $allProfessors[] = new Professor(  //don't need to put an index number between those brackets, awesome
                    $profRecord['prof_id'], $profRecord['prof_first'], $profRecord['prof_last'],
                    $profRecord['prof_email'],
                    $profRecord['prof_req_hours'],$profRecord['prof_rel_hours'],
                    $profRecord['dept_id']);
            }
            return $allProfessors;
        }catch(Exception $e){
            echo "getAllProfessors: ".$e->getMessage();
        }
    }




    public function getAllClassrooms($orderBy){
        $allClassrooms = [];
        $dbh = $this->getdbh();
        $stmtSelect = $dbh->prepare("SELECT * FROM ZAAMG.Classroom");
        try{
            $stmtSelect->execute();
            $result = $stmtSelect->fetchAll();
            foreach($result as $index=>$classroomRecord){
                $allClassrooms[] = new Classroom(  //don't need to put an index number between those brackets, awesome
                    $classroomRecord['classroom_id'], $classroomRecord['classroom_number'],
                    $classroomRecord['classroom_capacity'],
                    $classroomRecord['classroom_workstations'],
                    $classroomRecord['building_id']);
            }
            return $allClassrooms;
        }catch(Exception $e){
            echo "getAllClassrooms: ".$e->getMessage();
        }
    }

    public function getProfSections($prof, $orderBy){
        $profSections = [];
        $dbh = $this->getdbh();
        $stmtSelect = $dbh->prepare("SELECT * FROM ZAAMG.Section
                                      WHERE prof_id = {$prof->getProfId()}");
        try{
            $stmtSelect->execute();

            $result = $stmtSelect->fetchAll();
            foreach($result as $index=>$sectionRecord){
                $profSections[] = new Section(  //don't need to put an index number between those brackets, awesome
                    $sectionRecord['section_id'],
                    $sectionRecord['course_id'],
                    $sectionRecord['prof_id'],
                    $sectionRecord['classroom_id'],
                    $sectionRecord['sem_id'],
                    $sectionRecord['section_days'],
                    $sectionRecord['section_start_time'],
                    $sectionRecord['section_end_time'],
                    $sectionRecord['section_block'],
                    $sectionRecord['section_capacity']);
            }
            return $profSections;
        }catch(Exception $e){
            echo "getProfSections: ".$e->getMessage();
        }
    }

    public function getCourse($section){
        $theCourse = null;
        $dbh = $this->getdbh();
        $stmtSelect = $dbh->prepare("SELECT * FROM ZAAMG.Course
                                      WHERE course_id = {$section->getCourseID()}");
        try{
            $stmtSelect->execute();

            $courseRecord = $stmtSelect->fetchAll()[0];

                $theCourse = new Course(  //don't need to put an index number between those brackets, awesome
                    $courseRecord['course_id'],
                    $courseRecord['course_prefix'],
                    $courseRecord['course_number'],
                    $courseRecord['course_title'],
                    $courseRecord['course_credits'],
                    $courseRecord['dept_id']);

            return $theCourse;
        }catch(Exception $e){
            echo "getProfSections: ".$e->getMessage();
        }
    }


    
    public function getdbh(){
        return $this->dbh;
    }
}
