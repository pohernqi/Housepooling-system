<?php include("connect.php");  ;
 session_start();
 session_destroy(); //Unsets $_SESSION['logged_in']

 //2. REdirect to Login Page
 header('location: index.php');
 ?>