<?php

    session_start();
    error_reporting(0);
    include("dbconnect.php");

    echo "<h2 style= 'text-align: center; font-size: 70px;'><br>FinMate</h2>";
    echo "<h2 style= 'text-align: center; font-size: 50px;'>Sign In</h2>";


    if(isset($_POST['user_id']) && isset($_POST['password'])){
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];


        $sql_query = "SELECT * FROM user WHERE userid = '$user_id' AND password = '$password'";

        

        $checker = mysqli_query($conn, $sql_query);

        $result = mysqli_fetch_array($checker);

        if(mysqli_num_rows($checker) > 0){

            $_SESSION['user_id'] = $result['userID'];
            header("Location: dashboard.php");
        }else{
            $msg = "User ID or password is incorrect.<br>";

        }

    }



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
</head>
<body>
    <div style="text-align: center">
        <form action="index.php" method="post">
      Username: <input type="text" name="user_id"> <br/>
            <br/>
      Password: <input type="password" name="password"> <br/> <br/>
            <?php
                if($msg) echo $msg;
            ?>
      <input type="submit" value="Sign In" name='login'> 
    </form>
        <button><a href="register.php"> Register </button><br><br>

        <button><a href="reset_password.php"> Reset Password </button>
    </div>

</body>
</html>