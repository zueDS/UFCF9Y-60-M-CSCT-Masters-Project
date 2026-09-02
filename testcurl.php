<?php

$ch = curl_init("https://api.openai.com");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$result = curl_exec($ch);

if ($result === false) {
    echo "CURL ERROR: " . curl_error($ch);
} else {
    echo "SSL CONNECTION WORKING";
}

curl_close($ch);