<?php 
  session_start();
  include("dbconnect.php");
?>

<?php

$swID = $dwID = $amnt = '';
$swIDERR = $dwIDERR = $amntERR = '';
$user_id = $_SESSION['user_id'];


if(isset($_POST['transfer'])) {
  if(empty($_POST['swID'])) {
      $swIDERR = 'Sending WalletID is required';
  } else {
      $swID = $_POST['swID'];
  }
}

if(isset($_POST['transfer'])) {
  if(empty($_POST['amnt'])) {
      $amntERR = 'Amount is required';
  } else {
      $amnt = $_POST['amnt'];
  }
}

if(isset($_POST['transfer'])) {
  if(empty($_POST['dwID'])) {
      $dwIDERR = 'Receiver wallet ID is required';
  } else {
        $dwID = $_POST['dwID'];
  }
}

$user_wallets = (mysqli_query($conn, "SELECT * FROM wallets WHERE userID='$user_id'"));
$wallet_list = array();

while ($arr = mysqli_fetch_assoc($user_wallets)) {
    $wallet_list[] = $arr["wallet_id"];
}

// var_dump($swID);
// var_dump($dwID);
// var_dump($amnt);

if (empty($swIDERR) && empty($amntERR)  && empty($dwIDERR) && !empty($swID) && !empty($dwID) && !empty($amnt)) {
  // add to database
  if($swID!=$dwID){
      $sql = "INSERT IGNORE INTO wallet_transfer (source_wallet_id,destination_wallet_id) VALUES ('$swID','$dwID')";
      if (mysqli_query($conn, $sql)) {

      } else {
        echo "Error";
      }

      $sql = "UPDATE wallets SET amount = amount + '$amnt' WHERE wallet_id = '$dwID' and userID='$user_id'";
      if (mysqli_query($conn, $sql)) {
        $done1=true;
      } else {
        echo "Error";
      }
      $sql = "UPDATE wallets SET amount = amount -'$amnt' WHERE wallet_id='$swID' and userID='$user_id'";

      if (mysqli_query($conn, $sql)) {
        $done2=true;
      } else {
        echo "Error";
      }
      if ($done1==true and $done2==true) {
        echo "Transfer Successful!";
      }
    } else {
      echo "Cannot choose the same wallet!";
    }
}
echo $swIDERR;
echo "<br>";
echo $amntERR;
echo "<br>";
echo $dwIDERR;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Transfer</title>
</head>
<body>

    <section id="header">
        <div class="row">
            <br>
            <div class="col-md-2" style="text-align: center;font-size: 30px;color:#000000;"> Wallet Transfer </div>
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
    <div class="title" style="font-size: 20px;"><b> Wallet Transfer </b></div>
    </br>

    <form action="wallet_transfer.php" class="form_design" method="post">
        Source Wallet ID: <select name="swID">
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
        </br>
        Destination Wallet ID: <select name="dwID">
                      <option value="">Choose Wallet</option>
                      <?php
                      $it1 = 1;
                      foreach ($wallet_list as $user_wallet){
                        $info=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wallets WHERE wallet_id='$user_wallet'"));
                        $wlt_id=$info['wallet_id'];
                        $org_name=$info['organization_name'];
                        $acc_amnt=$info['amount'];
                        echo "<option value='$wlt_id'>$it1 - $org_name - ৳$acc_amnt</option>";
                        $it1++;
                      }
                      ?>
                   </select></br>
        </br>
        Transfer Amount: <input type="text" name="amnt" placeholder="Enter Amount"> </br>
        </br>
        <input type="submit" value="Transfer" name="transfer">
    </form>
    </section>

</body>
</html>