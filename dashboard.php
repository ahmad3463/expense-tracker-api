<?php
require "vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

$secret_key = "MY_SECRET_KEY";

// Get headers
$headers = getallheaders();
if (!isset($headers["Authorization"])) {
    echo json_encode(["status" => false, "message" => "No token provided"]);
    exit;
}

$authHeader = $headers["Authorization"];
$token = str_replace("Bearer ", "", $authHeader);

try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
    echo json_encode([
        "status" => true,
        "message" => "Welcome " . $decoded->data->username,
        "user" => $decoded->data
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => "Invalid token: " . $e->getMessage()]);
}
