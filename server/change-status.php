<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input(INPUT_POST, "data", FILTER_DEFAULT);
    $decodedData = json_decode($data, true);
    $id = $decodedData["id"];
    $status = $decodedData["status"];
    $section = $decodedData["section"];
    $newStatus = ($status == 1) ? 0 : 1;

    switch ($section) {
        case "discipline":
            $sql = "UPDATE Disciplines SET Status = :newStatus WHERE Id = :id";
            $stmt = $link->prepare($sql);
            $stmt->execute([
                "newStatus" => $newStatus,
                "id" => $id,
            ]);
            break;
        case "section":
            $sql = "UPDATE `Sections` SET Status = :newStatus WHERE Id = :id";
            $stmt = $link->prepare($sql);
            $stmt->execute([
                "newStatus" => $newStatus,
                "id" => $id,
            ]);
            break;
        case "subSection":
            $sql = "UPDATE `SubSections` SET Status = :newStatus WHERE Id = :id";
            $stmt = $link->prepare($sql);
            $stmt->execute([
                "newStatus" => $newStatus,
                "id" => $id,
            ]);
            break;
        case "test":
            $sql = "UPDATE Tests SET Status = :newStatus WHERE Id = :id";
            $stmt = $link->prepare($sql);
            $stmt->execute([
                "newStatus" => $newStatus,
                "id" => $id,
            ]);
            break;
        default:
            echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос", "title" => "Ошибка"]);
    }
    echo json_encode(["success" => true]);
} catch (Exception $ex) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос", "title" => "Ошибка"]);
}