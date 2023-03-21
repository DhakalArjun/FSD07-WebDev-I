<?php require_once('db.php')?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="styles.css?<?php echo time();?>" />
</head>

<body class="containterCentered width800">
  <?php
      function printBidForm($content="")
      { $addBidForm = <<< FORMSTART

        <h2 class="txtCenter">Place Bid</h2>        
        <form class="formAsContainter width800" action="" method="post" enctype="multipart/form-data">  
        <div class="rowFlex">
          <label class="formLabel width7rem">Bidders Name</label>
          <input type="text" name="bidderName" class="width600" placeholder="" value="">
        </div> 
        <div class="rowFlex">
          <label class="formLabel width7rem">Bidders Email</label>
          <input type="text" name="bidderEmail" class="width600" placeholder="" value="">
        </div>
        <div class="rowFlex">
          <label class="formLabel width7rem txtLeft">New Bid Price</label>
          <input type="text" name="newBidPrice" class="width600" placeholder="" value="">
        </div> <br> 
        <input type="submit" name="submitBid" value="Submit Bid" class="btnDefault">
        </form>
        FORMSTART;
        echo $addBidForm ;
      }
      //take id=18;
      if (!isset($_GET['id'])) {
        die("Error: missing article ID in the URL");
      } 
        $id = $_GET['id'];
        $sqlStr = sprintf("SELECT * FROM auctions WHERE id=%d",
            mysqli_real_escape_string($con, $id));
        $result = mysqli_query($con, $sqlStr);
        if (!$result) {
            die("SQL Query failed: " . mysqli_error($con));
        }
        $item = mysqli_fetch_assoc($result);
        //print_r($item);
        if (!$item) {
            http_response_code(404); // may work or not work, depending on whether output buffering is enabled or not
            echo '<h2>Item not found</h2>';
        } else {
          $itemDesc = strip_tags($item['itemDescription']);
          $imagePath = strip_tags($item['itemImagePath']);
          $sellersName = strip_tags($item['sellerName']);
          $lastBid = strip_tags($item['lastBidPrice']); 
          $image = 'uploads/'.$imagePath; 

          echo "<div class='itemDisplaySection'>         
                  <p><b>Item Description: </b>$itemDesc</p>
                  <img src='$image' alt='item image' width='150'>
                  <p><b>Seller Name: </b>$sellersName</p>
                  <p><b>Last Bid: </b>$lastBid</p>
                </div>";

     
            printBidForm();
    } 

    if(isset($_POST['submitBid'])){
      $bidderName = $_POST['bidderName'];
      $bidderEmail = $_POST['bidderEmail'];
      $newBidPrice = $_POST['newBidPrice'];

      if($newBidPrice<=$lastBid){
        echo "You bid price is less than last bit, so you cannot bid with this price";
      }
      else{
      
    
      $sqlStr = sprintf("UPDATE  auctions SET lastBidPrice = '', lastBidderName ='%s', lastBidderEmail='%s' WHERE id = %d",
      mysqli_real_escape_string($con, $bidderName),
      mysqli_real_escape_string($con, $bidderEmail),
      mysqli_real_escape_string($con, $newBidPrice)
      );  
      $result = mysqli_query($con, $sqlStr);
      //print_r($result);
          
      if(!$result){
        die("SQL Query Failed: " .mysqli_errno($con));
      }
      $bidderDetails = ['name'=> $bidderName, 'email'=>$bidderEmail];
      $_SESSION['bidderDetails'] = $bidderDetails;
    }
  }
      

      


    
        






  ?>

</body>

</html>