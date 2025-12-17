<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "Ecommerce_site");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$query = $conn->real_escape_string($query);

$results = [];

if (empty($query)) {
    // Return some test data if no query
    $results = [
        ['id' => 1, 'title' => 'Test Candle', 'price' => 10.99, 'img' => 'test.jpg', 'type' => 'candle'],
        ['id' => 2, 'title' => 'Test Bracelet', 'price' => 20.50, 'img' => 'test.jpg', 'type' => 'bracelet'],
    ];
} else {

// Search Candles
$candleSql = "SELECT id, title, price, img, 'candle' as type FROM candles WHERE title LIKE '%$query%'";
$candleRes = $conn->query($candleSql);
if ($candleRes->num_rows > 0) {
    while($row = $candleRes->fetch_assoc()) {
        $results[] = $row;
    }
}

// Search Bracelets
$braceletSql = "SELECT id, title, price, img, 'bracelet' as type FROM bracelets WHERE title LIKE '%$query%'";
$braceletRes = $conn->query($braceletSql);
if ($braceletRes->num_rows > 0) {
    while($row = $braceletRes->fetch_assoc()) {
        $results[] = $row;
    }
}

// Search Bouquets
$bouquetSql = "SELECT id, title, price, img, 'bouquet' as type FROM bouquet_of_flowers WHERE title LIKE '%$query%'";
$bouquetRes = $conn->query($bouquetSql);
if ($bouquetRes->num_rows > 0) {
    while($row = $bouquetRes->fetch_assoc()) {
        $results[] = $row;
    }
}

if (count($results) > 0) {
    echo json_encode(["status" => "success", "products" => $results]);
} else {
    echo json_encode(["status" => "success", "products" => []]);
}
?>
