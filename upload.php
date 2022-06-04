<!-- 
    File: upload.php
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

    // if the user clicked the button
    if(isset($_POST["upload"])){
        $fileName = $_FILES["fileToUpload"]["name"];
        $fileError = $_FILES["fileToUpload"]["error"];
        $fileSize = $_FILES["fileToUpload"]["size"];
        $fileTempLocation = $_FILES["fileToUpload"]["tmp_name"];

        // get the file extension
        $fileExt = explode('.',$fileName);
        $fileExt = strtolower(end($fileExt));

        // if the file extension is correct
        if ($fileExt == "csv"){
            // if there's no error uploading the file
            if ($fileError === 0) {
                if ($fileSize <= 500000) {
                    $uploadOk = 1;
                    // set file name to a unique one and upload the file
                    $fileName = uniqid("",true).".csv";
                    $fileDest = 'user_files/'.$fileName;
                    move_uploaded_file($fileTempLocation,$fileDest);

                    // if there's no issue with the format, proceeed with the database operation
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
                            // if authorization is successful, insert the file name into the user_file database and redirect the user back to dashboard
                            if($rows -> rowCount() > 0) {
                                $fileDest = $db -> quote($fileDest);
                                $rows = $db -> exec("INSERT INTO user_file (email,file_name) VALUES ($email,$fileDest)");

                                header('Refresh:2; url=dashboard.php');
                                echo "File upload successful!";
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

                    
                }
                else {
                    $uploadOk = 0;
                    header('Refresh:2; url=dashboard.php');
                    echo "File is too big, please reduce the file size.";
                }
                

            }
            else {
                $uploadOk = 0;
                header('Refresh:2; url=dashboard.php');
                echo "An error occured when uploading your file, please upload again";
            }
        }
        else {
            $uploadOk = 0;
            header('Refresh:2; url=dashboard.php');
            echo "Your file type is not csv, please upload the correct file type!";
        }


    }
    else {
        header("Location: dashboard.php",true,301);
    }
?>