<!-- File: signup.php
Name: Leo Qian
Class: CS325, Jan 2022
Final Project
Due date: Last day of class -->

<?php
    // Start the session
    session_start();

    // if the session variable exist, redirect to the login process
    if (isset($_SESSION["email"])&&isset($_SESSION["password"])) {
        header('Refresh:2; url=login.php');
        echo "Your login status is alive, now proceed with the login process.";
        exit();
    }

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // server-end format check
    $username_match = preg_match('/^[a-zA-Z0-9]{6,}$/',$username);
    $email_match = preg_match('/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/',$email);
    $password_match = preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',$password);

    // if information entered doesn't match the desired format, echo the error page
    if ($username_match==0 || $email_match==0 || $password_match ==0) {
        header("Location: signup-error.html",true,301);
        exit();
    }
    else {
        try {
            $db = new PDO("mysql:dbname=zqian23;host=localhost","zqian23","px8jhkq2ct");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // determine if the user exist based on email
            $rows = $db -> query("SELECT * FROM login_info WHERE email = '$email'");

            // if the user existed in the database, redirect to the login page
            if($rows -> rowCount() > 0) {
                $db = NULL;
                header("Location: login-repeated_user.html",true,301);
                exit();
            }
            else {
                // if the user account didn't exist, create a new account and redirect the page to login page
                $username = $db -> quote($username);
                $email = $db -> quote($email);
                $password = $db -> quote($password);

                $query = "INSERT INTO login_info (username,email,password)
                VALUES($username,$email,$password)";
                $rows = $db -> exec($query);
                $db = NULL;
                header("Location: login.html",true,301);
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