<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../php_sessions'));
session_start();
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

	<title>Student Reports</title>

</head>
<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="index.php">
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
		<h1>Student Report</h1>
    	</div>
	<div class="row">
		<div class="col-lg-12">
		<p>Welcome: <?php echo $_SESSION['myid']; ?>
	</div>
    </div>

<?php

$success = True; //keep track of errors
$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");

function executePlainSQL($cmdstr) {
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);   
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement);
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;
}

function executeBoundSQL($cmdstr, $list) {

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
			unset ($val);

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement);
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}
}

function printTasks($result) { //prints the student's tasks
?>
<table id="reportTable" class="table" style="empty-cells:show">
    <thead>
        <tr>
            <th width="10%">Task ID</th>
            <th width="10%">Description</th>
            <th width="10%">Course Num</th>
            <th width="10%">Dept</th>
            <th width="10%">Deadline</th>
            <th width="10%">Completed?</th>
            <th width="10%">Grade</th>
            <th width="10%">Time Spent</th>
        </tr>
    </thead>
    <tbody>
        <?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr>
        <td><?php echo $row["TASK_ID"]; ?></td>
        <td><?php echo $row["DESCRIP"]; ?></td>
        <td><?php echo $row["COURSE_DEPT"];  ?></td>
        <td><?php echo $row["COURSE_NUM"]; ?></td>
        <td><?php echo $row["DEADLINE"];  ?></td>
        <td><input type="checkbox" <?php if ($row["COMPLETED"] == 'Y') echo "checked='checked'"; ?></td>
        <td><?php echo $row["GRADE"]; ?></td>
        <td><?php echo $row["TIME_SPENT"]; ?></td>
	</tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('deletetask', $_POST)) {
		//$tuple = array (
		//		":bind1" => $_POST['id'],
		//	);
		//	$alltuples = array (
		//		$tuple
		//	);
		// executeBoundSQL("delete from assignment where id=:bind1", $alltuples);
		//$query = "delete from assignment where id={$_POST['id']}";
		//mysql_query($query);
		// OCICommit($db_conn);
	}

	if ($_POST && $success) {
		header("location: studreport.php");
	} else {
		// Select data...
		$tasks = executePlainSQL("select t.task_id, t.descrip, t.course_dept, t.course_num, t.deadline, p.completed, p.grade, p.time_spent from task t, student s, performs p where s.stid='".$_SESSION[myid]."' AND p.stid=s.stid AND t.task_id=p.task_id");
		printTasks($tasks);
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
?>
</div>
</div>

     <script>
	 $(document).ready(function(){
	 $(function(){
	 	$("#reportTable").tablesorter();
	 	});
	 });
     </script>

    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>
</html>
