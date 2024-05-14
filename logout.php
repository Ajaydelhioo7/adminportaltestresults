<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: login_admin.php"); // Redirect to login page
exit();
?>
