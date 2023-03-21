<?php require_once("db.php")?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="css/styles.css?<?php echo time();?>" />
</head>

<body class="containterCentered">
  <?php
    function printLoginForm()
    { $loginForm = <<< LOGIN
      <h2 class="txtCenter">User login</h2>
      <form class="formAsContainter width350" action="login.php" method="post" enctype="multipart/form-data">
      <!-- username -->
      <div class="rowFlex">            
          <label for="IdUserName" class="formLabel">Username</label>
          <input type="text" name="userName" id="IdUserName" class="" required>
      </div>
      <!-- password -->
      <div class="rowFlex">
          <label for="IdPassword" class="formLabel">Password</label>
          <input type="password" name="password" id="IdPassword" class="" aria-describedby="passwordHelpInline" required>
      </div>
      <!-- button and anchor tag to Register--> 
          <input type="submit" name="login" value="Login" class="btnDefault">                      
          <p><a href="register.php">No account? Register here</a></p>          
      </form>
      LOGIN;
      echo $loginForm;
    }
    if(isset($_POST['login'])){
      $username = $_POST['userName'];
      $password= $_POST['password'];
    
      $sqlStr = sprintf("SELECT * FROM users WHERE userName='%s'", mysqli_real_escape_string($con, $username));  
      $result = mysqli_query($con, $sqlStr);
      //print_r($result);
    
      if(!$result){
        die("SQL Query Failed: " .mysqli_errno($con));
      }
      $userDetails = mysqli_fetch_assoc($result);
      //print_r($userDetails);
      $caseLoginSuccessful = ($userDetails !=null)&&($userDetails['userPassword']===$password);
    
      if(!$caseLoginSuccessful){    
        printLoginForm();
        echo '<div class="errorMsg width350"><h4>Invalid username or password !</h4></div>';
      } else {
    
        unset($userDetails['password']); //for security reason erase password element from associative array of user details
        $_SESSION['blogUser'] = $userDetails; // declaring a session variable to store user detail except password
        echo '<div class="successMsg">
        <h4>You have successfully logged in !</h4>
        <a href="articleadd.php" class="btnDefault">Post Article</a>&nbsp;&nbsp;
        <a href="index.php" class="btnDefault">Home</a>
        </div>';    
      }
    }else{
      printLoginForm();
    }
  ?>
</body>

</html>