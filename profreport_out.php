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

	<title>All Tasks Complete </title>

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
		<h3><?php echo "Students who have finished all tasks in ".$_POST["ac_course_dept"]." ".$_POST["ac_course_num"].": "?></h3>
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

function printAllComplete($all_complete, $course_dept, $course_num) { //prints results from a select statement
?>
	<div class="container" style="display:hidden" id="no_students">
		<p><?php echo "No students have completed all tasks in ".$_POST["ac_course_dept"]." ".$_POST["ac_course_num"]."."?></p>
	</div>
	<div class="container" id="complete_students">
		<?php 
		while ($row = OCI_Fetch_Array($all_complete, OCI_BOTH)) : ?>
		<li><?php echo $row["FNAME"];?> <?php echo $row["LNAME"];?></li>
		<?php endwhile; ?>
	</div>
	<script>
	if ($("#complete_students").has("li").length == 0) {
		$("#no_students").show();
	}
	else{
		$("#no_students").hide();
	}
	</script>
<?php
}

$ac_course_dept = $_POST['ac_course_dept'];
$ac_course_num = $_POST['ac_course_num'];

// print "CONTENT_TYPE: " . $_SERVER['CONTENT_TYPE'] . "<BR />";
// $data = file_get_contents('php://input'); print "DATA: <pre>";
// var_dump($data);
// var_dump($_POST);
// print "</pre>";

$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");

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

?>
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
