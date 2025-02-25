<?php
    session_start();
    include("dbconnect.php");
    //echo "monthly budget plan<br>";
    //var_dump($_SESSION['user_id']);

    $this_user = $_SESSION['user_id'];
    $check_user = "SELECT * FROM monthly_budget_plan where userID = '$this_user'";
    $msg = false;
    $check_user = "SELECT * FROM monthly_budget_plan where userID = '$this_user'";
    $result = mysqli_query($conn, $check_user);
    $is_present = mysqli_num_rows($result) > 0;

    if(mysqli_num_rows($result) == 0){
        $food = $medical = $subscriptions = $vacations = $entertainment = "NOT SET";


        mysqli_query($conn, "INSERT INTO monthly_budget_plan (userID, category, categorical_upper_bound) VALUES ('$this_user', 'food', '$food')");
        mysqli_query($conn, "INSERT INTO monthly_budget_plan (userID, category, categorical_upper_bound) VALUES ('$this_user', 'medical', '$medical')");
        mysqli_query($conn, "INSERT INTO monthly_budget_plan (userID, category, categorical_upper_bound) VALUES ('$this_user', 'subscriptions', '$subscriptions')");
        mysqli_query($conn, "INSERT INTO monthly_budget_plan (userID, category, categorical_upper_bound) VALUES ('$this_user', 'vacations', '$vacations')");
        mysqli_query($conn, "INSERT INTO monthly_budget_plan (userID, category, categorical_upper_bound) VALUES ('$this_user', 'entertainment', '$entertainment')"); 
    }

    if(isset($_POST['set_budgets'])){
        //$all_set = !empty($_POST['food']) && !empty($_POST['medical']) && !empty($_POST['subscriptions']) && !empty($_POST['vacations']) && !empty($_POST['entertainment']);


        $food = $_POST['food'];
        $medical = $_POST['medical'];
        $subscriptions = $_POST['subscriptions'];
        $vacations = $_POST['vacations'];
        $entertainment = $_POST['entertainment'];

        if(empty($food)){
            $food = "NOT SET";
        }
        if(empty($medical)){
            $medical = "NOT SET";
        }
        if(empty($subscriptions)){
            $subscriptions = "NOT SET";
        }
        if(empty($vacations)){
            $vacations = "NOT SET";
        }
        if(empty($entertainment)){
            $entertainment = "NOT SET";
        }

        $catg = array("food" => $food, "medical" => $medical,"$subscriptions" => $food,"vacations" => $vacations,"entertaiment" => $entertainment);


        // foreach($catg as $cat => $bound){

        //     $ins_query = "INSERT INTO monthly_budget_plan ("
        // }

        mysqli_query($conn, "UPDATE monthly_budget_plan SET categorical_upper_bound = '$food' WHERE userID = '$this_user' AND category = 'food'");
        mysqli_query($conn, "UPDATE monthly_budget_plan SET categorical_upper_bound = '$medical' WHERE userID = '$this_user' AND category = 'medical'");
        mysqli_query($conn, "UPDATE monthly_budget_plan SET categorical_upper_bound = '$subscriptions' WHERE userID = '$this_user' AND category = 'subscriptions'");
        mysqli_query($conn, "UPDATE monthly_budget_plan SET categorical_upper_bound = '$vacations' WHERE userID = '$this_user' AND category = 'vacations'");
        mysqli_query($conn, "UPDATE monthly_budget_plan SET categorical_upper_bound = '$entertainment' WHERE userID = '$this_user' AND category = 'entertainment'");     

        // if($all_set){
        //     $msg = "all set<br>";
        //     $_SESSION['food'] = $food;
        //     $_SESSION['medical'] = $medical;
        //     $_SESSION['subscriptions'] = $subscriptions;
        //     $_SESSION['vacations'] = $vacations;
        //     $_SESSION['entertainment'] = $entertainment;

        // }else{
        //     $msg = "All segments not set<br>";     
        // }

    }



    $ext_food = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM monthly_budget_plan WHERE userID = '$this_user' AND category = 'food'"));
    $ext_food = $ext_food['categorical_upper_bound'];
    $ext_medical = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM monthly_budget_plan WHERE userID = '$this_user' AND category = 'medical'"));
    $ext_medical = $ext_medical['categorical_upper_bound'];
    $ext_subscriptions = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM monthly_budget_plan WHERE userID = '$this_user' AND category = 'subscriptions'"));
    $ext_subscriptions = $ext_subscriptions['categorical_upper_bound'];
    $ext_vacations = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM monthly_budget_plan WHERE userID = '$this_user' AND category = 'vacations'"));
    $ext_vacations = $ext_vacations['categorical_upper_bound'];
    $ext_entertainment = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM monthly_budget_plan WHERE userID = '$this_user' AND category = 'entertainment'"));
    $ext_entertainment = $ext_entertainment['categorical_upper_bound'];

    //echo "<h2 style=text-align: center>Budget Plan</h2>";
    echo "<table border='1'><tr><th>Segment Name</th><th>Categorical Upper Bound</th></tr>";
    echo "<tr><td>Food</td><td>" . $ext_food . "</td></tr>";
    echo "<tr><td>Medical</td><td>" . $ext_medical . "</td></tr>";
    echo "<tr><td>Subscriptions</td><td>" . $ext_subscriptions . "</td></tr>";
    echo "<tr><td>Vacations</td><td>" . $ext_vacations . "</td></tr>";
    echo "<tr><td>Entertainment</td><td>" . $ext_entertainment . "</td></tr>";

?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FinMate</title>
        <style>
        
        table {
            margin-top: 20px;
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
                    <div class="col-md-2" style="text-align: center;font-size: 30px;color:#000000;"> Budget Plan </div>
                    <br><br>
                    <div class="col-md-10" style="text-align: right">
                        </br>
                        <a href="dashboard.php"> Dashboard </a> 
                        <a href="show_wallet.php" style="margin-left: 20px;"> Wallets </a>
                        <a href="payments.php" style="margin-left: 20px;"> Payments </a>
                        <!-- <a href="recurring_monthly_expenses.php" style="margin-left: 20px;"> Recurring Expenses </a> -->
                        <a href="logout.php" style="margin-left: 20px;"> Log out  </a>
                    </div>
                </div>
            </section>

            <div class="title" style="font-size: 20px;"><b> Set Monthly Budget </b></div><br>
            <form action="monthly_budget_plan.php" method="post">
                Food: <input type="text" name="food"> <br/>
                <br/>
                Medical: <input type="text" name="medical"> <br/>
                <br/>
                Subscriptions: <input type="text" name="subscriptions"> <br/>
                <br/>
                Vacations: <input type="text" name="vacations"> <br/>
                <br/>
                Entertainment: <input type="text" name="entertainment"> <br/><br/>
                <?php
                    if($msg) echo $msg;
                ?>
                <input type="submit" value="Set Budgets" name='set_budgets'>
            </form>


    </body>
    </html>

