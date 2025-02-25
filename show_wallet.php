<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallets</title>
    <style>
        .hidden {
            display: none;
        }
        table {
            width: 25%;
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
            <div class="col-md-2" style="text-align: center;font-size: 30px;color:#000000;">Wallets </div>
            <br><br>
        <div class="col-md-10" style="text-align: right">
                </br>
                <a href="dashboard.php"> Dashboard </a> 
          <a href="payments.php" style="margin-left: 20px;"> Payments </a> 
          <a href="monthly_budget_plan.php" style="margin-left: 20px;"> Monthly Budget Plan  </a>
                <!-- <a href="recurring_monthly_expenses.php" style="margin-left: 20px;"> Recurring Expenses </a> -->
                <a href="logout.php" style="margin-left: 20px;"> Log out  </a>
            </div>
            <br><br><br>
            <div style="text-align: left">
                <a href="add_wallet.php" style="margin-left: 0px;"> Add Wallet </a>
                <a href="add_fund.php" style="margin-left: 20px;"> Add Fund </a>
                <a href="wallet_transfer.php" style="margin-left: 20px;"> Wallet Transfer </a>
            </div>
        </div>
    </section>
</body>
</html>

<?php
    session_start();
    include('dbconnect.php');

    echo "<h2><br> Added Wallets </h2>"; //add <centre></centre> to centre

    $this_user = $_SESSION['user_id'];

    echo "<table border='1'><tr><th>Wallet ID</th><th>Organization Name</th><th>Category</th><th>Amount</th></tr>";

    $user_wallets = (mysqli_query($conn, "SELECT * FROM wallets WHERE userID='$this_user'"));
    $wallet_list = array();

    while ($arr = mysqli_fetch_assoc($user_wallets)) {
        $wallet_list[] = $arr["wallet_id"];
    }
    $it = 1;
    foreach ($wallet_list as $user_wallet){
        $info=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wallets WHERE wallet_id='$user_wallet'"));
        $wlt_id=$info['wallet_id'];
        $org_name=$info['organization_name'];
        $ctgry=$info['category'];
        $amount=$info['amount'];

        echo "<tr><td>". $it ."</td><td>" . $org_name . "</td><td>". $ctgry ."</td><td>৳" . $amount . "</td></tr>";
        $it++;
    }

?>