<!-- Modal -->
<div class="modal fade" id="newCourseModal" tabindex="-1" role="dialog" aria-labelledby="course-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="course-label">Create New Course</h4>
            </div>
            <div class="modal-body" id="id_form-group" style="margin-bottom: 150px;">

                <div class="form-group" >
                    <div class="col-xs-4">
                        <label for="courseCode">Code</label>
                        <input type="number" class="form-control" id="courseCode" placeholder="1000" >
                    </div>
                    <div class="col-xs-8">
                        <label for="courseTitle">Title</label>
                        <input type="text" class="form-control" id="courseTitle" placeholder="Course Title Here..." >
                    </div>

                </div>
                <div class="form-group">
                    <div class="col-xs-2">
                        <label for="courseCapacity">Capacity</label>
                        <input type="number" class="form-control" id="courseCapacity" value=30 >
                    </div>
                    <div class="col-xs-2">
                        <label for="courseCredits">Credits</label>
                        <input type="number" class="form-control" id="courseCredits" value=4 >
                    </div>
                    <div class="col-xs-8">
                        <label for="courseDepartment">Department</label>
                        <select class="form-control" id="courseDepartment" >
                            <option value="''" >Please Select...</option>

                            <?php
                            $database = new Database();
                            $selectDepts = $database->getdbh()->prepare(
                                'SELECT dept_id, dept_name FROM ZAAMG.Department
                        ORDER BY dept_name ASC');
                            $selectDepts->execute();
                            $result = $selectDepts->fetchAll();

                            foreach($result as $row){
                                echo "<option value=\"".$row['dept_id']."\">".$row['dept_name']."</option>";
                            }
                            ?>
                            <!--<option value="0">Computer Science</option>
                            <option value="1">Network, Multimedia and Technology</option>
                            <option value="2">Web Development</option>-->
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <span class="error-message"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_insertCourse">Save</button>
            </div>
        </div>
    </div>
</div>