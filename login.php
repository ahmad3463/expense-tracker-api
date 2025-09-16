<?php
require "config/db.php";
require "vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

$secret_key = "MY_SECRET_KEY";

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email']);
$password = trim($data['password']);

$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([":email" => $email]);

if ($stmt->rowCount() == 0) {
    echo json_encode(["status" => false, "message" => "Invalid email"]);
    exit;
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => false, "message" => "Invalid password"]);
    exit;
}

$payload = [
    "iss" => "http://localhost",  // issuer
    "aud" => "http://localhost",  // audience
    "iat" => time(),              // issued at
    "exp" => time() + (60*60),    // expires in 1 hour
    "data" => [
        "id" => $user['id'],
        "username" => $user['username'],
        "email" => $user['email']
    ]
];

$jwt = JWT::encode($payload, $secret_key, 'HS256');

echo json_encode([
    "status" => true,
    "message" => "Login successful",
    "token" => $jwt,
    "user" => [
        "id" => $user['id'],
        "username" => $user['username']
    ]
]);
