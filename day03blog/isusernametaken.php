<?php

require_once("db.php");

if(!isset($_GET['username'])){    //here username used by $_GET is a url parameter input  
    die("Error: username parameter not provided");
}
$username = $_GET['username'];
$checkSqlStr = sprintf("SELECT * FROM users WHERE userName='%s'",
    mysqli_real_escape_string($con, $username));
    $result = mysqli_query($con, $checkSqlStr); 
if(!$result){
    die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
}
$userRecord = mysqli_fetch_assoc($result);
if ($userRecord){
    echo $username . " is not available";
}
//   else
//   {
//     echo $username . " is available";       
// }
// here this php closing tag  is optional as there is not html or other any element after this. But be careful that if you close, there should be any extra white space aftr closing tag
?>