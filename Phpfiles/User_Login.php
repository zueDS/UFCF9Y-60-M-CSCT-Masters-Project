<?php

include './DB.php';



$emailz = $_POST["uname"];
$passwordz = $_POST["passwordz"];

$sqlz = "SELECT * FROM user_details where email='$emailz' and passwordz='$passwordz'";
$result = $conn->query($sqlz);

if ($result->num_rows > 0) {

    if ($row = $result->fetch_assoc()) {

        $usertrypez = $row["usertype"];
        if ($usertrypez == "Employer") {
            session_start();
            
            $buyerid = $row["id_user"];
            $buyernameinfull = $row["nameinfull"];
            $buyeremail = $row["email"];
            $buyerstatuz = $row["usertype"];

            $_SESSION["user_id"] = $buyerid;
            $_SESSION["nameinfull"] = $buyernameinfull;
            $_SESSION["emailz"] = $buyeremail;
            $_SESSION["passz"] = $passwordz;
            $_SESSION["btypez"] = $buyerstatuz;
            echo 'Employer';
            
        } else if ($usertrypez == "Designer") {
            session_start();
            $buyerid = $row["id_user"];
            $buyernameinfull = $row["nameinfull"];
            $buyeremail = $row["email"];
            $buyerstatuz = $row["usertype"];

            $_SESSION["user_id"] = $buyerid;
            $_SESSION["nameinfull"] = $buyernameinfull;
            $_SESSION["emailz"] = $buyeremail;
            $_SESSION["passz"] = $passwordz;
            $_SESSION["btypez"] = $buyerstatuz;

            echo 'Designer';
        }
    }
} else {

    echo "Invalid";
}
