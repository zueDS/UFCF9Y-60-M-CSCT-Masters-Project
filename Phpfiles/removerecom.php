<?php


include './DB.php';




$row_id = $_POST["dd"];

$sql = "delete from chatbot  where id='$row_id'";
if ($conn->query($sql) === TRUE) {

    echo "ok";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
