<?php
// Note: above <?php should be at first row and first column otherwise it will create expected white spaces
session_start();

$dbhost="localhost";
$dbUser="day03blog";
$dbPassword="fgkkas_Hg0)cpvy0";
$dbName="day03blog";
$con = @mysqli_connect($dbhost, $dbUser, $dbPassword, $dbName);

if(!$con){
  die("Fatal Error - fail to connect to database -".mysqli_connect_error());
}
?>