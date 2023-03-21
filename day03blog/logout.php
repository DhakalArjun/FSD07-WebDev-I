<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css?<?php echo time();?>" />
  <title>Logout</title>
</head>

<body>
  <div class=" containterCentered successMsg">
    <?php
        // session_reset();
        unset($_SESSION['blogUser']);
        ?>
    <h4>You've successfully logged out. <br><br><a href="index.php" class="btnDefault">Click to continue</a></h4>
  </div>
</body>

</html>