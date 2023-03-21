<?php
session_start();

$dbhost="localhost";
$dbUser="quiz1auctions";
$dbPassword="I_1t23n]uVe70tnf";
$dbName="quiz1auctions";
$con = @mysqli_connect($dbhost, $dbUser, $dbPassword, $dbName);

if(!$con){
  die("Fatal Error - fail to connect to database -".mysqli_connect_error());
}
?>