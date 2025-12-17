<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
include("../db.php");

// Fetch bouquets table
$bouquets = [];

if ($conn) {
    $sql = "SELECT id, title, img, price FROM bouquet_of_flowers";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bouquets[] = $row;
        }
    } else if (!$result) {
        // Query failed - return error
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
        exit;
    }
    $conn->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

echo json_encode($bouquets);
exit;
?>