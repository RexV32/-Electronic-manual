<?php
require_once("../server/connect.php");

$id = filter_input(INPUT_POST, 'id', FILTER_DEFAULT);

$sql = "SELECT Content, Name FROM `SubSections` WHERE Id = ?";
$stmt = $link->prepare($sql);

try {
    $stmt->execute([$id]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($content);
} catch (PDOException $exception) {
    echo json_encode(["error" => "$exception"]);
}