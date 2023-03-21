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
  //calling db.php to connect to database
  require_once('db.php');

  function createForm($taskVal="", $difficultyVal="", $isDoneVal=""){
    $diffArray=['Easy'=>'', 'Medium'=>'', 'Hard'=>''];
    $diffArray[$difficultyVal] = 'checked';
    $isDoneChecked=$isDoneVal?'checked':'';
    $todoForm = <<< ANYTEXT
      <form method="post" class="formAsContainter width300 txtLeft"> 
        <div class="rowFlexLeftAlign">
        <label for="task" class="formLabel">Task:</label><input type="text" name="task" value="$taskVal" />
        </div>  
        <div class="rowFlexLeftAlign">
        <label for="taskDifficulty" class="formLabel">Difficulty:</label>
        <input type="radio" name="taskDifficulty" value="Easy" {$diffArray['Easy']} />Easy
        <input type="radio" name="taskDifficulty" value="Medium" {$diffArray['Medium']} />Medium
        <input type="radio" name="taskDifficulty" value="Hard" {$diffArray['Hard']} />Hard
        </div>  
        <div class="rowFlexLeftAlign">
        <label for="isDone" class="formLabel">IsDone?:</label>
        <input type="checkbox" name="isDone" value="1" $isDoneChecked />
        </div>
        <input type="submit" value="Create task">      
      </form>
    ANYTEXT;
    echo $todoForm;
  }
  if(isset($_POST["task"])){
    // following four lines of code will print list of values for POST method to verify if form is able to collect data. after verification just comment them
    // echo "<pre>\n";
    // echo "$_POST:"."\n";
    // print_r($_POST);
    // echo "</pre>\n";

    $task = $_POST["task"];
    $taskDificulty = $_POST["taskDifficulty"];
  // $isDone = $_POST["isDone"]; my error
  $isDone = isset($_POST["isDone"]);
    $errorList=[];

    //the task description is between 2-50 characters long
    if(strlen($task)<2 || strlen($task)>50){
      $errorList[]="Task must be 2-50 characters long";
      $taskVal="";
    }
    //to make sure that taskDifficulty is one of the values expected (Actually, this not for client, but for integrety of forntend and backend)
    if(!in_array($taskDificulty,['Easy', 'Medium', 'Hard'])){
      $errorList[]="Task difficulty value invalid (possibly internal error)";
    }
    if($errorList){
      echo "<p> Submission failed, error found: </p>\n";
      echo "<ul>\n";
      foreach($errorList as $error){
        echo "<li>$error</li>";
      }
      echo "</ul>\n";
      createForm($task, $taskDificulty, $isDone);
    }
    else{

      $sqlStr = sprintf("INSERT INTO todos (task, difficulty, isDone) VALUES ( '%s', '%s', '%d')",
      //Note: we don't need to use quotes for column names.
      mysqli_real_escape_string($con, $task),
      mysqli_real_escape_string($con, $taskDificulty),
      mysqli_real_escape_string($con, $isDone)
      );

      $queryResult = mysqli_query($con, $sqlStr);
      if(!$queryResult){
        die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
      }
      // echo "One record added\n";    //Here\n doesnot add new line while instead it will add just a white space as you're outputting HTML, and in HTML, "\n" is treated as whitespace
      // echo "This is a $taskDificulty task $task and is " . (!$isDone?"not":"") . " done";

      echo "One record added, <br>"  . "This is a $taskDificulty task $task and is " . (!$isDone?"not":"") . " done";  
      
    }
  }
  else {
    
    createForm($taskVal="", $difficultyVal="Easy", $isDoneVal="");
  }
?>
</body>

</html>