<?php
require_once("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

$data = filter_input_array(INPUT_POST, ["id" => FILTER_DEFAULT, "password" => FILTER_DEFAULT, "passwordConfirm" => FILTER_DEFAULT]);
$id = $data["id"];
$password = $data["password"];
$passwordConfirm = $data["passwordConfirm"];

if($password != $passwordConfirm) {
    echo json_encode(["success" => false, "message" => "Пароли не совпадают"]);
}
else {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE `Users` SET Password = :password WHERE Id = :id";
    $stmt = $link -> prepare($sql);
    try {
        $stmt -> execute([
            "password" => $passwordHash,
            "id" => $id
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $exception) {
        echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
    }
}