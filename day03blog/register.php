<?php require_once("db.php")?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registration</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script>
  $(document).ready(function() {
    // alert("It works"); // for testing jQuery is added properly
    $('input[name=desiredUserName]').keyup(function() {
      var userInput = $(this)
        .val(); // this is eqivalent to $('input[name=desiredUserName]').val(); -- here .val() means .value in jQuery
      $('#usernameTaken').load('isusernametaken.php?username=' + userInput)
    });
  });
  </script>
  <link rel="stylesheet" href="css/styles.css?<?php echo time();?>" />
</head>

<body class="containterCentered">
  <?php
    function printRegisterForm($user="", $email="")
    {
      $registerForm = <<< REGFORM
      <h2 class="txtCenter">New User Registration</h2>
      <form class="formAsContainter width400" action="register.php" method="post" enctype="multipart/form-data">
        <!-- username -->
        <div class="rowFlex">         
          <label for="IdDesiredUserName" class="formLabel">Desired username</label>          
          <div class="colFlex">
            <input type="text" name="desiredUserName" id="IdDesiredUserName" class="" value="$user" required>
            <span id="usernameTaken" class=""></span>
          </div>            
        </div>
        <!-- username -->
        <div class="rowFlex">          
            <label for="Idemail" class="formLabel">Your email</label>
            <input type="text" name="emailAddress" id="Idemail" class="" value="$email" required>
        </div>
        <!-- password -->
        <div class="rowFlex">
            <label for="IdPassword" class="formLabel">Password</label>
            <input type="password" name="password" id="IdPassword" class="" aria-describedby="passwordHelpInline" required>
        </div>
        <!-- password repeat -->
        <div class="rowFlex">
            <label for="IdPasswordRepeat" class="formLabel">Password (repeat)</label>
            <input type="password" name="passwordRepeat"  id="IdPasswordRepeat" class="" aria-describedby="passwordHelpInline" required>
        </div>
        <!-- button to Register--> 
            <input type="submit" name="register" value="Register !" class="btnDefault moveRight2" >  
      </form> 
      <div class="width400 colFlex registraionNote">
          <div>
          <h4>Note: </h4>
          <li> User name must be 4 to 20 characters long.</li>
          <li> User name must only consist of lower case letters and numbers. </li>
          <li> Password must be at least 6 characters long </li>
          <li> Password must contain at least one uppercase letter, one lower <br> &nbsp;&nbsp;&nbsp;&nbsp; case letter, and one number or special character.</li> 
          <li> Password must not be longer than 100 characters </li> 
          </div>            
        </div>
      REGFORM;
      echo $registerForm;
    } 
        
    if(isset($_POST['register'])){
      $desiredUserId = $_POST["desiredUserName"];
      $userEmail = $_POST["emailAddress"]; 
      $password = $_POST["password"]; 
      $passwordRepeat = $_POST["passwordRepeat"];
    
      $errors =[];
      
      //check is user name start with any lowercase letter and between 4-20 characters long
      if(preg_match('/^[a-z][a-z0-9_]{3,19}$/', $desiredUserId) !==1){
        $errorList[] = "User name mus start with lowercase letters and be 4-20 characters in length";
      }
      else
      {
        $checkSqlStr = sprintf("SELECT * FROM users WHERE userName='%s'",
        mysqli_real_escape_string($con, $desiredUserId));
        $result = mysqli_query($con, $checkSqlStr); 
    
        if(!$result){
          die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
        }
        $userRecord = mysqli_fetch_assoc($result);
        if ($userRecord){
          $errors[]="The user name " . $desiredUserId . " already exist !";
          $desiredUserId="";      
        }
      } 
      //Password must be at least 6 characters long and must contain at least one uppercase letter, one lower case letter, 
      //and one number or special character. It must not be longer than 100 characters. Passwords must match for the user to be created.
      //1. at least one lower case letter
      if(!preg_match('/[a-z]{1,}/',$password)){
        $errors[]="Password must containg at least one lower case letters";
      }
      //2. at least one upper case letter.
      if(!preg_match('/[A-Z]{1,}/',$password)){
        $errors[]="Password must containg at least one upper case letters";
      }
      //3. at least one number
      if(!preg_match('/[0-9]+/',$password)){
        $errors[]="Password must containg at least one number";
      }
      //4. at least one special character
      if(!preg_match('/[*@!#%&()^~_{}]+/',$password)){
        $errors[]="Password must containg at least one special character";
      }
      //5. length of password must be within 6 to 100 character
      if(strlen($password)<6 || strlen($password)>100){
        $errors[]="Password must be at least 6 character long and not longer than 100 characters";
      }
      //6. Both passwords must match;
      if($password !== $passwordRepeat){
        $errors[]="Both passwords must match exactly";
      }
      //email address format check: ^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z]{2,})$
      // if(preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z]{2,})$/',$userEmail) != 1){
      if (filter_var($userEmail, FILTER_VALIDATE_EMAIL) === false) {
        $errors[]="Please verify your email address, it doesn't look valid.";
        $userEmail="";
      }
    
      // if there is no error register
      if(!$errors){ 
        $sqlStr = sprintf("INSERT INTO users (userName, userEmail, userPassword) VALUES ('%s', '%s', '%s')",
        //Note: we don't need to use quotes for column names.
        mysqli_real_escape_string($con, $desiredUserId),
        mysqli_real_escape_string($con, $userEmail),
        mysqli_real_escape_string($con, $password)     
        );
        //echo $sqlStr;
        $queryResult = mysqli_query($con, $sqlStr);
        if(!$queryResult){
          die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
        }
        echo '<div class=successMsg>
                <h4>You have successfully registered</h4>               
                <a href="login.php" class="btnDefault">Click here to continue</a>
              </div>';           
      }  
      else {
        //print form
        printRegisterForm($desiredUserId,$userEmail);   
        echo '<div class="errorMsg errorRegistration">
                <h4>Registration failed, error occured</h4>';                
                foreach ($errors as $error){
                  echo "<li>$error</li>";
                }            
        echo '</div>';
      }
    }
    else
    {
      printRegisterForm();
    }
    ?>
  <script scr=""></script>
</body>

</html>