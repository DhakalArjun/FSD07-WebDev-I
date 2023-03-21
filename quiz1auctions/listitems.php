<?php require_once('db.php')?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Item Lists</title>
  <link rel="stylesheet" href="styles.css?<?php echo time();?>" />
</head>

<body class="containterCenteredlarge">

  <?php

$sqlStr = "SELECT * FROM auctions ORDER BY id DESC ";
$result = mysqli_query($con, $sqlStr);
if (!$result) {
    die("SQL Query failed: " . mysqli_error($con));
}

echo '<table align="left" cellspecing="5" cellpadding="8">
      <tr>
       <td align="left"><b>Item Description</b><td>
       <td align="left"><b>ImagePath</b><td>
       <td align="left"><b>Seller Name</b><td>
       <td align="left"><b>Item Description</b><td>
       <td align="left"><b>Last Bid Price</b><td>
       </tr>';
        while ($item = mysqli_fetch_assoc($result)) {
          $itemDesc = strip_tags($item['itemDescription']);
          $imagePath = strip_tags($item['itemImagePath']);
          $sellersName = strip_tags($item['sellerName']);
          $lastBid = strip_tags($item['lastBidPrice']); 
          
           //echo $imagePath;

           $image = 'uploads/'.$imagePath;  
           //echo $image;
        echo '<tr>
              <td align="left">'.$itemDesc.'<td>'.
            '<td align="left">'.'<img src="$image" alt="item image" width="150">'.'<td>'.
            '<td align="left">'.$sellersName.'<td>'.
            '<td align="left">'.$lastBid.'<td>'.
            '<td align="left" class="width7rem">'.'<a href="#" class="btnSmall">Make a Bid</a>'.'<td>
            </tr>';
        }        
        echo '</table>';   
?>

</body>

</html>