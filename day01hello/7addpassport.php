<?php
  require_once('db.php');
  $targetDir = "uploads/";
  // Create the uploads folder if missing
  if (!file_exists($targetDir)) { 
    mkdir( $targetDir,0777,false);  // for description of mkdir(): https://www.php.net/manual/en/function.mkdir.php and https://www.w3schools.com/php/func_filesystem_mkdir.asp
  }
  $acceptableFileFormats = ['jpg', 'gif', 'png'];
  
  if(isset($_POST['submit'])){  //if submit button is clicked, 'submit' here -> name attribute of the button.   
    $passportNo = $_POST['passportNo']; // 'passportNo' here -> name attribute of input for passport number   
    $fileName = $_FILES['ImageToUpload']['name'];  

    // $fileType = $_FILES['ImageToUpload']['type'];   -> This gives image/jpeg      
    //$fileFormat = strtolower(end(explode('.',$fileName)));  //Notice: Only variables should be passed by reference in C:\xampp\htdocs\fsd07-php\day01hello\7addpassport.php on line 12
    $fileNameExplode = explode('.',$fileName);
    $fileExtention = end($fileNameExplode);
    $fileFormat = strtolower($fileExtention); 
   
    //$fileFormat = strtolower(end(explode('.',$fileName)));
    $targetFileName = $passportNo.".".$fileFormat;    
    $errors = [];

    // To check passport number condition : ^[A-Z]{2}[0-9]{6}$
    if(preg_match('/^[A-Z]{2}[0-9]{6}$/', $passportNo) !=1){
      $errors[]="Passport number should have two uppercase letter followed by exactly 6 numbers, example: AB123456.";
      $passportNo = "";
    }

    //// is there a photo being uploaded and is it okay?
    if ($_FILES['ImageToUpload']['error'] != UPLOAD_ERR_OK) {
      $errors[]= "Error uploading photo " . $_FILES['ImageToUpload']['error'];
    }


     //To check file format 
    if(!in_array($fileFormat,$acceptableFileFormats)){
      $errors[]="File format of image selected  is not allowed, please choose a JPG or GIF or PNG file.";
    }

    $imageDimension = getimagesize($_FILES['ImageToUpload']['tmp_name']);
    // echo "<pre>\ngetimagesize result:\n";
    // print_r($info);
    // echo "</pre>\n";
    if ($imageDimension[0] < 100 || $imageDimension[0] > 1000 || $imageDimension[1] < 100 || $imageDimension[1] > 1000) {
      $errors[]= "Width and height must be within 200-1000 pixels range";
    }

    //To check file size: 
    $fileSize = $_FILES['ImageToUpload']['size'];  
    if($fileSize > 2 * 1024 * 1024){                        // 1 MB = 1024 bytes
        $errors[]='Sorry your file is too large, maximum file size is 2 MB';
    }

    // to check if file already exists
    if(file_exists( "uploads/".$targetFileName)){
      $errors[]='Sorry, file already exists.';      
    }

    if(!$errors){ 
      //full path of the file uploaded      
      //$fullPath = getcwd()."\uploads\\".$targetFileName;   -> this will create the full path  
      $filePath = "/uploads/".$targetFileName;
      

      $sqlStr = sprintf("INSERT INTO passports (passportNo, photoFilePath) VALUES ('%s', '%s')",
      //Note: we don't need to use quotes for column names.
      mysqli_real_escape_string($con, $passportNo),
      mysqli_real_escape_string($con, $filePath)     
      );
      //echo $sqlStr;

      $queryResult = mysqli_query($con, $sqlStr);
      if(!$queryResult){
        die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
      }
      move_uploaded_file($_FILES["ImageToUpload"]["tmp_name"],"uploads/".$targetFileName);
      displaySuccessMessage($passportNo, $fileFormat); 

    }
    else
    {
      printForm($passportNo); 
      
      //echo "<p> Submission failed, error found: </p>\n";
      // echo "<ul>";
      // foreach($errors as $error){
      //   echo "<li>$error</li>";
      // }
      // echo "</ul>";
      $errorHeader = "<p>Submission failed, error occured</p>";
      displayErrorHeader($errorHeader);
      foreach($errors as $error){          
        displayErrorMessage($error);
      }      
    }
  }
  else
  {
    printForm();
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <style>
  .success-message {
    text-align: center;
    background-color: green;
    color: white;
    padding: 1rem;
  }
  </style>
</head>

<body>
  <?php
      function printForm($ppNo="")
      { 
        // $ppNo = htmlentities($ppNo); // may need to avoid invalid html in case <>" are part of name - Not understood this line till now - need to work on it     
        $form = <<< ANYTEXTHERE
          <div class="containter-fluid align-items-center">
            <form class="container mt-3" action="7addpassport.php" method="post" enctype="multipart/form-data">
              <div class="mb-4 col-6">
                <label for="passportNumber" class="form-label">Passport Number</label>
                <input type="text" name="passportNo" class="form-control" id="passportNumber" value="$ppNo" />
                <span class="form-text">
                  Passport number must be composed of two uppercase letters followed
                  by 6 digits exactly.
                </span>
              </div>

              <div class="col-6">
                <label for="filePath" class="form-label">Upload ImageToUpload</label>
                <input type="file" name="ImageToUpload" class="form-control" id="filePath"/>
                <span class="form-text">
                  ImageToUploads uploaded must be: <br />
                  <li>One of these formats: jpg, gif, png</li>
                  <li>Width and height must be within 200-1000 pixels range</li>
                  <li>Size not larger than 2MB</li>
                </span>
              </div>
              <button type="submit" name="submit" value="Upload Image" class="btn btn-primary mt-3">Submit</button>
            </form>
          </div>
      
      ANYTEXTHERE;
      echo $form;
      } 
      function displaySuccessMessage($ppNo, $ext){
        $successMsg = <<< MSG
          <div class="container mt-3">          
            <form action="7addpassport.php" class="mb-4 col-6">          
              <h4 class="success-message">Upload successful, the image is saved with file name $ppNo.$ext</h4> 
              <input type="submit" name="uploadAgain" value="Upload Next Image" class="btn btn-primary mt-3">
            </form>          
          </div>
        MSG;
        echo $successMsg;        
      }

      function displayErrorHeader($headerStr){                
        $headerStr = <<< HSTR
          <div class="container text-start">
                       
              <h4>$headerStr</h4>
                          
          </div>
          HSTR;
        echo $headerStr;               
      } 
      
      function displayErrorMessage($err){              
        $errorMsg = <<< MSG
          <div class="container text-start">
                      
              <li>$err</li>
                               
          </div>
        MSG;
        echo $errorMsg;               
      } 
      ?>
</body>

</html>

<!-- Note: (Some rules to follow for the HTML form to upload file)
- Make sure that the form uses method="post"
- The form also needs attribute: enctype="multipart/form-data". It specifies which content-type to use when submitting the form,
  without specifying this, the file upload will not work. 
- action ="7addpassport.php" -> Above html form sends data by calling php code on top of this file -->