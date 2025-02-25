<?php
    session_start();
    error_reporting(0);
    $user_present = $_SESSION["user_present"];
    if($user_present == true){
        echo '<div style="text-align:centre"> USER ID already taken</div>';
    }
    $_SESSION["user_present"] = false;
    echo "<h2 style= 'text-align: center; font-size: 50px;'><br><br>Registration</h2>";
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <div style="text-align: center">
        <br>
        <form action="reg_action.php" method="post">
      UserID: <input type="text" name="user_id"> <br/>
            <br/>
      First Name: <input type="text" name="first_name"> <br/>
            <br/>
            Last Name: <input type="text" name="last_name"> <br/>
            <br/>
            Password: <input type="password" name="password"> <br/>
            <br/>
            Emails [comma separated]: <input type="text" name="emails"> <br/>
            <br/>
            Phones [comma separated]: <input type="text" name="phones"> <br/><br><br>
      <input type="submit" value="Register" name='register'><br><br>
      <button><a href="index.php"> Sign In </button>
    </form>
</div>


</body>
</html>