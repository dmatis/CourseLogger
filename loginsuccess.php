<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../php_sessions'));
session_start();
?>

<html>
<body>
Login Successful!
<?php
if (!isset($_SESSION['myprofid'])){
	header('Refresh: 2; URL=index.php');
} else {
	header('Refresh: 2; URL=profreport.php');
}
?>
</body>
</html>
