<?php
// include('php/firebase/php-firebase-config.php');

if(!isset($_SESSION['user_id'])){
  header("location: login.php");
} else {
  header("location: /dashboard-pages/home.php");
}
?>