<html>
<?php
session_save_path("../../php_sessions");
session_start();
include 'executeQueries.php';

$isProf = $_POST['prof']; //prof checkbox
$tbl_name="members"; // Table name 

// Connect to server and select database.
$success = True;
$db_conn = oci_connect("ora_f3w8", "a94897071", "ug");
if ($db_conn) {
	echo "Successfully Connected to Oracle!\n";
} else {
  $err = OCIError();
  echo "Oracle Error " . $err['message'];
} 

if(empty($isProf)){
	echo"<p>Searching Student database</p>";
	$stid = oci_parse($db_conn, "create table memb as select * from members");
	oci_execute($stid);

} else {
	echo"<p>Searching Prof database</p>";
	$stid = oci_parse($db_conn, "create table memb as select * from profs");
	oci_execute($stid);
}


//Useful for seeing what's in the _POST session data:
//print_r($_POST);

$myusername = $_POST['myusername'];
$mypassword = $_POST['mypassword'];

$tuple = array (
	":bind1" => $myusername,
	":bind2" => $mypassword
);
$alltuples = array ($tuple);  

//$sql="select * from $tbl_name WHERE username=:bind1 and password=:bind2";
//$result=executeBoundSQL($sql, $alltuples);
//OCICommit($db_conn);

$count = oci_num_rows($stid);
oci_free_statement($stid);

$stid = oci_parse($db_conn, "drop table memb");
oci_execute($stid);
oci_free_statement($stid);
oci_close($db_conn);

if($count==1){
	$_SESSION['myusername'] = $myusername;
	$_SESSION['mypassword'] = $mypassword;
	echo $_SESSION['myusername'];
	header('Refresh: 1; URL=loginsuccess.php');
	exit();
} else {
	echo "Wrong Username or Password";
}

?>
</html>
