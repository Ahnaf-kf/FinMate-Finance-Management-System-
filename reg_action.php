<?php

    include("dbconnect.php");
    session_start();
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $emails = $_POST['emails'];
    $phones = $_POST['phones'];


    $all_info_given = $_POST['user_id'] != "" && $_POST['first_name'] != "" && $_POST['last_name'] != "" && $_POST['password'] != "";

    $dup_check = "SELECT * FROM user WHERE userID = '$user_id'";
    $res = mysqli_query($conn, $dup_check);

    if(mysqli_num_rows($res) > 0){
        $_SESSION["user_present"] = true;
        header("Location: register.php");
    }
    $SESSION["user_present"] = false;

    if($all_info_given){

            $reg_query = "INSERT INTO user (userID, first_name, last_name, password) VALUES ('$user_id', '$first_name', '$last_name', '$password')";
            $success = mysqli_query($conn, $reg_query);

            if($success){

                if($emails != ""){
                    $email_array = explode(',', $emails);
                    $email_array = array_map('trim', $email_array);

                    foreach ($email_array as $eml){
                        $ins_query_email = "INSERT INTO user_email (userID, email) VALUES ('$user_id', '$eml')";
                        mysqli_query($conn, $ins_query_email);
                    }
                }

                if($phones != ""){
                    $phone_array = explode(',', $phones);
                    $phone_array = array_map('trim', $phone_array);

                    foreach ($phone_array as $phn){
                        $ins_query_phone = "INSERT INTO user_phone_no (userID,  phone_no) VALUES ('$user_id', '$phn')";
                        mysqli_query($conn, $ins_query_phone);
                    }
                }
                header("Location: index.php");

            }else{

                header("Location: register.php");
            }

    }else{

        header("Location: register.php");

    }
?>