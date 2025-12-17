<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// For preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
include("../../db.php");
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// 🔹 ADD BOUQUET
if ($action == "create") {

    $title = $_POST['title'] ?? '';
    $img   = $_POST['img'] ?? '';
    $price = $_POST['price'] ?? '';

    $q = "INSERT INTO bouquet_of_flowers (title, img, price) VALUES ('$title', '$img', '$price')";
    if (mysqli_query($conn, $q)) {
        echo json_encode(["success" => "Bouquet added"]);
    } else {
        echo json_encode(["error" => "Insert failed"]);
    }
}

/* 🔹 READ BOUQUETS */
elseif ($action == "read") {

    $q = "SELECT * FROM bouquet_of_flowers ORDER BY id DESC";
    $r = mysqli_query($conn, $q);

    $data = [];
    while ($row = mysqli_fetch_assoc($r)) {
        $data[] = $row;
    }
    echo json_encode($data);
}

/* 🔹 UPDATE BOUQUET */
elseif ($action == "update") {

    $id    = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $img   = $_POST['img'] ?? '';
    $price = $_POST['price'] ?? '';

    $q = "UPDATE bouquet_of_flowers SET title='$title', img='$img', price='$price' WHERE id=$id";
    if (mysqli_query($conn, $q)) {
        echo json_encode(["success" => "Bouquet updated"]);
    } else {
        echo json_encode(["error" => "Update failed"]);
    }
}

/* 🔹 DELETE BOUQUET */
elseif ($action == "delete") {

    $id = $_POST['id'] ?? '';

    $q = "DELETE FROM bouquet_of_flowers WHERE id=$id";
    if (mysqli_query($conn, $q)) {
        echo json_encode(["success" => "Bouquet deleted"]);
    } else {
        echo json_encode(["error" => "Delete failed"]);
    }
}

else {
    echo json_encode(["error" => "Invalid action"]);
}

?>