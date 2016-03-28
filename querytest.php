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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

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
                    <a href="#">About</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->


    <div class="jumbotron">
	<h1>CS304 TaskLogger</h1>
	<p class="lead">Reset the Student Table</p>
	<form method="POST" action="querytest.php">   
	<p><input class="btn btn-lg btn-success" type="submit" value="Reset" name="reset"></p>
	</form>
    </div>

<h3>Insert record into Student table:</h3>
<div class="container">
<form action="querytest.php" method="POST">
	<label>Assignment ID</label>
	<input type="text" name="id"><br />
	<label>Deadline</label>
	<input type="text" name="deadline"><br />
	<label>Description</label>
	<input type="text" name="description"><br />
	<label>Hand-in location</label>
	<input type="text" name="hand_in_loc"><br /><br>
	<label>Course #</label>
	<input type="text" name="course_num"><br /><br>
	<label>Course dept.</label>
	<input type="text" name="course_dept"><br /><br>
<input type="submit" value="insert" name="insertsubmit"></p>
</form>
</div>


<!-- create a form to pass the values. See below for how to 
get the values--> 

<h3> Update the Student Record by inserting the new values below: </h3>
<p size="10" color="light-grey">**Note: Student ID cannot change</p>

<div class="container">
<form method="POST" action="querytest.php">
<!--refresh page when submit-->

	<label>Assignment ID</label>
	<input type="text" name="id"><br />
	<label>Deadline</label>
	<input type="text" name="deadline"><br />
	<label>Description</label>
	<input type="text" name="description"><br />	
	<label>Hand-in location</label>
	<input type="text" name="hand_in_loc"><br /><br>
	<label>Course #</label>
	<input type="text" name="course_num"><br  /><br>
	<label>Course dept.</label>
	<input type="text" name="course_dept"><br /><br>
<input type="submit" value="update" name="updatesubmit"></p>

<input type="submit" value="run hardcoded queries" name="dostuff"></p>
</form>
</div>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_v7t8", "a35176114", "ug");

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that descriptionribe some of the OCI specific functions and how they work

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
	echo '<div class="table-striped"><table id="resultTable">';
	echo '<thead><tr><th>ID</th><th>Deadline</th><th>Description</th><th>Hand-in location</th><th>Course #</th><th>Course dept.</th></tr></thead><tbody>';

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>".$row["ID"]."</td><td>".$row["DEADLINE"]."</td><td>".$row["DESCRIPTION"]."</td><td>".$row["HAND_IN_LOC"]."</td><td>".$row["COURSE_NUM"]."</td><td>".$row["COURSE_DEPT"]."</td></tr>";
		//echo $row[0];
	}
	echo '</tbody></table></div>';
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("Drop table assignment");

		// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table assignment (id number, deadline varchar2(12), description varchar2(30), hand_in_loc varchar2(20), course_num number, course_dept varchar2(4), primary key(id))");
		OCICommit($db_conn);

	} else
		if (array_key_exists('insertsubmit', $_POST)) {
			//Getting the values from user and insert data into the table
			$tuple = array (
				":bind1" => $_POST['id'],
				":bind2" => $_POST['deadline'],
				":bind3" => $_POST['description'],
				":bind4" => $_POST['hand_in_loc'],
				":bind5" => $_Post['course_num'],
				":bind6" => $_Post['course_dept']
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
					":bind2" => $_POST['deadline'],
					":bind3" => $_POST['description'],
					":bind4" => $_POST['hand_in_loc'],
					":bind5" => $_Post['course_num'],
					":bind6" => $_Post['course_dept']
				);
				$alltuples = array (
					$tuple
				);
				executeBoundSQL("update assignment set deadline=:bind2, description=:bind3, hand_in_loc=:bind4, course_num=:bind5, course_dept=:bind6 where id=:bind1", $alltuples);
				OCICommit($db_conn);

			} else
				if (array_key_exists('dostuff', $_POST)) {
					// Insert data into table...
					executePlainSQL("insert into assignment values (001, '06/07/16', 'Database project', 'X230', '320', 'CPSC')");
					// Inserting data into table using bound variables
					$list1 = array (
						":bind1" => 6,
						":bind2" => "06/07/16",
						":bind3" => "Project",
						":bind4" => "X250",
						":bind5" => "304",
						":bind6" => "CPSC"
					);
					$list2 = array (
						":bind1" => 7,
						":bind2" => "06/06/17",
						":bind3" => "Test",
						":bind4" => "X235",
						":bind5" => "317",
						":bind6" => "CPSC"
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
		header("location: querytest.php");
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
     OCI_RETURN_LOBS - return the value of a LOB of the descriptionriptor.  
     Default mode is OCI_BOTH.  */
?>
/* Script to sort the table */
<script>
	$(document).ready(function(){
	$(function(){
		$("#resultTable").tablesorter();
		});
	});
</script>

</body>
</html>
