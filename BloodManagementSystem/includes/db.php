<?php 
$host = "localhost";
$password = "";
$user = "root";
$db = "sid24152358";

$conn = mysqli_connect($host,$user,$password,$db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>