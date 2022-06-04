<!-- File: login.php
Name: Leo Qian
Class: CS325, Jan 2022
Final Project
Due date: Last day of class -->

<?php
    // Start the session
    session_start();

    // get the email and password from the login page
    $email = $_POST["email"];
    $password = $_POST["password"];

    // check if the session variable exist
    $sessionSet = isset($_SESSION["email"])&&isset($_SESSION["password"]);

    if ($sessionSet) {
        $email = $_SESSION["email"];
        $password = $_SESSION["password"];
    }

    // format check
    $email_match = preg_match('/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/',$email);
    $password_match = preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',$password);

    // if the format doesn't match, redirect to the error page
    if ($email_match==0 || $password_match ==0) {
        // remove all session variables
        session_unset();

        // destroy the session
        session_destroy();

        // echo file_get_contents('signup-error.html');
        header("Location: login-format_error.html",true,301);
        exit();
    }
    else {
        try {
            $db = new PDO("mysql:dbname=zqian23;host=localhost","zqian23","px8jhkq2ct");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $rows = $db -> query("SELECT * FROM login_info WHERE email = '$email'");
            // if the user existed in the database
            if($rows -> rowCount() > 0) {
                $row = $rows->fetch(PDO::FETCH_NUM);
                $match = $password == $row[2];

                // close the connection
                $db = NULL;
                // if password matches
                if ($match) {
                    // set session variable 
                    $_SESSION["email"] = $email;
                    $_SESSION["password"] = $password;

                    // if session is alive, inform the user
                    if (sessionSet) {
                        header('Refresh:3; url=dashboard.php');
                        echo "Your login is processed using your saved information. Please signout if you want to use new account.";
                    }
                    else {
                        // redirect to the dashboard with user's email
                        header("Location: dashboard.php",true,301);
                    }
                    
                    exit();
                }
                else {
                    // if password didn't match, redirect to login page to enter new password
                    // remove all session variables
                    session_unset();

                    // destroy the session
                    session_destroy();

                    header("Location: login-passwd_error.html",true,301);
                    exit();
                }
                

            }
            else {
                // close the connection
                $db = NULL;
                // if the user account didn't exist, redict to singup page
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