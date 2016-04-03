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
    <div class="jumbotron">
	<h1>CS304 TaskLogger</h1>
	<p class="lead">Professor Portal</p>
    </div>
    </div>
    </div>

<?php
//it's now parsing PHP
include 'executeQueries.php';

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_f3w8", "a94897071", "ug");

function printResult($result) { //prints results from a select statement
?>
<table id="resultTable">
    <thead>
        <tr>
            <th width="10%">Student ID</th>
            <th width="10%">First Name</th>
            <th width="10%">Last Name</th>
            <th width="10%">Major</th>
        </tr>
    </thead>
    <tbody>
        <?php
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) : ?>
	<tr>
        <td><?php echo $row["STID"]; ?></td>
        <td><?php echo $row["FNAME"]; ?></td>
        <td><?php echo $row["LNAME"];  ?></td>
        <td><?php echo $row["MAJOR"]; ?></td>
	</tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php
}

// Connect Oracle...
if ($db_conn) {

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: profindex.php");
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

</div>
</div>

</body>
</html>
