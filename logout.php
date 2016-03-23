<?php 
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../php_sessions'));
session_start();
session_destroy();
echo "<h3>Logged Out Successfully</h3>";
echo "<p><i>Redirecting in 2 seconds</i>.</p>";
header('Refresh: 2; URL=main_login.php');
?>
