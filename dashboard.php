<!-- 
    File: dashboard.php
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>dashboard</title>
    <link rel="stylesheet" href="page_top.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script src="https://raw.githubusercontent.com/google/palette.js/master/palette.js"></script>
    <script src="logout.js"></script>
    <script src="dashboard.js"></script>
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

    <h2>Hey,<?=$username?><br/>Here's your analysis page.</h2>

    <?php
        try {
            $rows = $db -> query("SELECT * FROM user_file WHERE email = $email");
            // if the user has a file attached, display the content of the file into three sections
            if($rows -> rowCount() > 0) {
                // get the file name
                $row = $rows->fetch(PDO::FETCH_NUM);
                $fileName = $row[1];

                // open the file and start to analyze it
                $file = fopen("$fileName","r");
                
                // create year array
                $yearArr = array();

                while (($line = fgetcsv($file)) !== FALSE){
                    $year = end(explode("/",$line[0]));
                    array_push($yearArr,$year);
                }

                fclose($file);

                // filter out the repeated value of the array and list them in the option tag in descending order
                $yearArr = array_unique($yearArr);
                arsort($yearArr);

                ?>
                <select name="year" id="year">
                    <?php
                        foreach ($yearArr as $year) {
                            ?>
                            <option id = "<?=$year?>"><?=$year?></option>
                            <?php
                        }
                    ?>
                </select>
                <div id = "anualRev">
                    <p>In the year of <span class="year"></span>, you have a total revenue of $<span id='rev'></span></p>
                </div>
                <div id = 'vendor'>
                    <p>Name of Vendor vs. Percentage of orders from these vendors</p>
                </div>
                <div id = 'monAct'>
                    <p>Montly Cashflow of year <span class="year"></span> vs. Each Month</p>
                    <canvas id="line"></canvas>
                </div>
                <?php
            }
            // otherwise, ask the user to upload their first file
            else {
                ?>

                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <p>Select your first csv file to upload:</p>
                    <p><input type="file" name="fileToUpload" id="fileToUpload" accept=".csv"></p>
                    <p><input type="submit" value="Upload" name="upload" id = "upload"></p>
                </form>

                <?php
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
    
</body>
</html>