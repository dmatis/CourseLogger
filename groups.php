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

	<title>Student Records</title>

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
                    <a href="groups.php">Groups</a>
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
		<h1>Groups Page</h1>
    		</div>
	</div>
	</div>

<h3>Check Project Status:</h3>

<div class="container">
<form method="POST" action="groups.php">
<!--refresh page when submit-->

	<label>Task ID</label>
	<input type="text" name="taskid"><br /><br>
<input type="submit" value="Check Status" name="statussubmit"></p>
</form>
</div>

<?php
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
function printResult($result) { //prints results from a select statement
?>
	<table id="resultTable">
    	<thead>
        <tr>
            <th width="10%">Student Num</th>
            <th width="10%">Name</th>
            <th width="10%">Description</th>
            <th width="10%">Deadline</th>
            <th width="10%">Time Spent</th>
            <th width="10%">Complete?</th>
            <th width="10%">Grade</th>
        </tr>
    	</thead>
    	<tbody>
	<?php while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr>
        <td><?php echo $row["ID"]; ?></td>
        <td><?php echo $row["USERNAME"]; ?></td>
        <td><?php echo $row["DESCRIP"]; ?></td>
        <td><?php echo $row["DEADLINE"];  ?></td>
        <td><?php echo $row["TIME_SPENT"]; ?></td>
        <td><?php echo $row["COMPLETE"];  ?></td>
        <td><?php echo $row["GRADE"];  ?></td>
	</tr>
        <?php endwhile; ?>
    	</tbody>
	</table>
<?php
}

// Connect Oracle...
if ($db_conn) {
	if (array_key_exists('statussubmit', $_POST)) {
		$tuple = array (
			":bind1" => $_POST['taskid']
		);
		$result = executePlainSQL("select m.id, m.username, g.descrip, g.deadline, p.time_spent, p.complete, p.grade from performs p, group_project g, members m where g.task_id=".$_POST['taskid']." AND p.task_id=g.task_id AND p.id=g.id AND m.id=g.id", $tuple);
		printResult($result);
	}

	if ($_POST && $success) {
		header("location: groups.php");
	}
	
	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
//echo "hey"; this area works
?>
</div>
</div>

    <script>
	$(document).ready(function(){
	$(function(){
		$("#resultTable").tablesorter();
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
