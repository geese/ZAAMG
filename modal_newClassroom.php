<div class="modal fade" id="newClassroomModal" tabindex="-1" role="dialog" aria-labelledby="classroom-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="classroom-label">Create New Classroom</h4>
            </div>
            <div class="modal-body" style="margin-bottom: 75px;">
                <div class="form-group">
                    <div class="col-xs-2">
                        <label for="classroomCapacity">Capacity</label>
                        <input type="number" class="form-control" id="roomCapacity" value=30 >
                    </div>
                    <div class="col-xs-3">
                        <label for="classroomNumber">Room Number</label>
                        <input type="text" class="form-control" id="classroomNumber" >
                    </div>
                    <div class="col-xs-7">
                        <label for="classroomBuilding">Building/Campus</label>
                        <select type="text" class="form-control" id="classroomBuilding" >

                            <option value="0">Please Select...</option>
                            <option value="-1">None</option>

                            <?php
                            $database = new Database();
                            $selectBuilding = $database->getdbh()->prepare(
                                'SELECT ZAAMG.Campus.campus_id, campus_name, building_name, building_id
                                  FROM ZAAMG.Campus JOIN ZAAMG.Building
                                  ON ZAAMG.Campus.campus_id = ZAAMG.Building.campus_id
                                  ORDER BY building_name ASC');
                            $selectBuilding->execute();
                            $result = $selectBuilding->fetchAll();

                            foreach($result as $row){
                                echo "<option value=\"".$row['building_id']."\">"
                                    .$row['building_name'].", ".$row['campus_name']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <span class="error-message"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_insertClassroom">Save</button>
            </div>
        </div>
    </div>
</div>
