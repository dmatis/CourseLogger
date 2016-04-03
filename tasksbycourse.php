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

	<title>View Course Tasks</title>

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
                    <a href="studreport.php">Reports</a>
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

    <h3> View Tasks by Course </h3>

    <script>
    function isAlpha(str) {
	  return /^[a-zA-Z]+$/.test(str);
	}
    function validate()
      {
      	 
	     if( (document.myForm.course_dept.value.length != 4) || (!isAlpha(document.myForm.course_dept.value)))
	     {
	        alert( "Please provide a valid course department!" );
	        document.myForm.course_dept.focus() ;
	        return false;
	     }

	     if ($('input[name="queryAttr[]"]:checked').length == 0) {
	     	alert( "Please check at least one box!" );
	     	document.myForm.course_dept.focus() ;
	        return false;
	     }
	     
	     if(  (document.myForm.course_num.value.length != 3) || (document.myForm.course_num.value / 10) == 0)
	     {
	        alert( "Please provide a valid course number!" );
	        document.myForm.course_num.focus() ;
	        return false;
	     }
	     
	     return( true );
      }
    </script>


	<form name="myForm" action="tasksbycourse_out.php" onsubmit="return validate()" method="POST">
		<h4> Pick attributes to be displayed: </h4>
		<div class="container">
			<input type="checkbox" name="queryAttr[]" value="task_id" id="cb_id"> ID<br>
			<input type="checkbox" name="queryAttr[]" value="descrip" id="cb_descrip"> Description<br>
			<input type="checkbox" name="queryAttr[]" value="course_dept" id="cb_course_dept"> Course dept.<br>
			<input type="checkbox" name="queryAttr[]" value="course_num" id="cb_course_num"> Course #<br>
			<input type="checkbox" name="queryAttr[]" value="deadline" id="cb_deadline"> Deadline<br>
			<!-- <input type="submit" name="checkboxsubmit" value="Search" id="cb_submit"><br> -->
		</div>
		<h4> Enter a course department and a course #: </h4>
		<div class="container">
			<label>Course dept.</label>
			<input type="text" name="course_dept"><br>
			<label>Course #</label>
			<input type="text" name="course_num"><br>
			<input type="submit" name="coursesubmit" value="Submit"><br>
		</div>
	</form>

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

if (array_key_exists('checkboxsubmit', $_POST)) {
	if(!empty($_POST['queryAttr'])){
		foreach($_POST['queryAttr'] as $selected){
			echo $selected."</br>";
		}
	}
}

// Connect Oracle...
if ($db_conn) {

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: tasksbycourse.php");
	} else {
		// Select data...
		$result = executePlainSQL("select * from task");
		// printResult($result);
		// checkCheckboxes();
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
    // $("#menu-toggle").click(function(e) {
    //     e.preventDefault();
    //     $("#wrapper").toggleClass("toggled");
    // });
    </script>

</body>
</html>
