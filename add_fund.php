<?php 
  session_start();
  include("dbconnect.php");
?>

<?php

$swid = $amnt = '';
$swidERR = $amntERR = '';
$user_id = $_SESSION['user_id'];

if(isset($_POST['Add'])) {
  if(empty($_POST['swid'])) {
      $swidERR = 'WalletID is required';
  } else {
      $swid = $_POST['swid'];
  }
}

if(isset($_POST['Add'])) {
  if(empty($_POST['amnt'])) {
      $amntERR = 'Amount is required';
  } else {
      $amnt = $_POST['amnt'];
  }
}

$user_wallets = (mysqli_query($conn, "SELECT * FROM wallets WHERE userID='$user_id'"));
$wallet_list = array();

while ($arr = mysqli_fetch_assoc($user_wallets)) {
    $wallet_list[] = $arr["wallet_id"];
}

// var_dump($swid);
// var_dump($amnt);

if (empty($swidERR) && empty($amntERR) && !empty($swid) && !empty($amnt)) {
  // add to database
  $sql = "UPDATE wallets SET amount = amount + '$amnt'  WHERE wallet_ID = '$swid' and userID = '$user_id'";

  if (mysqli_query($conn, $sql)) {
    echo "Fund added successfully!";
  } else {
    echo "Error";
  }
}
echo $swidERR;
echo $amntERR;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Fund</title>
</head>
<body>

    <section id="header">
        <div class="row">
            <br>
            <div class="col-md-2" style="text-align: center;font-size: 30px;color:#000000;">Add Fund </div>
            <br><br>
            <div class="col-md-10" style="text-align: right">
                </br>
                <a href="dashboard.php"> Dashboard </a> 
                <a href="show_wallet.php" style="margin-left: 20px;"> Wallets </a> 
                <a href="logout.php" style="margin-left: 20px;"> Log out  </a>
            </div>
        </div>
    </section>

    <section id="section1">
    <div class="title" style="font-size: 20px;"><b> Add Fund </b></div>
    </br>

    <form action="add_fund.php" class="form_design" method="post">
        Wallet ID: <select name="swid">
                      <option value="">Choose Wallet</option>
                      <?php
                      $it = 1;
                      foreach ($wallet_list as $user_wallet){
                        $info=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wallets WHERE wallet_id='$user_wallet'"));
                        $wlt_id=$info['wallet_id'];
                        $org_name=$info['organization_name'];
                        $acc_amnt=$info['amount'];
                        echo "<option value='$wlt_id'>$it - $org_name - ৳$acc_amnt</option>";
                        ++$it;
                      }
                      ?>
                   </select></br>
        </br>
        Add Amount: <input type="text" name="amnt" placeholder="Enter Amount"></br>
        </br>
        <input type="submit" value="Add" name="Add">
    </form>
    </section>

</body>
</html>