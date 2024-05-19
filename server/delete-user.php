<?php
require_once ("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

function deleteUser($link, $id) {
    $sql = "DELETE FROM `Users` WHERE Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);
}

try {
    $id = filter_input(INPUT_POST, "id", FILTER_DEFAULT);
    $sql = "SELECT Id FROM `Results` WHERE `Id_User` = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);
    $rowCount = $stmt->rowCount();
    if ($rowCount > 0) {
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($array as $value) {
            $sql = "DELETE FROM `Results` WHERE Id = ?";
            $stmt = $link->prepare($sql);
            $stmt->execute([$value["Id"]]);
        }
        deleteUser($link, $id);
    } else {
        deleteUser($link, $id);
    }
    echo json_encode(["success" => true, "title" => "Успешно", "message" => "Пользователь успешно удален"]);
} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос", "title" => "Ошибка"]);
}