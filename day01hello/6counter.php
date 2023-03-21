<?php
        session_start();
        if (!isset($_SESSION['counter'])) {
            $_SESSION['counter'] = 0;
        }
        $_SESSION['counter']++;
        $counter = $_SESSION['counter'];
        echo "<p>You have visited this script $counter time(s) in this web browser session</p>\n";
    ?>