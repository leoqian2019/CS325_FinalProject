<?php
    // File: graph.php
    // Name: Leo Qian
    // Class: CS325, Jan 2022
    // Final Project
    // Due date: Last day of class
    // Start the session
    session_start();

    // check if user is logged in, if not redirect the user to the login page
    if (isset($_SESSION["email"])&&isset($_SESSION["password"])) {
        $email = $_SESSION["email"];
        $password = $_SESSION["password"];
    }
    else {
        header('Refresh:2; url=login.html');
        echo "Please login in first!";
        exit();
    }

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
            // if authorization is successful, continue with the other action
            if($rows -> rowCount() > 0) {
                $rows = $db -> query("SELECT * FROM user_file WHERE email = $email");
                // if the user has a file attached, continue with the ajax process
                if($rows -> rowCount() > 0) {
                    // get the file name
                    $row = $rows->fetch(PDO::FETCH_NUM);
                    $fileName = $row[1];

                    $file = fopen("$fileName","r");

                    $result = "";

                    if ($file !== FALSE) {
                        while (($line = fgetcsv($file)) !== FALSE) {
                            foreach($line as $element) {
                                $result = $result.$element.",";
                            }
                            $result = substr($result,0,-1).'*';
                        }
                        fclose($file);
                        echo $result;
                    }

                }
                // otherwise redirect the user back to the dashboard
                else {
                    header('Refresh:2; url=dashboard.php');
                    echo "Please upload your file first!";
                    exit();
                }

            }
            // otherwise, redirect the page to the login page
            else {
                // remove all session variables
                session_unset();

                // destroy the session
                session_destroy();

                // if the user account didn't exist, create a new account and redirect the page to login page
                $db = NULL;
                header("Location: login-passwd_error.html",true,301);
                exit();
            }
            
        }
        else {
            // remove all session variables
            session_unset();

            // destroy the session
            session_destroy();

            // if the user account didn't exist, create a new account and redirect the page to login page
            $db = NULL;
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
?>