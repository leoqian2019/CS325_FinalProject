<!-- 
    File: logout.php
    Name: Leo Qian
    Class: CS325, Jan 2022
    Final Project
    Due date: Last day of class
-->

<?php
    // Start the session
    session_start();

    // remove all session variables
    session_unset();

    // destroy the session
    session_destroy();

    header("Location: homepage.html",true,301);
    exit();

?>