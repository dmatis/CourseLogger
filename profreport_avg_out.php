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

	<title><?php 
		if (isset($_POST["maxavg"])) {
			echo "Maximum Average Time Spent in ".$_POST["course_dept"]." ".$_POST["course_num"].": ";
		}
		else if (isset($_POST["minavg"])) {
			echo "Minimum Average Time Spent in ".$_POST["course_dept"]." ".$_POST["course_num"].": ";
		}
			?></title>

</head>
<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="profindex.php">
                        HOME
                    </a>
                </li>
                <li>
                    <a href="profreport.php">Reports</a>
                </li>
                <li>
                    <a href="groups.php">Groups</a>
                </li>
                <li>
                    <a href="tasksbycourse.php">Search</a>
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
		<h3><?php 
		if (isset($_POST["maxavg"])) {
			echo "Maximum average time spent on tasks for ".$_POST["course_dept"]." ".$_POST["course_num"].": ";
		}
		else if (isset($_POST["minavg"])) {
			echo "Minimum average time spent on tasks for ".$_POST["course_dept"]." ".$_POST["course_num"].": ";
		}
			?></h3>
    	</div>
	</div>
	</div>

<?php
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

function printAllComplete($query, $course_dept, $course_num) { //prints results from a select statement
?>
	<div class="container" id="complete_students">
		<?php 
		while ($row = OCI_Fetch_Array($query, OCI_BOTH)) : ?>
		<li><?php echo "Task # ".$row["TASK_ID_RESULT"];?><?php echo ", ".$row["DESCRIP_RESULT"]. ", ";?> <?php echo "with ". $row["AVGTIME_RESULT"]. " hours";?></li>
		<?php endwhile; ?>
	</div>
<?php
}

$course_dept = $_POST['course_dept'];
$course_num = $_POST['course_num'];

// Displays $_POST data
// print "CONTENT_TYPE: " . $_SERVER['CONTENT_TYPE'] . "<BR />";
// $data = file_get_contents('php://input'); print "DATA: <pre>";
// var_dump($data);
// var_dump($_POST);
// print "</pre>";

$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");

// Alter the query here
executePlainSQLForDrop("drop table task_average");
executePlainSQL("create table task_average as select task_id, avg(time_spent) as avgtime from (select p.task_id, p.time_spent from (select task_id, time_spent from performs union all select task_id, time_spent from group_performs) p, task t where p.task_id = t.task_id and t.course_dept='$course_dept' and t.course_num='$course_num') group by task_id");
$query;
if (isset($_POST["maxavg"])) {
	$query = executePlainSQL("select t.task_id as task_id_result, t.descrip as descrip_result, ta.avgtime as avgtime_result from task_average ta, task t where ta.task_id = t.task_id and ta.avgtime = (select max(avgtime) from task_average)");
}
else if (isset($_POST["minavg"])) {
	$query = executePlainSQL("select t.task_id as task_id_result, t.descrip as descrip_result, ta.avgtime as avgtime_result from task_average ta, task t where ta.task_id = t.task_id and ta.avgtime = (select min(avgtime) from task_average)");
}
//$query = executePlainSQL("select t.task_id, t.descrip from task_average ta, task t where ta.task_id = t.task_id and ta.avgtime = (select max(avgtime) from task_average)");

// Need to handle cases where there are no tasks
// Should reflect average time spent, not average grade
// Display min/max average
printAllComplete($query, $course_dept, $course_num);
//$average = "0 hours";
//?><h3><?php echo $average;?></h3><?php

OCICommit($db_conn);

?>
<div class="row" style="height:50px"></div>
<div class="container">
	<button class="btn" onclick="goBack()">Go Back</button>
	<script>
	function goBack() {
	    window.history.back();
	}
	</script>
</div>
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
