<?php
include "config/db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username']);
$email = trim($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute([":email" => $email]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => false, "message" => "Email already registered"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:u, :e, :p)");
$stmt->execute([":u" => $username, ":e" => $email, ":p" => $password]);

echo json_encode(["status" => true, "message" => "Signup successful"]);
