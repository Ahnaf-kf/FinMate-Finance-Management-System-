<?php 
  session_start();
  include("dbconnect.php");
?>

<?php

$catagory = $paymentID = $walletID = $payment_amount = $tag = '';
$catagoryERR = $paymentIDERR = $walletIDERR = $payment_amountERR = $tagERR = '';
$user_id = $_SESSION['user_id'];
if(isset($_POST['pay'])) {
  if(empty($_POST['catagory'])) {
    $catagoryERR = 'Catagory is required';
  } else {
    $catagory = $_POST['catagory'];
  }
}
// if(isset($_POST['pay'])) {
//   if(empty($_POST['paymentID'])) {
//     $paymentIDERR = 'PaymentID is required';
//   } else {
//     $paymentID = $_POST['paymentID'];
//   }
// }
if(isset($_POST['pay'])) {
  if(empty($_POST['walletID'])) {
    $walletIDERR = 'WalletID is required';
  } else {
    $walletID = $_POST['walletID'];
  }
}
if(isset($_POST['pay'])) {
  if(empty($_POST['payment_amount'])) {
    $payment_amountERR = 'Payment amount is required';
  } else {
    $payment_amount = $_POST['payment_amount'];
  }
}
if(isset($_POST['pay'])) {
  if(empty($_POST['tag'])) {
    $tagERR = 'Description is required';
  } else {
    $tag = $_POST['tag'];
  }
}

$user_wallets = (mysqli_query($conn, "SELECT * FROM wallets WHERE userID='$user_id'"));
$wallet_list = array();

while ($arr = mysqli_fetch_assoc($user_wallets)) {
    $wallet_list[] = $arr["wallet_id"];
}

if (empty($catagoryERR) && empty($walletIDERR) && empty($payment_amountERR) && empty($tagERR) && !empty($catagory) && !empty($walletID) && !empty($payment_amount) && !empty($tag)) {
  // add to database
  $sql = "UPDATE wallets SET amount = amount - '$payment_amount' WHERE wallet_ID = '$walletID' and userID='$user_id'";
  if (mysqli_query($conn, $sql)) {

  } else {
    echo "Error";
  }
  $sql = "INSERT INTO payments (category, payment_status, tag, payment_amount, wallet_id) VALUES ('$catagory', 'Done', '$tag', '$payment_amount', '$walletID')";

  if (mysqli_query($conn, $sql)) {
    echo "Payment Successful!";
  } else {
    echo "Error";
  }
}
echo $catagoryERR;
//echo $paymentIDERR;
echo $walletIDERR;
echo $payment_amountERR;
echo $tagERR;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
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
        <div class="col-md-2" style="text-align: center;font-size: 30px;color:#000000;"> Payments </div>
        <br><br>
        <div class="col-md-10" style="text-align: right">
          </br>
          <a href="dashboard.php"> Dashboard </a> 
          <a href="show_wallet.php" style="margin-left: 20px;"> Wallets </a>
          <a href="monthly_budget_plan.php" style="margin-left: 20px;"> Monthly Budget Plan </a>
          <a href="recurring_monthly_expenses.php" style="margin-left: 20px;"> Recurring Expenses </a>
          <a href="logout.php" style="margin-left: 20px;"> Log out  </a>
        </div>
      </div>
    </section>

    <br><br><br>
    <div class="title" style="font-size: 20px;"><b> Payment Informations </b></div><br>
    <form action="payments.php" class="form_design" method="post">
      Category: <input type="text" name="catagory" placeholder="Enter Category"> <br/>
      <br/>
      <!-- Payment ID: <input type="text" name="paymentID" placeholder="Enter Payment ID"> <br/>  -->
      <!-- Wallet ID: <input type="text" name="walletID" placeholder="Enter Wallet ID"> <br/> -->
      Wallet ID: <select name="walletID">
                      <option value="">Choose Wallet</option>
                      <?php
                      $it = 1;
                      foreach ($wallet_list as $user_wallet){
                        $info=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wallets WHERE wallet_id='$user_wallet'"));
                        $wlt_id=$info['wallet_id'];
                        $org_name=$info['organization_name'];
                        $acc_amnt=$info['amount'];
                        echo "<option value='$wlt_id'>$it - $org_name - ৳$acc_amnt</option>";
                        $it++;

                      }
                      ?>
                  </select></br>
      <br/>
      Payment amount: <input type="text" name="payment_amount" placeholder="Enter Amount"> <br/>
      <br/>
      Description: <input type="text" name="tag" placeholder="Enter Description"> <br/><br/>
      <input type="submit" value="Pay" name='pay'> 
    </form>

</body>
</html>

<?php
    // session_start();
    // include('dbconnect.php');

    echo "<h2><br> Added Wallets </h2>"; //add <centre></centre> to centre

    $this_user = $_SESSION['user_id'];

    echo "<table border='1'><tr><th>Serial</th><th>Payment ID</th><th>Wallet ID</th><th>Category</th><th>Payment Status</th><th>Tag</th><th>Payment Amount</th></tr>";
    $user_payments = mysqli_query($conn, "SELECT * FROM wallets w INNER JOIN payments p ON w.wallet_id = p.wallet_id WHERE userID = '$this_user'");
    $payments_list = array();

    while ($arr = mysqli_fetch_assoc($user_payments)) {
        $payments_list[] = $arr["payment_id"];
    }

    $it = 1;
    foreach ($payments_list as $user_payments){
        $info=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wallets w INNER JOIN payments p  ON w.wallet_id = p.wallet_id WHERE p.payment_id = '$user_payments'"));
        $wlt_id=$info['wallet_id'];
        $pmnt_id=$info['payment_id'];
        $pmnt_status=$info['payment_status'];
        $ctgry=$info['category'];
        $pmnt_amount=$info['payment_amount'];
        $tg = $info['tag'];


        echo "<tr><td>". $it ."</td><td>" . $pmnt_id . "</td><td>". $wlt_id ."</td><td>" . $ctgry . "</td><td>" . $pmnt_status . "</td><td>" . $tg . "</td><td>৳" . $pmnt_amount . "</td></tr>";
        $it++;
    }

?>