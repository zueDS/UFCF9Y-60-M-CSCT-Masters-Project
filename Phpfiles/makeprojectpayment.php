<?php

include './DB.php';
session_start();


$buyer_emailz = $_SESSION["emailz"];
$buyer_nameinfull = $_SESSION["nameinfull"];
$date = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
$todaydate = $date->format('Y-m-d');
$todaytime = $date->format('H:i:s');



$row_sellername = $_POST["designername"];
$row_selleremail = $_POST["designeremail"];
$row_proid = $_POST["proidz"];
$row_protitle = $_POST["protitlez"];
$row_procost = $_POST["paymentz"];
$row_idz = $_POST["proidzrow"];
$row_prostat = "Payed";

$ad2 = 15;


$sql = "update newprojectz set statusz='$row_prostat' where id='$row_idz'";

$asdad = "update sellerratingz set ratingz=ratingz+'$ad2' where selleremailz='$row_sellername'";


$sqlqq = "INSERT INTO `projectpayment`
            (`empname`,
             `empemailz`,
             `designername`,
             `designeremailz`,
             `proid`,
             `protitle`,
             `paymentz`,
             `paydatez`,
             `paytimr`)
VALUES (
        '$buyer_nameinfull',
        '$buyer_emailz',
        '$row_selleremail',
        '$row_sellername',
        '$row_proid',
        '$row_protitle',
        '$row_procost',
        '$todaydate',
        '$todaytime');";



if ($conn->query($sqlqq) === TRUE && $conn->query($sql) === TRUE && $conn->query($asdad) === TRUE) {

    echo "ok";
} else {
    echo "Error: " . $sqlqq . "<br>" . $conn->error;
}