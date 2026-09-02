<?php


include './DB.php';
session_start();


$des_emailz = $_SESSION["emailz"];

$upname= $_POST["namez"];
$upcontactz= $_POST["contzt"];


$sql = "update user_details set nameinfull='$upname',contactnumber='$upcontactz' where email='$des_emailz'";
if ($conn->query($sql) === TRUE) {

    echo "ok";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}