<?php
// reset_password.php
session_start();
include("dbconnect.php");
// $servername = "localhost"; 
// $username_db = "root"; 
// $password_db = ""; 
// $dbname = "sam";

// // Create connection
// $conn = new mysqli($servername, $username_db, $password_db, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST["userID"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $newPassword = $_POST["newPassword"]; // The new password

    $stmt = $conn->prepare("SELECT * FROM user WHERE userID = ? AND first_name = ? AND last_name = ?");
    $stmt->bind_param("sss", $userID, $firstName, $lastName); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User exists, proceed with updating the password
        $updateStmt = $conn->prepare("UPDATE user SET password = ? WHERE userID = ? AND first_name = ? AND last_name = ?");
        $updateStmt->bind_param("ssss", $newPassword, $userID, $firstName, $lastName); 
        $updateStmt->execute();

        if($updateStmt->affected_rows === 1) {
            echo "Password successfully updated.";
        } else {
            echo "Failed to update password. It may already be up to date or the new password is the same as the old.";
        }

        $updateStmt->close();
    } else {
        echo "No user found with the provided details.";
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
echo "<h2 style= 'text-align: center; font-size: 50px;'><br><br>Reset Password</h2>";

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    </style> -->
</head>
<body>
    <!-- <h2>Reset Password</h2> -->
    <br>
    <div style="text-align: Center">
      <form action="reset_password.php" method="POST">
          <div>
              <label for="userID">User ID:</label>
              <input type="text" id="userID" name="userID" required>
          </div>
          <div>
              <label for="firstName">First Name:</label>
              <input type="text" id="firstName" name="firstName" required>
          </div>
          <div>
              <label for="lastName">Last Name:</label>
              <input type="text" id="lastName" name="lastName" required>
          </div>
          <div>
              <label for="newPassword">New Password:</label>
              <input type="password" id="newPassword" name="newPassword" required>
          </div>
          <button type="submit">Reset Password</button><br><br>
          <button><a href="index.php"> Sign In </button>
      </form>
    </div>
</body>
</html>


