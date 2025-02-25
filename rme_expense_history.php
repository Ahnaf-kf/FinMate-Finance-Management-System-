<?php
session_start();
include("dbconnect.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT rm.subscription_id, rm.wallet_id, w.organization_name, rm.monthly_payment, 
               CASE WHEN rm.expense_type=1 THEN 'Utilities' ELSE 'Digital Subscription' END AS expense_type, 
               rm.next_payment_date, 
               rm.payment_status
        FROM recurring_monthly_expenses AS rm
        INNER JOIN wallets AS w ON rm.wallet_id = w.wallet_id
        WHERE w.userID = ?
        ORDER BY rm.subscription_id DESC";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurring Expense History</title>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>
<body>
<section id="header">
    <div class="row">
        <div class="col-md-10" style="text-align: right">
            </br>
            <a href="dashboard.php" style="margin-left: 20px;"> Dashboard </a> 
            <a href="recurring_monthly_expenses.php" style="margin-left: 20px;"> Add/Stop Recurring Expenses </a> 
            <a href="index.php" style="margin-left: 20px;"> Log out  </a>
        </div>
    </div>
</section>

<br><br>
<h2>Recurring Expense History</h2>
<table>
    <thead>
        <tr>
            <th>Wallet ID</th>
            <th>Organization Name</th>
            <th>Monthly Payment</th>
            <th>Expense Type</th>
            <th>Payment Status</th>
            <th>Next Payment Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Re-establish database connection
        include("dbconnect.php");

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['wallet_id'] . "</td>";
                        echo "<td>" . $row['organization_name'] . "</td>";
                        echo "<td>৳" . number_format($row['monthly_payment'], 2) . "</td>";
                        echo "<td>" . $row['expense_type'] . "</td>";
                        echo "<td>" . $row['payment_status'] . "</td>";
                        echo "<td>" . $row['next_payment_date'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No recurring expenses available for the logged-in user.</td></tr>";
                }
            } else {
                echo "Error executing statement: " . mysqli_error($conn);
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }

        mysqli_close($conn);
        ?>
    </tbody>
</table>
<br>

</body>
</html>
