<?php require_once("db.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Article Add</title>
  <!-- fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,600;0,900;1,400&family=Roboto:wght@400;900&display=swap"
    rel="stylesheet" />
  <!-- css -->
  <link rel="stylesheet" href="css/styles.css?<?php echo time();?>" />
</head>

<body>
  <div class="containterCentered width800">
    <?php
      function printArticleForm($content="")
      { $addCommentForm = <<< FORMSTART
          <form class="formAsContainter width800" action="" method="post" enctype="multipart/form-data">                
          <div class="rowFlex">
            <div class="colFlex txtLeft">
              <label for="IdAddComment" class="">My Comment:</label>
              <input type="submit" id="IdAddComment" name="create" value="Add Comment" class="btnDefault"> 
            </div>  
            <textarea name="content" id="IdCommentContent" class="" placeholder="Type your content here...." requried>$content</textarea>              
          </div>
        </form>
        FORMSTART;
        echo $addCommentForm ;
      }

      if (!isset($_GET['id'])) {
        die("Error: missing article ID in the URL");
      } 
      $id = $_GET['id'];
      $sqlStr = sprintf("SELECT a.id, a.creationTS, a.title, a.body, u.username FROM articles as a JOIN users as u ON a.authorId = u.id AND a.id=%d",
          mysqli_real_escape_string($con, $id));
      $result = mysqli_query($con, $sqlStr);
      if (!$result) {
          die("SQL Query failed: " . mysqli_error($con));
      }
      $article = mysqli_fetch_assoc($result);
      if (!$article) {
          http_response_code(404); // may work or not work, depending on whether output buffering is enabled or not
          echo '<h2>Article not found</h2>';
      } else {
        if (isset($_SESSION['blogUser'])) {
          $blogUser =$_SESSION['blogUser']['userName'];  
          echo "<h4 class='txtRight'>You are logged in as $blogUser. &nbsp;&nbsp;  <a href='logout.php' class='btnDefault'>Logout</a>&nbsp;&nbsp;<a href='index.php' class='btnDefault'>Home</a></h4>";
          $titleNoTags = strip_tags($article['title']);
          $postedDate = date('M d, Y \a\t H:i:s', strtotime($article['creationTS']));
          $author = $article['username'];
          $content =strip_tags($article['body']);
          echo "<div class='articleSection'>         
                  <h2>$titleNoTags</h2>         
                  <p>Posted by <i>$author</i> on <i>$postedDate</i></p>
                  <div class='articleContent'>$content</div>
                </div>";
          // NOT recommended - use Twig (next week)
          // echo "<script> document.title='" . htmlentities($article['title']) . "'; </script>\n";
          printArticleForm();          
        }else{
          echo '<h4 class="txtRight">To post articles and comments <a href="login.php" class="btnDefault">Login</a> or <a href="register.php" class="btnDefault">Register</a></h4>';
          $titleNoTags = strip_tags($article['title']);
          $postedDate = date('M d, Y \a\t H:i:s', strtotime($article['creationTS']));
          $author = $article['username'];
          $content =strip_tags($article['body']);
          echo "<div class='articleSection'>         
                  <h2>$titleNoTags</h2>         
                  <p>Posted by <i>$author</i> on <i>$postedDate</i></p>
                  <div class='articleContent'>$content</div>
                </div>";
        }       
      }
   ?>
  </div>
</body>

</html>