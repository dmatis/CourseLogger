<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../php_sessions'));
session_start();

//if(!isset($_SESSION['myprofname'])){
//	header("location:main_login.php");
//}
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
                    <a href="#">Courses</a>
                </li>
                <li>
                    <a href="#">About</a>
                </li>
                <li>
                    <a href="../CourseLogger/logout.php">LOGOUT</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->


    <div class="jumbotron">
	<h1>CS304 TaskLogger</h1>
	<p class="lead">Professor Portal</p>
	<form method="POST" action="index.php">   
	<p><input class="btn btn-lg btn-success" type="submit" value="Reset" name="reset"></p>
	</form>
    </div>

<h3>Insert record into Student table:</h3>
<div class="container">
<form action="index.php" method="POST">
	<label>Student ID</label>
	<input type="text" name="id"><br />
	<label>First Name</label>
	<input type="text" name="fname"><br />
	<label>Last Name</label>
	<input type="text" name="lname"><br />
	<label>Major</label>
	<input type="text" name="major"><br /><br>
<input type="submit" value="insert" name="insertsubmit"></p>
</form>
</div>

<h3> Update the Student Record by inserting the new values below: </h3>
<p size="10" color="light-grey">**Note: Student ID cannot change</p>

<div class="container">
<form method="POST" action="index.php">
<!--refresh page when submit-->

	<label>Student ID</label>
	<input type="text" name="id"><br />
	<label>First Name</label>
	<input type="text" name="newFName"><br />
	<label>Last Name</label>
	<input type="text" name="newLName"><br />	
	<label>Major</label>
	<input type="text" name="major"><br /><br>
<input type="submit" value="update" name="updatesubmit"></p>

<input type="submit" value="run hardcoded queries" name="dostuff"></p>
</form>
</div>

<?php
//it's now parsing PHP
include 'executeQueries.php';

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");

function printResult($result) { //prints results from a select statement
	echo '<div class="table-striped"><table id="resultTable">';
	echo '<thead><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Major</th></tr></thead><tbody>';

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>".$row["ID"]."</td><td>".$row["FNAME"]."</td><td>".$row["LNAME"]."</td><td>".$row["MAJOR"]."</td></tr>";
		//echo $row[0];
	}
	echo '</tbody></table></div>';
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("Drop table student");

		// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table student (id number, fname varchar2(20), lname varchar2(20), major varchar2(4), primary key(id))");
		OCICommit($db_conn);

	} else
		if (array_key_exists('insertsubmit', $_POST)) {
			//Getting the values from user and insert data into the table
			$tuple = array (
				":bind1" => $_POST['id'],
				":bind2" => $_POST['fname'],
				":bind3" => $_POST['lname'],
				":bind4" => $_POST['major']
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into student values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
			OCICommit($db_conn);

		} else
			if (array_key_exists('updatesubmit', $_POST)) {
				// Update tuple using data from user
				$tuple = array (
					":bind1" => $_POST['id'],
					":bind2" => $_POST['newFName'],
					":bind3" => $_POST['newLName'],
					":bind4" => $_POST['major']
				);
				$alltuples = array (
					$tuple
				);
				executeBoundSQL("update student set fname=:bind2, lname=:bind3, major=:bind4 where id=:bind1", $alltuples);
				OCICommit($db_conn);

			} else
				if (array_key_exists('dostuff', $_POST)) {
					// Insert data into table...
					executePlainSQL("insert into student values (001, 'Frank', 'Sinatra', 'BIOL')");
					// Inserting data into table using bound variables
					$list1 = array (
						":bind1" => 6,
						":bind2" => "Doris",
						":bind3" => "Day",
						":bind4" => "MUSI"
					);
					$list2 = array (
						":bind1" => 7,
						":bind2" => "Bob",
						":bind3" => "Barker",
						":bind4" => "HIST"
					);
					$allrows = array (
						$list1,
						$list2
					);
					executeBoundSQL("insert into student values (:bind1, :bind2, :bind3, :bind4)", $allrows); //the function takes a list of lists
					// Update data...
					//executePlainSQL("update tab1 set nid=10 where nid=2");
					// Delete data...
					//executePlainSQL("delete from tab1 where nid=1");
					OCICommit($db_conn);
				}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: index.php");
	} else {
		// Select data...
		$result = executePlainSQL("select * from student");
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
/* Script to sort the table */
//<script>
	$(document).ready(function(){
	$(function(){
		$("#resultTable").tablesorter();
		});
	});
</script>

</body>
</html>
