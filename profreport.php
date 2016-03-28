<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../php_sessions'));
session_start();

if(!isset($_SESSION['myusername'])){
	header("location:main_login.php");
}
?>

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

	<title>Assignment Records</title>

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
		<!-- <p class="lead">Reset the Assignment Table</p>
		<form method="POST" action="querytask.php">   
		<p><input class="btn btn-lg btn-success" type="submit" value="Reset" name="reset"></p>
		</form> -->
    	</div>
	</div>
	</div>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");

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
<table id="reportTable" class="table" style="empty-cells:show">
    <thead>
        <tr>
            <th width="10%">Course</th>
            <th width="10%" style="text-align:right">Average</th>
            <th width="10%" style="text-align:right">Completion rate</th>
            <th width="5%"></th>
            <th width="5%"></th>
        </tr>
    </thead>
    <tbody>
        <?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr style="background-color:#ddd">
        <td style="font-weight:600"><?php echo $row["COURSE_DEPT"];?> <?php echo $row["COURSE_NUM"]; ?>:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
	</tr>
    <?php 
    $course_dept = $row["COURSE_DEPT"];
    $course_num = $row["COURSE_NUM"];
    $assignments = executePlainSQL("select id, description from assignment where course_dept='$course_dept' and course_num='$course_num'");
    printAssignments($assignments);
    ?>
    <?php endwhile; ?>
    </tbody>
</table>
<?php
}

function printAssignments($result) { //prints results from a select statement
?><?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<?php $task_id = $row['id']; ?>
	<tr>
        <td style="padding-left:20px"><?php echo $row["DESCRIPTION"]; ?></td>
        <td align="right">80</td>
        <td align="right">117/120</td>
        <td align="right">
        	<form method="POST" action="profreport.php">   
			<p><input class="btn btn-xs btn-default" type="submit" value="Update" name="updatetask"></p>
			</form>
		</td>
		<td>
			<form method="POST" action='profreport.php?id="<?php echo $row['id']; ?>"'>
				<input type="hidden" value="<?php echo $row['id']?>" name="deletetask">
				<input class="btn btn-xs btn-danger" value="Delete" name="deletetask" type="submit">
			</form>
        </td>
		<?php
	    $grade_sum = executePlainSQL("select sum(grade) as sum_grade from performs where task_id='$task_id'");
	    $student_count = executePlainSQL("select count(id) as student_count from performs where task_id='$task_id'");
	    // printAverage($grade_sum, $student_count);
	    ?>
	</tr>
    <?php endwhile; ?>
<?php
}

function printAverage($sum_result, $count_result) { //prints results from a select statement
?><?php
	$sum = OCI_Fetch_Array($sum_result);
	$count = OCI_Fetch_Array($count_result);
	?>
    <td><?php echo $sum['sum_grade']; ?></td>
<?php
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('deletetask', $_POST)) {
		$tuple = array (
				":bind1" => $_POST['id'],
			);
			$alltuples = array (
				$tuple
			);
		// executeBoundSQL("delete from assignment where id=:bind1", $alltuples);
		$query = "delete from assignment where id={$_POST['id']}";
		mysql_query($query);
		// OCICommit($db_conn);
	}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: profreport.php");
	} else {
		// Select data...
		$courses = executePlainSQL("select course_dept, course_num from course_teach");
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
