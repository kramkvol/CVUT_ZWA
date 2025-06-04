<?php
if (!isset($_SESSION)) {
    session_start();
}

$servername = "localhost";
$username = "kramkvol";
$password = "webove aplikace";
$database = "kramkvol";

try {
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection error. Please try again later.");
}
?>