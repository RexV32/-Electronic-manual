<?php
$host = "localhost";
$dbname = "mainDb";
$username = "root";
$password = "";

try {
    $link = new PDO("mysql:host=$host;dbname=$dbname;", $username, $password); 
} catch(PDOException $exception) {
    echo json_encode(["success" => false, "message" => "Не удалось подключиться к базе"]);
    exit;
}