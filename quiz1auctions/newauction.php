<?php require_once('db.php')?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Aunction</title>
  <link rel="stylesheet" href="styles.css?<?php echo time();?>" />
  <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
  tinymce.init({
    selector: 'textarea[name=itemDesc]'
  });
  </script>
</head>

<body class="containterCentered">
  <?php

   function printAuctionsForm($itemDesc="", $sellerName="", $sellerEmail="", $initialBid="")
   { $newAuctions = <<< FORMSTART
     <h2 class="txtCenter">Create New Aunctions</h2>
     <form class="formAsContainter width800" action="" method="post" enctype="multipart/form-data">
        <div class="rowFlex">            
         <label class="formLabel width10rem" >Item Description</label>  
        <textarea name="itemDesc" class="width600" placeholder="Type item description here...." requried>$itemDesc</textarea>            
       </div>
       <div class="rowFlex">
        <label for="filePath" class="formLabel width10rem">Item Image</label>
        <input type="file" name="imageToUpload" class="width600 inputUploadImg" id="filePath"/>        
       </div>              
       <div class="rowFlex">
         <label class="formLabel width10rem">Seller Name</label>
         <input type="text" name="sellerName" class="width600" placeholder="Type title here...." value="$sellerName">
       </div>   
       <div class="rowFlex">
        <label class="formLabel width10rem">Seller Email</label>
        <input type="text" name="sellerEmail" class="width600" placeholder="Type title here...." value="$sellerEmail">
       </div>  
       <div class="rowFlex">
       <label class="formLabel width10rem">Initial Bid Price</label>
       <input type="text" name="initialBid" class="width600" placeholder="Type title here...." value="$initialBid">
      </div>
       <input type="submit" name="create" value="Create" class="btnDefault moveRight-3"> 
     </form>
    
     FORMSTART;
     echo $newAuctions;
   }
   if(isset($_POST['create'])){   //if create button is clicked
    $itemDesc=$_POST['itemDesc'];    
    $sellerName=$_POST['sellerName'];
    $sellerEmail=$_POST['sellerEmail'];
    $initialBid=$_POST['initialBid'];   

    $itemDesc = strip_tags($itemDesc, "<p><ul><ol><li><br><hr><em><strong><b><span>");
    
    
    $sellerName = htmlentities($sellerName);
    $errors = [];
    
    $fileName = $_FILES['imageToUpload']['name'];  
    $acceptableFileFormats = ['jpg', 'gif', 'png', 'bmp'];
    $fileNameExplode = explode('.',$fileName); 
    $fileNameOriginal = $fileNameExplode[0];
    //echo  $fileNameOriginal;
       
    $fileExtention = end($fileNameExplode);
    $fileFormat = strtolower($fileExtention); 
    //echo  $fileFormat ;

    if(strlen($itemDesc)<2|| strlen($itemDesc)>1000){
      $errors[]= "Item description must be 2-1000 characters long";
      
    }
     //To check file format 
     if(!in_array($fileFormat,$acceptableFileFormats)){
      $errors[]="File format of image uploaded  is not allowed, please choose a JPG or GIF or PNG or BMP file.";
    }

    if(strlen($sellerName)<2|| strlen($sellerName)>100){
      $errors[]= "Title must be 2-100 characters long";
      
    }
    if(preg_match('/^[A-Za-z][a-z0-9\s\-.,]{1,99}$/', $sellerName) !==1){
      $errorList[] = "User name  must contain only letters (upper/lower-case), space, dash, dot, comma and numbers";
    }
    if (filter_var($sellerEmail, FILTER_VALIDATE_EMAIL) === false) {
      $errors[]="Please verify your email address, it doesn't look valid.";
      $sellerEmail="";
    }

   // $fileNameReplaced = preg_replace("/s\-.,/", "_", $fileNameOriginal); // this is not working
   $fileNameReplaced =$fileNameOriginal;
    //echo  $fileNameReplaced;
    // Create the uploads folder if missing
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) { 
      mkdir( $targetDir,0777,false);  
    }

    if(!$errors){ 

    if(file_exists( "uploads/".$fileNameReplaced)){
      function generateRandomString() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i <10; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
      }
      $uniqueSuffix = generateRandomString();
      $targetFileName = $fileNameReplaced.$uniqueSuffix.".".$fileFormat;           
    }else{
      $targetFileName = $fileNameReplaced.".".$fileFormat;
    } 


    $sqlStr = sprintf("INSERT INTO auctions (itemDescription, itemImagePath, 	sellerName, sellerEmail, lastBidPrice) VALUES ('%s', '%s', '%s', '%s', '%d')",
      //Note: we don't need to use quotes for column names.
      mysqli_real_escape_string($con, $itemDesc),
      mysqli_real_escape_string($con, $targetFileName),
      mysqli_real_escape_string($con, $sellerName),  
      mysqli_real_escape_string($con, $sellerEmail),   
      mysqli_real_escape_string($con, $initialBid)        
      );

      //echo  $itemDesc;

      $queryResult = mysqli_query($con, $sqlStr);
      if(!$queryResult){
        die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
      }
      move_uploaded_file($_FILES['imageToUpload']['tmp_name'],'uploads/'.$targetFileName);
      echo '<div class=successMsg>
                <h4>You have successfully created new auction</h4>               
                <a href="listitems.php" class="btnDefault">Click here to continue</a>
              </div>'; 
    } else{

      printAuctionsForm($itemDesc,$sellerName, $sellerEmail, $initialBid);   
        echo '<div class="errorMsg ">
                <h4>Create new auction failed, error occured</h4>';                
                foreach ($errors as $error){
                  echo "<li>$error</li>";
                }            
        echo '</div>';      
    }

   }else{

    printAuctionsForm();
   } //end of isset($_POST['create']))


  ?>
</body>

</html>