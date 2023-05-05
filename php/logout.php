<?php
//Logout the Admin
session_start();
session_destroy();
header("Location: ../login.php");
exit;
?>