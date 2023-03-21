<?php
// Note: above <?php should be at first row and first column otherwise it will create expected white spaces

$dbhost="localhost";
$dbUser="day01";
$dbPassword="123uvehY(2GwX5]4";
$dbName="day01";
$con = @mysqli_connect($dbhost, $dbUser, $dbPassword, $dbName);

if(!$con){
  die("Fatal Error - fail to connect to database -".mysqli_connect_error());
}
?>