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

	<title><?php echo "Tasks for ".strtoupper($_POST["course_dept"])." ".$_POST["course_num"]?></title>

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

<h3><?php echo "Tasks for ".strtoupper($_POST["course_dept"])." ".$_POST["course_num"].": "?></h3>
<div class="container">

</div>

<?php

function printResult($result, $attrs_arr) { //prints results from a select statement
?>
<div class="container" style="display:hidden" id="no_tasks">
	<p>No tasks found</p>
</div>
<div class="container" id="tasks">
	<table id="resultTable" class="table">
	    <thead>
	        <tr>
	        	<?php foreach ($attrs_arr as $attr) {
	        		?>
	        		<th width="10%"><?php echo strtoupper($attr)?></th>
	        		<?php
	        	}
	        	?>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
		<tr>
			<?php foreach ($attrs_arr as $attr) {
	        		?>
	        		<td><?php echo $row[strtoupper($attr)]?></td>
	        		<?php
	        	}
	        	?>
		</tr>
	        <?php endwhile; ?>
	    </tbody>
	</table>
</div>
<script>
if ($("#resultTable").has("td").length == 0) {
	$("#no_tasks").show();
	$("#tasks").hide();
}
else{
	$("#no_tasks").hide();
	$("#tasks").show();
}
</script>
<?php
}

$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");
$attrs_arr = array();
foreach ($_POST['queryAttr'] as $attr) {
	$attrs .= ", ".$attr;
	array_push($attrs_arr, $attr);
}
$attrs = substr($attrs,2);

$query = "select ".$attrs." from task where course_dept='".strtoupper($_POST["course_dept"])."' and course_num=".$_POST["course_num"];

$statement = OCIParse($db_conn, $query);
OCIExecute($statement, OCI_DEFAULT);
printResult($statement, $attrs_arr);

// print "CONTENT_TYPE: " . $_SERVER['CONTENT_TYPE'] . "<BR />";
// $data = file_get_contents('php://input'); print "DATA: <pre>";
// var_dump($data);
// var_dump($_POST);
// print "</pre>";
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

    <script>
	$(document).ready(function(){
	$(function(){
		$("#resultTable").tablesorter();
		});
	});
    </script>

    <script>
    // $("#menu-toggle").click(function(e) {
    //     e.preventDefault();
    //     $("#wrapper").toggleClass("toggled");
    // });
    </script>

</body>
</html>
