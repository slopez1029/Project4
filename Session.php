<?php
$mysqli = new mysqli("mysql.eecs.ku.edu", "slopez", "Password123!", "slopez");    

/**
 * Checks to see if sql connection is properly configured
 */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}


session_start();


if(!isset($_SESSION['PayerID'])){
  header("location:Login.html");
}
?>
