<?php require_once("db.php"); ?>;

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
  <!-- script to insert editor on content box-->
  <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
  tinymce.init({
    selector: 'textarea[name=content]'
  });
  </script>
</head>

<body>
  <div class="containterCentered">
    <?php    
     if (!isset($_SESSION['blogUser'])) {
      die("Error: Only authenticated users can post an article");
    } 
    function printCreateNewArticleForm($blogUser="",$title="",$content="")
    { $addArticle = <<< FORMSTART
      <h4 class="txtRight">You are logged in as $blogUser. &nbsp;  <a href="logout.php" class="btnDefault">Logout</a>&nbsp;&nbsp;<a href="index.php" class="btnDefault">Home</a></h4>
      <h2 class="txtCenter">Create new article</h2>
      <form class="formAsContainter width600" action="" method="post" enctype="multipart/form-data">
        <!-- Title -->         
        <div class="rowFlex">
          <label for="IdTitle" class="formLabel">Title</label>
          <input type="text" name="title" id="IdTitle" class="" placeholder="Type title here...." value="$title">
        </div>          
        <!-- content -->
        <div class="rowFlex">            
          <label for="Idcontent" class="formLabel" >Content</label>  
          <textarea name="content" id="IdContent" class="" placeholder="Type your content here...." requried>$content</textarea>            
        </div>
        <!-- button to Register--> 
        <input type="submit" name="create" value="Create" class="btnDefault moveRight-3"> 
      </form>
      FORMSTART;
      echo $addArticle;
    }

    //print_r($_SESSION['blogUser']);
    $blogUser =$_SESSION['blogUser']['userName']; 
    if(isset($_POST['create'])){   //if create button is clicked
      $title=$_POST['title'];
      $content=$_POST['content'];

      // FIXME: sanitize body - 1) only allow certain HTML tags, 2) make sure it is valid html
      // WARNING: If you forget to sanitize the body bad things may happen such as JavaScript injection
      $content = strip_tags($content, "<h3><h4><h5><p><span><pre><ul><ol><li><em><strong><i><b>");
      $title = htmlentities($title);
      $errors = [];
    
      if(strlen($title)<2|| strlen($title)>100){
        $errors[]= "Title must be 2-100 characters long";
        // don't erase title although it doesn't valid as per above
      }
      if(strlen($content)<2|| strlen($content)>10000){
        $errors[]= "Content must be 2-10000 characters long";
        // don't erase content although it doesn't valid as per above
      }
      if(!$errors){
        $userId = $_SESSION['blogUser']['id'];
        $sqlStr = sprintf("INSERT INTO articles (authorId, title, body) VALUES(%d, '%s', '%s')",
        mysqli_real_escape_string($con, $userId),
        mysqli_real_escape_string($con, $title),
        mysqli_real_escape_string($con, $content));    
        //echo $sqlStr;
        $result = mysqli_query($con, $sqlStr);

        if(!$result){
          die("Fatal error: failed to execute SQL query: " . mysqli_error($con));
        }        
        $articleId = mysqli_insert_id($con);
        echo '<div class=successMsg>
                <h4>Article added successfully</h4>               
                <a href="article.php?id='.$articleId.'" class="btnDefault">Click here to view it</a>
              </div>';
      } else{
        printCreateNewArticleForm($blogUser,$title,$content);
        echo '<div class="errorMsg errorAddArticle">
                <h4>Error(s) Occured:</h4>';                
                foreach ($errors as $error){
                  echo "<li>$error</li>";
                }            
        echo '</div>';
      }
    }else{
    printCreateNewArticleForm($blogUser);
    }
   ?>
  </div>
</body>

</html>