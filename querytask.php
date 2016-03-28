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
		<h1>CS304 TaskLogger</h1>
		<p class="lead">Reset the Assignment Table</p>
		<form method="POST" action="querytask.php">   
		<p><input class="btn btn-lg btn-success" type="submit" value="Reset" name="reset"></p>
		</form>
    		</div>
	</div>
	</div>

<h3>Insert record into Assignment table:</h3>
<div class="container">
<form method="POST" action="querytask.php">
	<label>Assignment ID</label>
	<input type="text" name="id"><br />
	<label>Description</label>
	<input type="text" name="description"><br />
	<label>Course dept.</label>
	<input type="text" name="course_dept"><br />
	<label>Course #</label>
	<input type="text" name="course_num"><br />
	<label>Hand-in location</label>
	<input type="text" name="hand_in_loc"><br />
	<label>Deadline</label>
	<input type="text" name="deadline"><br />
<input type="submit" value="insert" name="insertsubmit"></p>
</form>
</div>

<!-- create a form to pass the values. See below for how to 
get the values--> 

<h3> Update the Assignment Record by inserting the new values below: </h3>
<p size="10" color="light-grey">**Note: Assignment ID cannot change</p>

<div class="container">
<form method="POST" action="querytask.php">
<!--refresh page when submit-->

	<label>Assignment ID</label>
	<input type="text" name="id"><br />
	<label>Description</label>
	<input type="text" name="new_description"><br />
	<label>Course dept.</label>
	<input type="text" name="new_course_dept"><br />
	<label>Course #</label>
	<input type="text" name="new_course_num"><br />
	<label>Hand-in location</label>
	<input type="text" name="new_hand_in_loc"><br />
	<label>Deadline</label>
	<input type="text" name="new_deadline"><br />
<input type="submit" value="update" name="updatesubmit"></p>

<input type="submit" value="run hardcoded queries" name="dostuff"></p>
</form>
</div>

<h3> Selection query: </h3>
<div class="container">
<form method="POST" action="querytask.php">
	<label>Course dept.</label>
	<input type="text" name="query_course_dept"><br />
	<label>Course #</label>
	<input type="text" name="query_course_num"><br />
<input type="submit" value="query" name="querysubmit"></p>
</form>
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

function printResult($result) { //prints results from a select statement
?>
<table id="resultTable">
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th width="10%">Description</th>
            <th width="10%">Course dept.</th>
            <th width="10%">Course #</th>
            <th width="10%">Hand-in location</th>
            <th width="10%">Deadline</th>
        </tr>
    </thead>
    <tbody>
        <?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr>
        <td><?php echo $row["ID"]; ?></td>
        <td><?php echo $row["DESCRIPTION"]; ?></td>
        <td><?php echo $row["COURSE_DEPT"];  ?></td>
        <td><?php echo $row["COURSE_NUM"]; ?></td>
        <td><?php echo $row["HAND_IN_LOC"]; ?></td>
        <td><?php echo $row["DEADLINE"]; ?></td>
	</tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php
}

function printQueryResult($result) { //prints results from a select statement
?>
<table id="queryResultTable">
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th width="10%">Description</th>
            <th width="10%">Hand-in location</th>
            <th width="10%">Deadline</th>
        </tr>
    </thead>
    <tbody>
        <?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr>
        <td><?php echo $row["ID"]; ?></td>
        <td><?php echo $row["DESCRIPTION"]; ?></td>
        <td><?php echo $row["HAND_IN_LOC"]; ?></td>
        <td><?php echo $row["DEADLINE"]; ?></td>
	</tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("Drop table assignment");

		// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table assignment (id number, description varchar2(50), course_dept varchar2(4), course_num number, hand_in_loc varchar2(12), deadline varchar2(12), primary key(id))");
		OCICommit($db_conn);

	} else
		if (array_key_exists('querysubmit', $_POST)) {
			// Find assignments by inputted course department and course number
			$tuple = array (
				":bind1" => $_POST['query_course_dept'],
				":bind2" => $_POST['query_course_num']
			);
			$alltuples = array (
				$tuple
			);
			$result = executeBoundSQL("select id, description, hand_in_loc, deadline from assignment where course_dept=:bind1", $alltuples);
			printQueryResult($result);
			OCICommit($db_conn);


	} else
		if (array_key_exists('insertsubmit', $_POST)) {
			//Getting the values from user and insert data into the table
			$tuple = array (
				":bind1" => $_POST['id'],
				":bind2" => $_POST['description'],
				":bind3" => $_POST['course_dept'],
				":bind4" => $_POST['course_num'],
				":bind5" => $_POST['hand_in_loc'],
				":bind6" => $_POST['deadline']
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into assignment values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6)", $alltuples);
			OCICommit($db_conn);

	} else
		if (array_key_exists('updatesubmit', $_POST)) {
			// Update tuple using data from user
			$tuple = array (
				":bind1" => $_POST['id'],
				":bind2" => $_POST['new_description'],
				":bind3" => $_POST['new_course_dept'],
				":bind4" => $_POST['new_course_num'],
				":bind5" => $_POST['new_hand_in_loc'],
				":bind6" => $_POST['new_deadline']
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("update assignment set description=:bind2, course_dept=:bind3, course_num=:bind4, hand_in_loc=:bind5, deadline=:bind6 where id=:bind1", $alltuples);
			OCICommit($db_conn);

	} else
		if (array_key_exists('dostuff', $_POST)) {
			// Insert data into table...
			executePlainSQL("insert into assignment values (001, 'Assignment 1', 'CPSC', 317, 'X250', '04/08/2016')");
			// Inserting data into table using bound variables
			$list1 = array (
				":bind1" => 6,
				":bind2" => "Project 1",
				":bind3" => "CPSC",
				":bind4" => 304,
				":bind5" => "X230",
				":bind6" => "03/30/2017"
			);
			$list2 = array (
				":bind1" => 7,
				":bind2" => "Exam 1",
				":bind3" => "CPSC",
				":bind4" => 320,
				":bind5" => "X130",
				":bind6" => "03/30/2016"
			);
			$allrows = array (
				$list1,
				$list2
			);
			executeBoundSQL("insert into assignment values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6)", $allrows); //the function takes a list of lists
			// Update data...
			//executePlainSQL("update tab1 set nid=10 where nid=2");
			// Delete data...
			//executePlainSQL("delete from tab1 where nid=1");
			OCICommit($db_conn);
		}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: querytask.php");
	} else {
		// Select data...
		$result = executePlainSQL("select * from assignment");
		printResult($result);
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
