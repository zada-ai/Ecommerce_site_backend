<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include("../db.php");

// product id must be provided
$id = $_GET['id'] ?? '';
// optional type param: expected values: 'candles', 'bracelets', 'bouquet'
$type = $_GET['type'] ?? '';

if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "Product ID required"]);
    exit;
}

function findProduct($conn, $mainTable, $detailTable, $id) {
    $sql = "SELECT m.id, m.title, m.price, d.description, d.images, d.colors
            FROM $mainTable m
            LEFT JOIN $detailTable d ON m.id = d.id
            WHERE m.id = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $product = $result->fetch_assoc();

        // Decode JSON fields for frontend use
        $product['images'] = $product['images'] ? json_decode($product['images']) : [];
        $product['colors'] = $product['colors'] ? json_decode($product['colors']) : [];
        $product['category'] = $mainTable;

        return $product;
    }

    return null;
}

// Map allowed type keys to their DB tables
$typeMap = [
    'candles' => ['main' => 'candles', 'detail' => 'product_candles'],
    'bracelets' => ['main' => 'bracelets', 'detail' => 'product_bracelets'],
    'bouquet' => ['main' => 'bouquet_of_flowers', 'detail' => 'product_bouquet_of_flowers']
];

// If type is provided and valid, only search that table to avoid id collisions across tables
if (!empty($type) && isset($typeMap[$type])) {
    $tbl = $typeMap[$type];
    $product = findProduct($conn, $tbl['main'], $tbl['detail'], $id);
} else {
    // Backwards-compatible fallback: try each table (in order)
    $product =
        findProduct($conn, "candles", "product_candles", $id) ??
        findProduct($conn, "bracelets", "product_bracelets", $id) ??
        findProduct($conn, "bouquet_of_flowers", "product_bouquet_of_flowers", $id);
}

if ($product) {
    echo json_encode([
        "status" => "success",
        "product" => $product
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Product not found"
    ]);
}
?>
