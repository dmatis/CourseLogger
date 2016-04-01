<html>
<?php
//session_save_path("../../php_sessions");
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../php_sessions'));
session_start();
include 'executeQueries.php';

$isProf = $_POST['prof']; //prof checkbox

// Connect to server and select database.
$success = True;
$db_conn = oci_connect("ora_f3w8", "a94897071", "ug");
if ($db_conn) {
	echo "Successfully Connected to Oracle!\n";
} else {
  $err = OCIError();
  echo "Oracle Error " . $err['message'];
}
 
$myid = $_POST['myid'];
$mypassword = $_POST['mypassword'];

if(empty($isProf)){
	echo"<p>Searching Student database</p>";
	$stid = oci_parse($db_conn, "create table memb as select * from student where stid='".$myid."' and password='".$mypassword."'");
	oci_execute($stid);

} else {
	echo"<p>Searching Professor database</p>";
	$stid = oci_parse($db_conn, "create table memb as select * from professor where profid='".$myid."' and password='".$mypassword."'");
	oci_execute($stid);
}


//Useful for seeing what's in the _POST session data:
//print_r($_POST);

$tuple = array (
	":bind1" => $myid,
	":bind2" => $mypassword
);
$alltuples = array ($tuple);  

$count = oci_num_rows($stid);
oci_free_statement($stid);

$stid = oci_parse($db_conn, "drop table memb");
oci_execute($stid);
oci_free_statement($stid);
oci_close($db_conn);

if($count==1){
	if(!empty($isProf)){
		$_SESSION['myprofid'] = $myid;
	} else {
		$_SESSION['myid'] = $myid;
	}
	$_SESSION['mypassword'] = $mypassword;
	header('Refresh: 1; URL=loginsuccess.php');
	exit();
} else {
	echo "Wrong Username or Password";
	header('Refresh: 2; URL=main_login.php');
}

?>
</html>
