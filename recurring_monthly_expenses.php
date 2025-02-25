<?php
session_start();
include("dbconnect.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Store wallet details as an associative array-----------------------------------------------------
$sql_wallets = "SELECT wallet_id, organization_name, category FROM wallets WHERE userID = ?";
if ($stmt_wallets = mysqli_prepare($conn, $sql_wallets)) {
    mysqli_stmt_bind_param($stmt_wallets, "i", $user_id);
    mysqli_stmt_execute($stmt_wallets);
    mysqli_stmt_store_result($stmt_wallets);
    $num_rows_wallets = mysqli_stmt_num_rows($stmt_wallets);
    if ($num_rows_wallets > 0) {
        $wallet_list = array();
        mysqli_stmt_bind_result($stmt_wallets, $wallet_id, $organization_name, $category);
        while (mysqli_stmt_fetch($stmt_wallets)) {
            $wallet_list[] = array(
                "wallet_id" => $wallet_id,
                "organization_name" => $organization_name,
                "category" => $category
            );
        }
    }
    mysqli_stmt_close($stmt_wallets);
} else {
    echo "Error fetching wallet IDs: " . mysqli_error($conn);
}

// Check user wallet registered-----------------------------------------------------
$sql_check_wallet = "SELECT wallet_id FROM wallets WHERE userID = ?";
if ($stmt_check_wallet = mysqli_prepare($conn, $sql_check_wallet)) {
    mysqli_stmt_bind_param($stmt_check_wallet, "i", $user_id);
    mysqli_stmt_execute($stmt_check_wallet);
    mysqli_stmt_store_result($stmt_check_wallet);
    $num_rows = mysqli_stmt_num_rows($stmt_check_wallet);
    if ($num_rows == 0) {
        echo "No wallet added yet.";
        exit();
    }
    mysqli_stmt_close($stmt_check_wallet);
} else {
    echo "Error checking wallet: " . mysqli_error($conn);
    exit();
}

// Check wallet is empty------------------------------------------------------------------
$sql_check_wallet_empty = "SELECT amount FROM wallets WHERE userID = ? LIMIT 1";
if ($stmt_check_wallet_empty = mysqli_prepare($conn, $sql_check_wallet_empty)) {
    mysqli_stmt_bind_param($stmt_check_wallet_empty, "i", $user_id);
    mysqli_stmt_execute($stmt_check_wallet_empty);
    mysqli_stmt_store_result($stmt_check_wallet_empty);
    mysqli_stmt_bind_result($stmt_check_wallet_empty, $wallet_amount);
    mysqli_stmt_fetch($stmt_check_wallet_empty);
    if ($wallet_amount == 0) {
        echo "Wallet is empty.";
        exit();
    }
    mysqli_stmt_close($stmt_check_wallet_empty);
} else {
    echo "Error checking wallet balance: " . mysqli_error($conn);
    exit();
}

$wallet_id = $payment_status = $monthly_payment = $descriptions = $shared_percentage = $media_company = $expense_type = "";
$monthly_payment_err = "";

// Excute forms--------------------------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_wallet_id = trim($_POST["wallet_id"]);
    if (empty($input_wallet_id)) {
        $monthly_payment_err = "Please, enter wallet ID.";
    } else {
        $wallet_id = $input_wallet_id;
    }

    $payment_status = trim($_POST["payment_status"]);

    $input_monthly_payment = trim($_POST["monthly_payment"]);
    if (empty($input_monthly_payment)) {
        $monthly_payment_err = "Please, enter the monthly payment amount.";
    } else {
        $monthly_payment = $input_monthly_payment;
    }

    $expense_type = $_POST["expense_type"];
    if ($payment_status == "Paid") {
        $total_payment_amount = $monthly_payment;
        if ($expense_type == "1") {
            if (!empty($_POST["shared_percentage"])) {
                $shared_percentage = trim($_POST["shared_percentage"]);
                if (!is_numeric($shared_percentage) || $shared_percentage < 0 || $shared_percentage > 100) {
                    echo "Shared percentage must be a valid number between 0 and 100.";
                    exit();
                }
                $total_payment_amount = ($shared_percentage / 100) * $total_payment_amount;
            }
            //---- wallet deduction----------------------------------------------
            $sql_update_wallet = "UPDATE wallets SET amount=amount - ? WHERE wallet_id=?";
            if ($stmt_update_wallet = mysqli_prepare($conn, $sql_update_wallet)) {
                mysqli_stmt_bind_param($stmt_update_wallet, "di", $total_payment_amount, $wallet_id);
                if (!mysqli_stmt_execute($stmt_update_wallet)) {
                    echo "Error updating wallet amount.";
                }
                mysqli_stmt_close($stmt_update_wallet);
            } else {
                echo "Error preparing update statement for wallet amount.";
            }
        } elseif ($expense_type == "0") {
            $sql_update_wallet = "UPDATE wallets SET amount = amount - ? WHERE wallet_id = ?";
            if ($stmt_update_wallet = mysqli_prepare($conn, $sql_update_wallet)) {
                mysqli_stmt_bind_param($stmt_update_wallet, "di", $total_payment_amount, $wallet_id);
                if (!mysqli_stmt_execute($stmt_update_wallet)) {
                    echo "Error updating wallet amount.";
                }
                mysqli_stmt_close($stmt_update_wallet);
            } else {
                echo "Error preparing update statement for wallet amount.";
            }
        }
    }

    // Assign next payment date(first entry)-----------------------------------------
    $next_payment_date = date('Y-m-d', strtotime('+1 month'));

    $sql = "INSERT INTO recurring_monthly_expenses (wallet_id, payment_status, monthly_payment, descriptions, shared_percentage, media_company, expense_type, next_payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "isdsdsis", $param_wallet_id, $param_payment_status, $param_monthly_payment, $param_descriptions, $param_shared_percentage, $param_media_company, $param_expense_type, $next_payment_date);

        $param_wallet_id = $wallet_id;
        $param_payment_status = $payment_status;
        $param_monthly_payment = $monthly_payment;

        if ($expense_type == "1") {
            $descriptions = trim($_POST["descriptions"]);
            $shared_percentage = trim($_POST["shared_percentage"]);
            $param_descriptions = $descriptions;
            $param_shared_percentage = $shared_percentage;
            $param_media_company = NULL;
        } elseif ($expense_type == "0") {
            $media_company = trim($_POST["media_company"]);
            $param_descriptions = NULL;
            $param_shared_percentage = NULL;
            $param_media_company = $media_company;
        }

        $param_expense_type = $expense_type;

        if (mysqli_stmt_execute($stmt)) {
            header("location: recurring_monthly_expenses.php");
            exit();
        } else {
            echo "Error executing main statement: " . mysqli_error($conn);
        }
    }
    mysqli_stmt_close($stmt);
}

$sql = "SELECT CASE WHEN expense_type=1 THEN 'Utilities' ELSE 'Digital Subscription' END AS expense_type_name, 'Paid' AS payment_status, SUM(monthly_payment) AS total_spend 
        FROM recurring_monthly_expenses 
        INNER JOIN wallets ON recurring_monthly_expenses.wallet_id=wallets.wallet_id
        WHERE payment_status='Paid' AND userID = ? 
        GROUP BY expense_type";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    echo "Error in preparing SQL statement: " . mysqli_error($conn);
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Recurring Expense</title>
    <style>
        .hidden {
            display: none;
        }

        table {
            width: 50%;
            border-collapse: collapse;
            border: 1px solid black;
        }

        th,
        td {
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
            <div class="col-md-2" style="text-align: center; font-size: 30px; color:#000000;">
                <a href="recurring_monthly_expenses.php" style="text-decoration: none; color: inherit;">Recurring Expenses</a>
            </div>
            <br><br>
            <div class="col-md-10" style="text-align: right">
                </br>
                <a href="dashboard.php"> Dashboard </a>
                <a href="show_wallet.php" style="margin-left: 20px;"> Wallets </a>
                <a href="payments.php" style="margin-left: 20px;"> Payments </a>
                <a href="monthly_budget_plan.php" style="margin-left: 20px;"> Monthly Budget Plan </a>
                <a href="rme_expense_history.php" style="margin-left: 20px;">Recurring Expense History </a>
                <a href="index.php" style="margin-left: 20px;"> Log out </a>
            </div>
        </div>
    </section>

    <br><br>
    <h2>Add New Recurring Expense</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="wallet_id">Wallet:</label>
        <select name="wallet_id" id="wallet_id" required>
            <option value="">Choose Wallet</option>
            <?php
            if (!empty($wallet_list)) {
                foreach ($wallet_list as $wallet) {
                    $wallet_id = $wallet['wallet_id'];
                    $organization_name = $wallet['organization_name'];
                    $category = $wallet['category'];
                    echo "<option value='$wallet_id'>$wallet_id - $organization_name ($category)</option>";
                }
            } else {
                echo "<option value='' disabled>No wallets found!</option>";
            }
            ?>
        </select><br><br>


        <label for="monthly_payment">Monthly Payment:</label>
        <input type="number" id="monthly_payment" name="monthly_payment" required>
        <span class="error"><?php echo $monthly_payment_err; ?></span><br><br>

        <label for="payment_status">Payment Status:</label>
        <select id="payment_status" name="payment_status" required>
            <option value="">Select Status</option>
            <option value="Pending">Pending</option>
            <option value="Paid">Paid</option>
        </select><br><br>

        <label for="expense_type">Expense Type:</label>
        <select id="expense_type" name="expense_type" required>
            <option value="">Select Expense Type</option>
            <option value="1">Utilities</option>
            <option value="0">Digital_Subscriptions</option>
        </select><br><br>

        <div id="descriptionsDiv" class="hidden">
            <label for="descriptions">Description:</label>
            <input type="text" id="descriptions" name="descriptions"><br><br>
        </div>

        <div id="sharedPercentageDiv" class="hidden">
            <label for="shared_percentage">Preferred Percentage(%):</label>
            <input type="number" id="shared_percentage" name="shared_percentage"><br><br>
        </div>

        <div id="mediaCompanyDiv" class="hidden">
            <label for="media_company_select">Media Company:</label>
            <select id="media_company_select" name="media_company">
                <option value="None">None</option>
                <option value="Netflix">Netflix</option>
                <option value="Amazon Prime Video">Amazon Prime Video</option>
                <option value="Spotify">Spotify</option>
                <option value="Apple TV">Apple TV</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" id="other_media_company" name="other_media_company" placeholder="Enter other media company" style="display: none;">
            <br><br>
        </div>


        <input type="submit" name="add_expense" value="Add Expense" id="addExpenseButton">
        <span id="addExpenseDone" style="display: none;">Done</span>
        <span id="nextPaymentDate" style="display: none;"></span>
    </form>
    <br>

    <br>
    <h2>Total Spend of Monthly Recurring Expenses</h2>
    <table>
        <tr>
            <th>Expense Type</th>
            <th>Payment Status</th>
            <th>Total Spend</th>
        </tr>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['expense_type_name'] . "</td>";
                echo "<td>" . $row['payment_status'] . "</td>";
                echo "<td>৳" . number_format($row['total_spend'], 2) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No recurring expenses available for the logged-in user.</td></tr>";
        }
        ?>
    </table>
    <br>
    <script>
        document.getElementById('expense_type').addEventListener('change', function() {
            var selectedOption = this.value;
            var descriptionsDiv = document.getElementById('descriptionsDiv');
            var sharedPercentageDiv = document.getElementById('sharedPercentageDiv');
            var mediaCompanyDiv = document.getElementById('mediaCompanyDiv');

            if (selectedOption === '1') {
                descriptionsDiv.classList.remove('hidden');
                mediaCompanyDiv.classList.add('hidden');
                sharedPercentageDiv.classList.remove('hidden');
            } else if (selectedOption === '0') {
                descriptionsDiv.classList.add('hidden');
                mediaCompanyDiv.classList.remove('hidden');
                sharedPercentageDiv.classList.add('hidden');
            } else {
                descriptionsDiv.classList.add('hidden');
                mediaCompanyDiv.classList.add('hidden');
                sharedPercentageDiv.classList.add('hidden');
            }
        })
        document.getElementById('media_company_select').addEventListener('change', function() {
            var selectedOption = this.value;
            var otherMediaCompanyInput = document.getElementById('other_media_company');

            if (selectedOption === 'Other') {
                otherMediaCompanyInput.style.display = 'inline';
            } else {
                otherMediaCompanyInput.style.display = 'none';
                otherMediaCompanyInput.value = '';
            }
        });
        document.getElementById('addExpenseButton').addEventListener('click', function() {
            document.getElementById('addExpenseDone').style.display = 'inline';
        });
    </script>
</body>

</html>
