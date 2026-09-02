<?php

include './DB.php';

$name_in_fullz = $_POST["name"];
$emailz = $_POST["email"];
$contcat_number = $_POST["contactz"];
$address_n = $_POST["addresss"];
$password_user =  hash('md5', $_POST["passwordz1"]);
$type_user = "Employer";

$statz = "Active";
$user_level = 10;

$sqlz = "SELECT * FROM user_details where email='$emailz'";
$result = $conn->query($sqlz);

$echdata = "";

if ($result->num_rows > 0) {
    echo "Already";
} else {

    $rating_data = 10;

    $sqldata1 = "insert into `buyerratingz`
            (`buyeridz`,
             `ratingpoints`)
values ('$emailz',
        '$rating_data');";
    if ($conn->query($sqldata1) === TRUE) {

        $echdata = "ok";
    } else {
        $echdata = "Error: " . $sqldata1 . "<br>" . $conn->error;
    }


    $sql = "INSERT INTO `user_details`
            (`nameinfull`,
             `email`,
             `passwordz`,
             `contactnumber`,
             `addressz`,
             `usertype`,
             `userstatus`,
             `userlevels`)
VALUES ('$name_in_fullz',
        '$emailz',
        '$password_user',
        '$contcat_number',
        '$address_n',
        '$type_user',
        '$statz',
        '$user_level');";

    if ($conn->query($sql) === TRUE) {
        $echdata = "ok";
        echo $echdata;
    } else {
        echo $echdata;
    }

    $conn->close();
}