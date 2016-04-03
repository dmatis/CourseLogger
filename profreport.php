<!DOCTYPE HTML>
<html>
<head>

	<!-- Bootstrap Core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="css/simple-sidebar.css" rel="stylesheet">
	<!-- jQuery -->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>	
	<!-- Sortable Table Script -->
	<script type="text/javascript" src="jquery.tablesorter.min.js"></script>	

	<title>Professor Report</title>

</head>
<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        HOME
                    </a>
                </li>
                <li>
                    <a href="#">Reports</a>
                </li>
                <li>
                    <a href="#">Add Time</a>
                </li>
                <li>
                    <a href="#">Overview</a>
                </li>
                <li>
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->


    <div id="page-content-wrapper">
	<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
		<h1>Course Report</h1>
    	</div>
	</div>
	</div>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_v7t8", "a35176114", "ug");

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn); // For OCIParse errors pass the       
		// connection handle
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;
}

function executePlainSQLForDrop($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		$success = False;
	} else {
		$statement = False;
	}
	return $statement;
}

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes a same statement will be excuted for severl times, only
	 the value of variables need to be changed.
	 In this case you don't need to create the statement several times; 
	 using bind variables can make the statement be shared and just 
	 parsed once. This is also very useful in protecting against SQL injection. See example code below for
	 how this functions is used */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}
}

function printCourses($result) { //prints results from a select statement
?>
<table id="reportTable" class="table" style="table-layout:fixed;empty-cells:show">
    <thead>
        <tr>
            <th width="10%">Course</th>
            <th width="2%" style="text-align:right">Average</th>
            <th width="5%" style="text-align:right">Completion rate</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody>
        <?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) :
    $course_dept = $row["COURSE_DEPT"];
    $course_num = $row["COURSE_NUM"];
    ?>
	<tr style="background-color:#ddd">
        <td style="font-weight:600">
        	<?php echo $course_dept;?> <?php echo $course_num; ?>:
        </td>
        <td></td>
        <td></td>
        <td>
        	<div class="col-md-offset-2 col-md-3">
	        	<form method="POST" action="profreport_avg_out.php">
					<input type="hidden" name="course_dept" value="<?php echo $course_dept;?>">
					<input type="hidden" name="course_num" value="<?php echo $course_num;?>">
					<input class="btn btn-xs btn-success" type="submit" value="Min. Average" name="minavg">
				</form>
			</div>
			<div class="col-md-3">
				<form method="POST" action="profreport_avg_out.php">
					<input type="hidden" name="course_dept" value="<?php echo $course_dept;?>">
					<input type="hidden" name="course_num" value="<?php echo $course_num;?>">
					<input class="btn btn-xs btn-success" type="submit" value="Max. Average" name="maxavg">
				</form>
			</div>
			<div class="col-md-3">
				<form method="POST" action="profreport_out.php">
					<input type="hidden" name="ac_course_dept" value="<?php echo $course_dept;?>">
					<input type="hidden" name="ac_course_num" value="<?php echo $course_num;?>">
					<input class="btn btn-xs btn-success" type="submit" value="Completed By" name="allcomplete">
				</form>
			</div>
        </td>
        <!-- <td align="right">
        	<form method="POST" action="profreport.php">
				<input type="hidden" name="min_course_dept" value="<?php echo $course_dept;?>">
				<input type="hidden" name="min_course_num" value="<?php echo $course_num;?>">
				<input class="btn btn-xs btn-success" type="submit" value="Get min. average" name="minavg">
			</form>
		</td>
        <td align="right" style="width:80px">
        	<form method="POST" action="profreport.php">
				<input type="hidden" name="max_course_dept" value="<?php echo $course_dept;?>">
				<input type="hidden" name="max_course_num" value="<?php echo $course_num;?>">
				<input class="btn btn-xs btn-success" type="submit" value="Get max. average" name="maxavg">
			</form>
        </td>
        <td>
        	<form method="POST" action="profreport_out.php">
				<input type="hidden" name="ac_course_dept" value="<?php echo $course_dept;?>">
				<input type="hidden" name="ac_course_num" value="<?php echo $course_num;?>">
				<input class="btn btn-xs btn-success" type="submit" value="Completed by" name="allcomplete">
			</form>
		</td> -->
	</tr>
    <?php 
    $tasks = executePlainSQL("select task_id, descrip from task where course_dept='$course_dept' and course_num='$course_num'");
    printTasks($tasks);
    ?>
    <?php endwhile; ?>
    </tbody>
</table>
<?php
}

function printTasks($result) { //prints results from a select statement
?><?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr>
        <td style="padding-left:20px"><?php echo $row["DESCRIP"]; ?></td>
        <?php
        $task_id = $row['TASK_ID'];
	    $grade_avg = executePlainSQL("select round(avg(grade),2) as avg_grade from performs where task_id=".$task_id);
	    $complete_count = executePlainSQL("select count(task_id) as complete_count from performs where completed='Y' and task_id=".$task_id);
	    $total_count = executePlainSQL("select count(task_id) as total_count from performs where task_id=".$task_id);
	    printAverage($grade_avg);
	    printCRate($complete_count, $total_count);
	    ?>
        <td>
    		<div class="col-md-offset-7 col-md-2">
	        	<form method="POST" action="profreport.php">   
					<p><input class="btn btn-xs btn-default" type="submit" value="Update" name="updatetask"></p>
				</form>
			</div>
			<div class="col-md-2">
				<form method="POST" action='profreport.php'>
					<input type="hidden" name='task_id' value="<?php echo $task_id?>">
					<input class="btn btn-xs btn-danger" type="submit" value="Delete" name="deletetask">
				</form>
			</div>
        </td>
	</tr>
    <?php endwhile; ?>
<?php
}

function printAverage($avg_result) { //prints results from a select statement
?><?php
	$avg = OCI_Fetch_Array($avg_result, OCI_BOTH);
	if (is_numeric($avg['AVG_GRADE'])) {
		?>
		<td align="right"><?php echo $avg['AVG_GRADE']; ?></td>
		<?php	
	}
	else {
		?>
		<td align="right">-</td>
		<?php
	}
	?>
<?php
}

function printCRate($complete_count_result, $total_count_result) { //prints results from a select statement
?><?php
	$c_count = OCI_Fetch_Array($complete_count_result, OCI_BOTH);
	$t_count = OCI_Fetch_Array($total_count_result, OCI_BOTH);
	if ($t_count['TOTAL_COUNT'] > 0) {
		?>
		<td align="right"><?php echo $c_count['COMPLETE_COUNT']; ?>/<?php echo $t_count['TOTAL_COUNT']; ?></td>
		<?php
	}
	else {
		?>
		<td align="right">Not assigned</td>
		<?php
	}
	?>
<?php
}

// print "CONTENT_TYPE: " . $_SERVER['CONTENT_TYPE'] . "<BR />";
// $data = file_get_contents('php://input'); print "DATA: <pre>";
// var_dump($data);
// var_dump($_POST);
// print "</pre>";

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('deletetask', $_POST)) {
		$tuple = array (
				":bind1" => $_POST['task_id'],
			);
			$alltuples = array (
				$tuple
			);
		executeBoundSQL("delete from task where task_id=:bind1", $alltuples);
		OCICommit($db_conn);
	}

	else if (array_key_exists('allcomplete', $_POST)) {

		$ac_course_dept = $_POST['ac_course_dept'];
		$ac_course_num = $_POST['ac_course_num'];

		executePlainSQLForDrop("drop table performs_each");
		executePlainSQLForDrop("drop table performs_each_group");
		executePlainSQLForDrop("drop table performs_total");

		executePlainSQL("create table performs_each as select stid 
			from performs 
			where task_id in (select task_id from task where course_num='$ac_course_num' and course_dept='$ac_course_dept') and completed='Y' 
			group by stid having count(*) = (select count(*) 
		                                 from task 
		                                 where course_num='$ac_course_num' and course_dept='$ac_course_dept')");

		executePlainSQL("create table performs_each_group as select stid
			from group_performs 
			where task_id in (select task_id from task where course_num='$ac_course_num' and course_dept='$ac_course_dept') and completed='Y' 
			group by stid having count(*) = (select count(*) 
		                                 from task 
		                                 where course_num='$ac_course_num' and course_dept='$ac_course_dept')");
		                    
		executePlainSQL("create table performs_total as select performs_each.stid 
			from performs_each
			left join performs_each_group
			on performs_each.stid=performs_each_group.stid");

		$all_complete = executePlainSQL("select fname, lname
			from student S, performs_total PT
			where S.stid = PT.stid");

		printAllComplete($all_complete, $ac_course_dept, $ac_course_num);
		OCICommit($db_conn);
	}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: profreport.php");
	} else {
		// Select data...
		$courses = executePlainSQL("select course_dept, course_num from course_teach order by course_dept, course_num");
		printCourses($courses);
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

/* OCILogon() allows you to log onto the Oracle database
     The three arguments are the username, password, and database
     You will need to replace "username" and "password" for this to
     to work. 
     all strings that start with "$" are variables; they are created
     implicitly by appearing on the left hand side of an assignment 
     statement */

/* OCIParse() Prepares Oracle statement for execution
      The two arguments are the connection and SQL query. */
/* OCIExecute() executes a previously parsed statement
      The two arguments are the statement which is a valid OCI
      statement identifier, and the mode. 
      default mode is OCI_COMMIT_ON_SUCCESS. Statement is
      automatically committed after OCIExecute() call when using this
      mode.
      Here we use OCI_DEFAULT. Statement is not committed
      automatically when using this mode */

/* OCI_Fetch_Array() Returns the next row from the result data as an  
     associative or numeric array, or both.
     The two arguments are a valid OCI statement identifier, and an 
     optinal second parameter which can be any combination of the 
     following constants:

     OCI_BOTH - return an array with both associative and numeric 
     indices (the same as OCI_ASSOC + OCI_NUM). This is the default 
     behavior.  
     OCI_ASSOC - return an associative array (as OCI_Fetch_Assoc() 
     works).  
     OCI_NUM - return a numeric array, (as OCI_Fetch_Row() works).  
     OCI_RETURN_NULLS - create empty elements for the NULL fields.  
     OCI_RETURN_LOBS - return the value of a LOB of the descriptor.  
     Default mode is OCI_BOTH.  */
?>
</div>
</div>

 //    <script>
	// $(document).ready(function(){
	// $(function(){
	// 	$("#reportTable").tablesorter();
	// 	});
	// });
 //    </script>

    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>
</html>
