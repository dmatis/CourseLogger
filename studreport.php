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
		<h1>Student Report</h1>
    	</div>
	<div class="row">
		<div class="col-lg-12">
		<p>Welcome: <?php echo $_SESSION['myid']; ?>
	</div>
    </div>

<!--<form action="studreport.php" method="post">
    <input type="text" name="darren">
    <input type="submit" name="update">
</form>
-->
<h1><?php echo $_POST['darren'] ?></h1>


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
		
		$e = oci_error($statement);
		echo "<script>alert('The following error occured: " . $e['message'] . "');</script>";
		//echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		//echo "<script>alert('failure');</script>";
		//echo htmlentities($e['message']);
		$success = False;
	}
	return $statement;
}

//Connect Oracle...
if ($db_conn) {
	if (array_key_exists('submit', $_POST)) {
		$arr_completed = $_POST['checkedTasks'];
		foreach ($arr_completed as $compTask){
			executePlainSQL("update performs set completed='Y' where task_id='".$compTask."' and stid='".$_SESSION['myid']."'");
		}
		for ($i = 0; $i < count($_POST['timeSpent']); $i++){
			executePlainSQL("update performs set time_spent=".$_POST['timeSpent'][$i]." where task_id='".$_SESSION['IDS'][$i]."'");
		}	
		OCICommit($db_conn);
	}
	$tasks = executePlainSQL("select t.task_id, t.descrip, t.course_dept, t.course_num, t.deadline, p.completed, p.grade, p.time_spent from task t, student s, performs p where s.stid='".$_SESSION[myid]."' AND p.stid=s.stid AND t.task_id=p.task_id");

	?>
    <div class="container">
    <form method="post" action="studreport.php">
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
	<?php $_SESSION['IDS'] = array(); ?>
	<?php while ($row = OCI_Fetch_Array($tasks, OCI_BOTH)) : ?>
	<?php array_push($_SESSION['IDS'], $row["TASK_ID"]); ?>
	<tr>
        <td><?php echo $row["TASK_ID"]; ?></td>
        <td><?php echo $row["DESCRIP"]; ?></td>
        <td><?php echo $row["COURSE_DEPT"];  ?></td>
        <td><?php echo $row["COURSE_NUM"]; ?></td>
        <td><?php echo $row["DEADLINE"];  ?></td>
        <!--<input type="hidden" name="checkedTasks[]" value='N'>-->
	<td><?php if ($row["COMPLETED"] == 'Y') echo "<input type='checkbox' name='checkedTasks[]' checked='checked' value='".$row["TASK_ID"]."'<br>"; else echo "<input type='checkbox' name='checkedTasks[]' value='".$row["TASK_ID"]."'<br>"; ?></td>
        <td><?php echo $row["GRADE"]; ?></td>
        <td><input type="number" name="timeSpent[]" <?php echo "id=" . $row["TASK_ID"] . " value=" . $row["TIME_SPENT"]; ?>></td>
	</tr>
        <?php endwhile; ?>
	<input type="submit" value="update" name="submit">
    	</tbody>
    	</table>
    </form>
    </div>

<?php
	if ($_POST) {
		header("location: studreport.php");
	}

	//Commit to save changes...
	//OCILogoff($db_conn);
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
