<h3>This is for debugging purposes only</h3>
<?php
    echo "<pre>\n";
    session_start();
   //  $_SESSION['blogUser'] = array("id"  => 5, 'name' => 'Jerry B.', 'email' => 'jerry@jerry.com');
    print_r($_SESSION);
    echo "\n";

    /* // is someone logged in?
    if (isset($_SESSION['blogUser'])) {
        echo "you're logged in as " . $_SESSION['blogUser']['username'];
    } else {
        echo 'not logged in';
    } */
