<?php

include './DB.php';
session_start();

$upload_path = 'gigthumimg/';

$des_id = $_SESSION["user_id"] ?? '';
$des_name = $_SESSION["nameinfull"] ?? '';
$des_emailz = $_SESSION["emailz"] ?? '';

$date = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
$todaydate = $date->format('Y-m-d');
$todaytime = $date->format('H:i:s');

$titlez = trim($_POST["p1"] ?? '');
$pricez = trim($_POST["p2"] ?? '');
$tagz = trim($_POST["p3"] ?? '');
$desz = trim($_POST["p4"] ?? '');
$cato = trim($_POST["p5"] ?? 'None');
$stt = "posted";

if ($cato === "None" || $titlez === '' || $pricez === '' || $tagz === '' || $desz === '') {
    echo 'empty';
    exit;
}

if (!isset($_FILES['thumbimage']) || $_FILES['thumbimage']['error'] === UPLOAD_ERR_NO_FILE) {
    echo 'imageone';
    exit;
}

try {
    if ($_FILES['thumbimage']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed with error code: ' . $_FILES['thumbimage']['error']);
    }

    $file_name = basename($_FILES['thumbimage']['name']);
    $file_tmp = $_FILES['thumbimage']['tmp_name'];

    // Sanitize filename and make it unique.
    $safe_email = preg_replace('/[^A-Za-z0-9._-]/', '_', $des_emailz);
    $safe_file_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $file_name);
    $filenewz = $safe_email . '_' . time() . '_' . $safe_file_name;

    $target_dir = __DIR__ . '/gigthumimg/';
    if (!is_dir($target_dir) && !mkdir($target_dir, 0775, true)) {
        throw new RuntimeException('Could not create image upload folder.');
    }

    $target_path = $target_dir . $filenewz;

    if (!move_uploaded_file($file_tmp, $target_path)) {
        throw new RuntimeException('Failed to move uploaded image.');
    }

    // Store the relative path used by your website.
    $fileekepatheka = 'gigthumimg/' . $filenewz;

    // Keep the existing GIGxxxxx style ID.
    $orderidgen = 'GIG' . random_int(10000, 99999);

    $sql = "INSERT INTO designerpostitem
    (
        gigtitle,
        gigprice,
        gigtags,
        gigdesc,
        catoz,
        imgurl,
        datez,
        timez,
        designerid,
        designernamez,
        designeremailz,
        statusz,
        ranidz
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException('Prepare failed: ' . $conn->error);
    }

    // Use strings for compatibility with the existing database schema.
    $stmt->bind_param(
        'sssssssssssss',
        $titlez,
        $pricez,
        $tagz,
        $desz,
        $cato,
        $fileekepatheka,
        $todaydate,
        $todaytime,
        $des_id,
        $des_name,
        $des_emailz,
        $stt,
        $orderidgen
    );

    if (!$stmt->execute()) {
        throw new RuntimeException('Database insert failed: ' . $stmt->error);
    }

    $stmt->close();
    echo 'ok';

} catch (Throwable $exc) {
    http_response_code(500);
    echo 'ERROR: ' . $exc->getMessage();
}