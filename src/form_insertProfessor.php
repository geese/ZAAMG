
<!--
The user needs the Department ID to fill out the Professor form.
The Department Id is assigned by the database,
so the following table will show the existing Department records
to the user, including the correct Department IDs.

Editing to add:  another way is to provide a drop-down menu,
so that the user can select a department code without having
to know the id number used in the database.
-->
<table>
 <tr>
  <!--<th>Department Id</th>--><th>Department Code</th><th>Department Name</th>
 </tr>
 <tr>    <!--open <tr> tag which will continue with a php echo-->


  <?php
  include 'Department.php';      # so php can make Department objects with Database results
  require_once 'Database.php';
  $database = new Database();

  $selectAll = $database->dbh->prepare('SELECT * FROM ZAAMG.Department ORDER BY ZAAMG.Department.dept_code ASC');
  $selectAll->execute();

  /* This line takes the query result and makes an array of Department objects,
   * one object per row.
   * http://php.net/manual/en/pdostatement.fetchall.php
   */http://stackoverflow.com/questions/29805097/php-constructing-a-class-with-pdo-warning-missing-argument
  $result = $selectAll->fetchAll(PDO::FETCH_CLASS, "Department",
      array('id','code','name'));


  #continuing the Department display table...
  foreach ($result as $row){
   echo #"<td>".$row->dept_id."</td>".
       "<td>".$row->dept_code."</td>"
       ."<td>".$row->dept_name."</td>"
       ."</tr>";  #close table row
  }
  #close table tag
  echo "</table>";
  echo "</br>";

  # var_dump($result);    # use this to see the attributes of the object


  echo "</br>";
  ?>


  <!--  Here is the Insert Professor form:  -->



<form action="action_insertProfessor.php" method="post">
 <p>Professor First: <input type="text" name="profFirst" /></p>
 <p>Professor Last: <input type="text" name="profLast" /></p>
 <p>Professor Email: <input type="email" name="profEmail" required="required"/></p>
 <!--<p>Department Id: <input type="text" name="deptId" /></p>-->

<!-- changed from a simple input field, (commented line above) to a dropdown menu -->

 <p>Department ID: <select name="deptId">

   <?php
   foreach ($result as $row) {
    echo "<option value=\"" . $row->dept_id . "\">" .$row->dept_code."</option>";
   }
    ?>

  </select></p>

 <p><input type="submit" /></p>
</form>