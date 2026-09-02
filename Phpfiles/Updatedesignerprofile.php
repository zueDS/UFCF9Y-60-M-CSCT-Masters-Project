<?php

include './DB.php';
session_start();


$des_emailz = $_SESSION["emailz"];

$upname= $_POST["namez"];
$upcontactz= $_POST["contzt"];
$updesx= $_POST["textz"];


$sql = "update user_details set nameinfull='$upname',contactnumber='$upcontactz',skills='$updesx' where email='$des_emailz'";
if ($conn->query($sql) === TRUE) {

    echo "ok";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}