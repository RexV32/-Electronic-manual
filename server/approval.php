<?php
require_once("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input_array(INPUT_POST, ["id" => FILTER_DEFAULT, "action" => FILTER_DEFAULT]);
    $id = $data["id"];
    $action = $data["action"];

    switch ($action) {
        case "accept":
            $sql = "UPDATE `Users` SET Status = 1 WHERE Id = ?";
            $stmt = $link->prepare($sql);
            $success = $stmt->execute([$id]);
            break;
        case "cancel":
            $sql = "DELETE FROM `Users` WHERE Id = ?";
            $stmt = $link->prepare($sql);
            $success = $stmt->execute([$id]);
            break;
        case "allAccept":
            $sql = "UPDATE `Users` SET Status = 1 WHERE Status = 0";
            $stmt = $link->query($sql);
            $success = true;
            break;
        default:
            throw new Exception("Недопустимое значение 'action'");
    }

    if (!$success) {
        throw new Exception("Не удалось выполнить запрос");
    }

    echo json_encode(["success" => true]);
} catch (Exception $exception) {
    echo json_encode(["success" => false, "message" => $exception->getMessage()]);
}
?>
