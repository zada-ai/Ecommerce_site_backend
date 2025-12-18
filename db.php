<?php
$host = "interchange.proxy.rlwy.net";
$port = 23362;
$dbname = "railway";
$username = "root";
$password = "LhiAPIWhlODJFVwCQDOiJajicCascqQH";

// Create mysqli connection without emitting any output on failure.
// The API scripts will check `$conn` and return JSON errors.
$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_errno) {
    // mark as null so callers can detect failure
    $conn = null;
}
?>
