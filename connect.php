<?php
//session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "housepool";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed because: " . mysqli_connect_error());
}

return $conn;
?>