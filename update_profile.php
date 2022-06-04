<!-- 
    File: update_profile.php
    Name: Leo Qian
    Class: CS325, Jan 2022
    Final Project
    Due date: Last day of class
-->
<?php
    // Start the session
    session_start();

    // check if user is logged in, if not redirect the user to the login page
    if (isset($_SESSION["email"])&&isset($_SESSION["password"])){
        $email = $_SESSION["email"];
        $password = $_SESSION["password"];
    }
    else {
        header('Refresh:2; url=login.html');
        echo "Please login in first!";
        exit();
    }
    
    // get the new username and check the format
    $newUsername = $_POST["username"];
    $username_match = preg_match('/^[a-zA-Z0-9]{6,}$/',$newUsername);

    // if the format doesn't match, redirect the user and print out error message
    if ($username_match==0) {
        header('Refresh:3; url=profile.php');
        echo "Your username format is not allowed, please enter again!";
        exit();
    }
    else {
        try {
            $db = new PDO("mysql:dbname=zqian23;host=localhost","zqian23","px8jhkq2ct");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // determine if the user exist based on email
            $email = $db -> quote($email);
            $password = $db -> quote($password);
            $rows = $db -> query("SELECT * FROM login_info WHERE email = $email");

            // if the user existed in the database, reauthorzing the user
            if($rows -> rowCount() > 0) {
                $rows = $db -> query("SELECT * FROM login_info WHERE email = $email and password = $password");
                // if authorization is successful, proceed with the update profile process
                if($rows -> rowCount() > 0) {
                    $newUsername = $db -> quote($newUsername);
                    $rows = $db -> exec("UPDATE login_info set username = $newUsername where email = $email and password = $password");
                    header('Refresh:3; url=profile.php');
                    echo "Your profile change is successful";
                    exit();
                }
                // otherwise, redirect the page to the login page
                else {
                    // remove all session variables
                    session_unset();

                    // destroy the session
                    session_destroy();

                    header("Location: login-passwd_error.html",true,301);
                    exit();
                }
            }
            else {
                // remove all session variables
                session_unset();

                // destroy the session
                session_destroy();

                // if the user account didn't exist based on email, ask them to sign up
                header("Location: signup-nonexist.html",true,301);
                exit();
            }
        }
        // if any error occurs, print out the error message
        catch (PDOException $ex) {
            ?>
            <p>Sorry, a database error occurred. Please try again later.</p>
            <p>(Error details: <?= $ex->getMessage() ?>)</p>
            <?php
        }
    }
?>