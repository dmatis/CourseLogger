<?php
session_save_path("/home/f/f3w8/php_sessions");
session_start();
//echo "<br>".session_id();
//var_dump($_SESSION);

//echo "user = ".$_SESSION["myusername"]."<br />";

//if(!isset($_SESSION['myusername'])){
//	header("location:main_login.php");
//	echo "nothing being set";
//}
?>

<html>
<body>
Login Successful, welcome:
<?php
echo $_SESSION['myusername'];
header('Refresh: 3; URL=index.php');
?>
</body>
</html>
