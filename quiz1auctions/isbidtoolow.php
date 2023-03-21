<?php
require_once("db.php");

if(!isset($_GET['id']) || !isset($_GET['newbidprice'])){    //here username used by $_GET is a url parameter input  
    die("Error: id or newbidprice not provided");
}
$id = $_GET['id'];
$newBidPrice = $_GET['newbidprice'];
$checkSqlStr = sprintf("SELECT * FROM auctions WHERE id=%d",
mysqli_real_escape_string($con, $id));
$result = mysqli_query($con, $checkSqlStr); 
if(!$result){
    die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
}
$lastBidDetail = mysqli_fetch_assoc($result);
$lastBidPrice = $lastBidDetail['lastBidPrice'];
$newBidPrice;
if ($lastBidDetail>=$newBidPrice){
    echo "bid too low";
}
//   else
//   {
//        
// }
// here this php closing tag  is optional as there is not html or other any element after this. But be careful that if you close, there should be any extra white space aftr closing tag
?>