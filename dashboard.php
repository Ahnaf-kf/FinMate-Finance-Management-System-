<?php
    session_start();
    include("dbconnect.php");
    //echo "dashboard<br>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinMate</title>
    <style>
        
        table {
            margin-bottom: 20px;
            border-collapse: collapse;
            border: 1px solid black; 
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <section id="header">
        <div class="row">
            <br>
            <div class="col-md-2" style="text-align: center;font-size: 50px;color:#000000;"> Dashboard </div>
            <br><br>
            <br><br><br>
            <div style="text-align: center">
                <a href="show_wallet.php" style="margin-left: 30px; font-size: 20px;"> Wallets </a>
                <a href="payments.php" style="margin-left: 30px; font-size: 20px;"> Payments </a>
                <a href="monthly_budget_plan.php" style="margin-left: 30px; font-size: 20px;"> Budget Plan </a>
                <a href="recurring_monthly_expenses.php" style="margin-left: 30px; font-size: 20px;"> Recurring Expenses </a>
                <a href="logout.php" style="margin-left: 30px; font-size: 20px;"> Log out </a>
            </div>
        </div>
    </section>
</body>
</html>

<?php
    $user_id = $_SESSION['user_id'];

    $user_emails = mysqli_query($conn, "SELECT * FROM user_email WHERE userID = '$user_id'");

    $email_list = array();

    while($temp = mysqli_fetch_assoc($user_emails)){
        $email_list[] = $temp['email'];
    }

    for($i = 0; $i < 3; ++$i) echo "<br>";
    echo "<table border='1' class='table-gap'><tr><th>SL No.</th><th>Registered Emails</th></tr>";
    $it = 1;
    foreach($email_list as $instance){
        echo "<tr><td>". $it ."</td><td>" . $instance . "</td></tr>";
        ++$it;
    }


    for($i = 0; $i < 3; ++$i) echo "<br>";

    $user_phones = mysqli_query($conn, "SELECT * FROM user_phone_no WHERE userID = '$user_id'");

    $phone_list = array();

    while($temp = mysqli_fetch_assoc($user_phones)){
        $phone_list[] = $temp['phone_no'];
    }

    echo "<table border='1' class='table-gap'><tr><th>SL No.</th><th>Registered Phones</th></tr>";
    $it1 = 1;
    foreach($phone_list as $instance){
        echo "<tr><td>". $it1 ."</td><td>" . $instance . "</td></tr>";
        ++$it1;
    }
?>
