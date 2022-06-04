<!-- 
    File: profile.php
    Name: Leo Qian
    Class: CS325, Jan 2022
    Final Project
    Due date: Last day of class
-->
<?php
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
            // if authorization is successful, assign the username to a variable
            if($rows -> rowCount() > 0) {
                $row = $rows->fetch(PDO::FETCH_NUM);
                $username = $row[0];
                $username = htmlspecialchars($username);
                $email = substr($email,1,-1);
            }
            // otherwise, redirect the page to the login page
            else {
                // remove all session variables
                session_unset();

                // destroy the session
                session_destroy();

                // if the user account didn't exist, create a new account and redirect the page to login page
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

<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>profile</title>
    <link rel="stylesheet" href="page_top.css">
    <link rel="stylesheet" href="profile.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="profile.js"></script>
    <script src="logout.js"></script>
</head>
<body>
    <a href="homepage.html"><img src="csv_pic.png" alt="csv picture" /></a>
    <h1>CSV Parser</h1>
    <details>
        <summary><?=$username?></summary>
        <p><a href="dashboard.php">Dashboard</a></p>
        <p><a href="profile.php">Profile</a></p>
        <p><button id="signout">Sign out</button></p>
    </details>

    <form action="update_profile.php" method="POST" class="form">
        <p>Username</p>
            <input type="text" name="username" id="username" class="textInput" pattern="^[a-zA-Z0-9]{6,}$" title="Your Username should be at lease 6 characters, letter and number only"
            value="<?=$username?>"/>
        <p class="btn"><input type="submit" value="Update Profile" class="btn"></p>
    </form>
</body>
</html>