<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="css/styles.css?<?php echo time();?>" />
</head>

<body class="containterCentered">
  <?php
    function printForm($nameVal="", $ageVal=""){
        $form = <<< END
        <form class="formAsContainter width350">
        <div class="rowFlex">
        <p class="formLabel">Name:</p> <input type="text" name="name" value="$nameVal"><br>
        </div>
        <div class="rowFlex">
        <p class="formLabel">Age:</p> <input type="number" name="age" value="$ageVal"><br>
        </div>         
        <input type="submit" class="btnDefault" value="Say hello">
        </form>
        END;
        echo $form;
    }
    if (isset($_GET["name"])){ //when there is a value in name field but this is only executed once teh button is clicked.
      $name = $_GET["name"];
      $age = $_GET["age"];
      $errorList = [];
      // name must be 2-20 charecters long
      if (strlen($name)<2 || strlen($name)>20){
        $errorList[]="Name must be 2-20 characters long";
        $name="";
      }
      // age must be a integer, 1-150 value
      if(!is_numeric($age) || $age<1 || $age >150){
        $errorList[]="Age must be 1-150";
        $age="";
      }

        if ($errorList){
          printForm($name, $age); 
          echo "<p>Submission failed, errors found:</p>\n";
          echo"<ul>\n";
          foreach($errorList as $error){
            echo "<li>$error</li>\n";           
          
          }
          echo "</ul>\n";
                 
        }
        else{
          echo "<p>Hi $name, your are $age y/o. Nice to meet you.</p>";
        }
    } else {
      printForm();
    }
  ?>
</body>

</html>