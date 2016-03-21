<?php 
session_save_path("/home/f/f3w8/php_sessions");
session_start();
session_destroy();
echo "<h3>Logged Out Successfully</h3>";
echo "<p><i>Redirecting in 5 seconds</i>.</p>";
header('Refresh: 5; URL=main_login.php');
?>
