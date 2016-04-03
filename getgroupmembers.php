<!DOCTYPE HTML>
<html>
<body>
    
    
<?php
$tid = $_GET['tid']; 
$gid = $_GET['gid'];
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
	}
	return $statement;
}
    
if($db_conn){
    $members = executePlainSQL("select S.fname, S.lname from student S, group_performs G where G.task_id ='".$tid."' AND G.group_id='".$gid."' AND G.stid = S.Stid");
    
    echo "<table>";
    echo "<tr>
        <th>FirstName</th>
        <th>Last Name</th></tr>";
    while ($row = OCI_Fetch_Array($members, OCI_BOTH)){
        echo "<tr>";
        echo "<td>" . $row["FNAME"] . "</td>";
        echo "<td>" . $row["LNAME"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    OCILogoff($db_conn);
} else {
  echo "cannot connect";
  $e = OCI_Error(); // For OCILogon errors pass no handle
  echo htmlentities($e['message']);
}
    ?>
    </body>
</html>
