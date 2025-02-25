<?php 
  session_start();
  include("dbconnect.php");
?>

<?php

$wid = $oname = $cat = $amnt = '';
$widERR = $onameERR = $catERR = $amntERR = '';
$user_id = $_SESSION['user_id'];

// if(isset($_POST['add'])) {
//   if(empty($_POST['wid'])) {
//       $widERR = 'WalletID is required';
//   } else {
//       $wid = $_POST['wid'];
//   }
// }
if(isset($_POST['add'])) {
  if(empty($_POST['oname'])) {
      $onameERR = 'Organization name is required';
  } else {
      $oname = $_POST['oname'];
  }
}
if(isset($_POST['add'])) {
  if(empty($_POST['cat'])) {
      $catERR = 'Catagory is required';
  } else {
      $cat = $_POST['cat'];
  }
}
if(isset($_POST['add'])) {
  if(empty($_POST['amnt'])) {
      $amntERR = 'Amount is required';
  } else {
      $amnt = $_POST['amnt'];
  }
}

// var_dump($oname);
// var_dump($cat);
// var_dump($amnt);
// var_dump($wid);

if (empty($onameERR) && empty($catERR) && empty($amntERR)&& !empty($oname) && !empty($cat) && !empty($amnt)) {
  // add to database
  $sql = "INSERT INTO wallets (userID, organization_name, category, amount) VALUES ('$user_id', '$oname', '$cat', '$amnt')";

  if (mysqli_query($conn, $sql)) {
    echo "Wallet added successfully!";
  } else {
    echo "Error";
  }
}
echo $widERR;
echo $onameERR;
echo $catERR;
echo $amntERR;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="About the site"/>
    <meta name="author" content="Author name"/>
    <title>Add Wallet</title>
</head>
<body>
    <section id="header">
        <div class="row">
            <br>
            <div class="col-md-2" style="text-align: center;font-size: 30px;color:#000000;">Add Wallet </div>
            <br><br>
            <div class="col-md-10" style="text-align: right">
                </br>
                <a href="dashboard.php"> Dashboard </a> 
                <a href="show_wallet.php" style="margin-left: 20px;"> Wallets </a> 
                <a href="logout.php" style="margin-left: 20px;"> Log out </a>
            </div>
        </div>
    </section>

    <section id = "section1">
        <br><br>
        <div class="title" style="font-size: 20px;"><b> Wallet Information</b> </div>
        </br>
        <form action="add_wallet.php" class="form_design" method="post">
            Organization Name: <input type="text" name="oname" placeholder="Enter Organization Name"> </br>
            </br>
            Category: <input type="text" name="cat" placeholder="Enter Category"> </br>
            </br>
            Amount: <input type="text" name="amnt" placeholder="Enter Amount"> </br>
            </br>
            <input type="submit" value="Add to Wallets" name="add">
        </form>
    </section>
</body>
</html>