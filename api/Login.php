<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../php_runtime_errors.log');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

include("../db.php");

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data === null && !empty($input)) {
    error_log("JSON parse error: " . json_last_error_msg());
}

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and Password required"]);
    exit;
}

// 🟦 SUPER ADMIN LOGIN (fixed account)
if ($email === "ecommercesite@viral.uv" && $password === "password") {
    // Set cookie for admin
    setcookie(
        "user_token",          // Cookie name
        "admin_super_token",   // Cookie value (aap yahan random string ya JWT bhi use kar sakte ho)
        time() + 86400,        // Expiry: 1 day (seconds)
        "/",                   // Path (site-wide)
        "",                    // Domain (empty = current domain)
        false,                 // Secure (true if HTTPS)
        true                   // HttpOnly (cannot be accessed via JS)
    );

    echo json_encode([
        "status" => "success",
        "message" => "Admin login successful",
        "role" => "admin",
        "redirect" => "/Admin/dashboard/page"
    ]);
    exit;
}

// 🟩 Normal user login
$sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "DB Error"]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        // Set cookie for normal user
        setcookie(
            "user_token",
            $user['id'] . "_" . bin2hex(random_bytes(16)), // unique value
            time() + 86400,        // 1 day
            "/",
            "",
            false,
            true
        );

        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "role" => "user",
            "username" => $user['name'],
            "email" => $user['email'],
            "redirect" => "/"
        ]);

    } else {
        echo json_encode(["status" => "error", "message" => "Wrong password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
?>
