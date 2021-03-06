<?php

require_once 'Database.php';

class Building {
    private $database;

    private $buildingID;
    private $campusID;
    private $buildCode;
    private $buildName;

    # removed types from formal arguments, don't think they're necessary
    //they aren't necessary, but they do help with back-end error checking
    public function __construct($buildingID, $buildingCode, $buildingName, $campusID) {
        $this->buildingID = $buildingID;
        $this->buildCode = $buildingCode;
        $this->buildName = $buildingName;
        $this->campusID = $campusID;

        $this->database = new Database();
    }

    public function getBuildingID() : int {
        return $this->buildingID;
    }

    public function getBuildingName(){
        return $this->buildName;
    }

    public function getBuildingCode(){
        return $this->buildCode;
    }

    public function getCampusID(){
        return $this->campusID;
    }

    public function insertNewBuilding(){
        $dbh = $this->database->getdbh();
        $stmtInsert = $dbh->prepare("INSERT INTO W01143557.Building VALUES (:id, :code, :buildName, :campusID)");
        # send NULL for building_id because the database auto-increments it
        $stmtInsert->bindValue("id", NULL);
        $stmtInsert->bindValue(":code", $this->buildCode);
        $stmtInsert->bindValue(":buildName", $this->buildName);
        $stmtInsert->bindValue(":campusID", $this->campusID);

        try {
            $stmtInsert->execute();
            echo "Success executing Insert";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    public function buildingExists($buildName, $buildCode, $campusId){
        $dbh = $this->database->getdbh();
        $stmtSelect = $dbh->prepare(
            "SELECT building_id FROM W01143557.Building
              WHERE campus_id = $campusId
              AND (building_code = ".$dbh->quote($buildCode)." OR building_name = ".$dbh->quote($buildName).")");

        try {
            $stmtSelect->execute();
            $result = $stmtSelect->fetch(PDO::FETCH_ASSOC);
            if ($result != NULL) {
                return "does exist";
            }else {
                return "does not exist";
            }
        } catch (Exception $e) {
            echo "Here's what went wrong: ".$e->getMessage();
            return "buildingExists failed!";
        }
    }
}
