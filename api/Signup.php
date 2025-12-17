<?php
header('Content-Type: application/json');
// Allow any origin for local development (DEV ONLY - replace with specific origin in production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

include("../db.php");

// Read and decode JSON from request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// If JSON decode fails or returns null, log it for debugging
if ($data === null && !empty($input)) {
    error_log("JSON parse error: " . json_last_error_msg() . " Input: " . substr($input, 0, 100));
}

// Extract name, email, password from parsed JSON (do NOT fall back to $_POST for API)
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert into users table (not login) - matches our setup.sql schema
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: ".$conn->error]);
    exit;
}

$stmt->bind_param("sss", $name, $email, $hashed);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User registered successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
?>