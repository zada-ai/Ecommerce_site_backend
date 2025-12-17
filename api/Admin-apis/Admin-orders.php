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

/* 🔹 READ ORDERS */
if ($action == "read") {

    $q = "SELECT * FROM orders ORDER BY created_at DESC";
    $r = mysqli_query($conn, $q);

    $data = [];
    while ($row = mysqli_fetch_assoc($r)) {
        $data[] = $row;
    }
    echo json_encode($data);
}

/* 🔹 UPDATE ORDER STATUS */
elseif ($action == "update_status") {

    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    // Validate status
    $valid_statuses = ['pending', 'successful', 'processing', 'failed'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(["error" => "Invalid status"]);
        exit;
    }

    $q = "UPDATE orders SET status='$status' WHERE id=$id";
    if (mysqli_query($conn, $q)) {
        echo json_encode(["success" => "Order status updated"]);
    } else {
        echo json_encode(["error" => "Update failed"]);
    }
}

else {
    echo json_encode(["error" => "Invalid action"]);
}

?>