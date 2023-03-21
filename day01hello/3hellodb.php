<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <?php
// database details
$dbName="day01";
$dbUser="day01";
$dbPassword="123uvehY(2GwX5]4";
$dbHost="localhost"; // for mac you may need to write 127.0.0.1

//to connect to database: 
$link = @mysqli_connect($dbHost,$dbUser, $dbPassword,$dbName);

//if any erorr happen during connection
if(mysqli_connect_errno()){
  //die: stop further execution below this line and publish this massage - mysqli_error() is the preprogrammed errors for mysql
  die("Fatal Error: Fail to connect to MySQL database - ".mysqli_connect_error());
}

// if name is set in URL then execute with { } else message
if (isset($_GET["name"])) {
  $name = $_GET["name"];
  $age = random_int(1,100);  //this will produce a random variable between 1, 100
  // TODO: talk about SQL Injection
  $sql = sprintf("INSERT INTO friends VALUES (NULL, '%s', '%s')",  //sprintf is same as printf, one difference is it stores values rather than print (to rememeber store print f)
          mysqli_real_escape_string($link, $name), // this is sql injection
          mysqli_real_escape_string($link, $age));
  if (!mysqli_query($link, $sql)) {   // mysli_query($link,$sql) -- is command to transfer data to database, command will execute to test boolean
      die("Fatal error: failed to execute SQL query: " . mysqli_error($link));
  }
  echo "<p>Hello " . $name . "! (from PHP)</p>";   //on successful transfer, publish this message
} else {
  echo "<p>Please provide name=... parameter in the URL</p>";
}
?>
</body>

</html>