<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// For preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$email = $data['email'] ?? "";
$password = $data['password'] ?? "";

// STATIC ADMIN LOGIN (You can change these)
$ADMIN_EMAIL = "rizaglowgarden@candle.dream";
$ADMIN_PASSWORD = "admin123";

// Validate
if ($email === "" || $password === "") {
    echo json_encode(["status" => "error", "message" => "Email and password required"]);
    exit;
}

// If matches admin credentials
if ($email === $ADMIN_EMAIL && $password === $ADMIN_PASSWORD) {
    echo json_encode([
        "status" => "success",
        "message" => "Admin login successful",
        "token" => "ADMIN_SECURE_TOKEN_12345"
    ]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid admin credentials"]);
exit;
?>
