<?php require_once 'db.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css?<?php echo time();?>" />
  <title>Home</title>
</head>

<body>
  <div class="containterCentered">
    <?php
        // is someone logged in?
        if (isset($_SESSION['blogUser'])) {
          $user = $_SESSION['blogUser']['userName'];
          echo "<div>
                  <h4 class='txtRight'>You are logged in as $user.
                  <a href='logout.php' class='btnDefault'>Logout</a> &nbsp;<a href='articleadd.php' class='btnDefault'>Post article</a></h4>
                </div>";
        } else {
            echo '<h4 class="txtRight">To post articles and comments <a href="login.php" class="btnDefault">Login</a> or <a href="register.php" class="btnDefault">Register</a></h4>';
        }
        // TODO: implement pagination (maybe)
        
        // function loadArticle($con, $limit){        
        //$sqlStr = "SELECT a.id, a.creationTS, a.title, a.body, u.username FROM articles as a JOIN users as u ON a.authorId = u.id ORDER BY a.id DESC LIMIT $limit";
        $sqlStr = "SELECT a.id, a.creationTS, a.title, a.body, u.username FROM articles as a JOIN users as u ON a.authorId = u.id ORDER BY a.id DESC "; // LIMIT 10;
        $result = mysqli_query($con, $sqlStr);
        if (!$result) {
            die("SQL Query failed: " . mysqli_error($con));
        }
        // echo "<pre>\n";
        while ($article = mysqli_fetch_assoc($result)) {
          $titleNoTags = strip_tags($article['title']);
          $fullBodyNoTags = strip_tags($article['body']);
          $bodyPreview = mb_strimwidth($fullBodyNoTags, 0, 245, " .... .. ..");          
          $postedDate = date('M d, Y \a\t H:i:s', strtotime($article['creationTS']));
          $articleId = $article['id'];
          $author  = $article['username'];
          // print_r($article);
          echo "<div class='indexArticleWrapper width800'>            
                  <h3  class='indexArticleHeading'><a href='article.php?id=$articleId'> $titleNoTags</a></h3>
                  <p class='indexArticleDetail'>Posted by <strong>$author</strong> on <i>$postedDate</i></p>
                  <p class='indexArticleContent'>$bodyPreview</p>
                </div>";
        }
      // } 

        // $limit = 10;
        // loadArticle($con, $limit);      
        echo "<p class='txtCenter width800'><button onclick='' type='button' class='btnDefault'>Load More</button/></p>";        
    ?>
</body>

</html>