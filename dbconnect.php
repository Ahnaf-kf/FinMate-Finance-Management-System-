<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "finmateproj";


$conn = mysqli_connect($dbhost, $dbuser, $dbpass);

if(mysqli_connect_errno()) {
    echo "Connection Fails".mysqli_connect_error();
}else{
    mysqli_select_db($conn, $dbname);
}

?>