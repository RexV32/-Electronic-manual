<?php
require_once ("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input(INPUT_POST, "id", FILTER_DEFAULT);
    $id = $data["id"];

    $sql = "DELETE FROM `Groups` WHERE Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode(["success" => true, "message" => "Группа успешно удалена"]);
} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
}