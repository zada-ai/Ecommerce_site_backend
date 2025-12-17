<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include("../db.php");

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// ================== INSERT ORDER ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $product_id = $data['product_id'] ?? '';
    $product_type = $data['product_type'] ?? '';
    $quantity = $data['quantity'] ?? 1;
    $selected_color = $data['selected_color'] ?? null;
    $tid = $data['tid'] ?? '';
    $payment_method = $data['payment_method'] ?? '';
    $phone_number = $data['phone_number'] ?? '';
    $full_name = $data['full_name'] ?? '';
    $street_address = $data['street_address'] ?? '';
    $city = $data['city'] ?? '';
    $zip_code = $data['zip_code'] ?? '';
    $total_price = $data['total_price'] ?? 0;
    $user_email = $data['user_email'] ?? '';

    // Validate required fields
    if (empty($product_id) || empty($product_type) || empty($tid) || empty($payment_method) || empty($full_name) || empty($street_address) || empty($city) || empty($zip_code) || empty($user_email)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    if (!is_numeric($quantity) || $quantity < 1) {
        echo json_encode(["status" => "error", "message" => "Invalid quantity"]);
        exit;
    }

    if (!is_numeric($total_price) || $total_price <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid total price"]);
        exit;
    }

    $sql = "INSERT INTO orders 
    (product_id, product_type, quantity, selected_color, tid, payment_method, phone_number, full_name, street_address, city, zip_code, total_price, status, user_email, country)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, 'Pakistan')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isissssssssds",
        $product_id, $product_type, $quantity, $selected_color, $tid, $payment_method,
        $phone_number, $full_name, $street_address, $city, $zip_code, $total_price, $user_email
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order placed successfully", "order_id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to place order: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// ================== FETCH ORDERS ==================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_email = $_GET['user_email'] ?? '';

    if (empty($user_email)) {
        echo json_encode(["status" => "error", "message" => "User email is required"]);
        exit;
    }

    $sql = "SELECT * FROM orders WHERE user_email=? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["status" => "success", "orders" => $orders]);

    $stmt->close();
    $conn->close();
    exit;
}

// Default fallback
echo json_encode(["status" => "error", "message" => "Invalid request method"]);
?>
