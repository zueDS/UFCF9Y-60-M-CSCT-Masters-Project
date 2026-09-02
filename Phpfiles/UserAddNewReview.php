<?php
include './DB.php';
session_start();
$emp_id = $_SESSION["user_id"];
$emp_name = $_SESSION["nameinfull"];
$emp_emailz = $_SESSION["emailz"];



$review_comment = $_POST["reviewcomment"];
$rating_countz = $_POST["ratingz"];
$des_idz = $_POST["desid"];
$des_namez = $_POST["desname"];
$des_emailzz= $_POST["des_emailz"];

$date = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
$todaydate = $date->format('Y-m-d');
$todaytime = $date->format('H:i:s');

$statuz = "Active";
$sqlloadz = "INSERT INTO `itemhasreview`
            (`startcount`,
             `sellername`,
             `selleremailz`,
             `postedusername`,
             `datez`,
             `timez`,
             `posteduseremail`,
             `commentz`,
             `status`)
VALUES ('$rating_countz',
        '$des_namez',
        '$des_emailzz',
        '$emp_name',
        '$todaydate',
        '$todaytime',
        '$emp_emailz',
        '$review_comment',
        '$statuz');";

$intvalofrat = (int) $rating_countz;


if ($conn->query($sqlloadz) === TRUE ) {

    echo "ok";
} else {
    echo "Error: " . $sqlloadz . "<br>" . $conn->error;
}

$conn->close();