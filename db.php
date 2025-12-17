<?php
$host = getenv('railway');
$port = getenv('3306'); // optional
$db   = getenv('railway');
$user = getenv('root');
$pass = getenv('hbcctYobWkjcESUNLRPQUfQGvTuPSmsC');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
?>
