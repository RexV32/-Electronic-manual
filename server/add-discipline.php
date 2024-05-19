<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

$errors = [];

try {
    $name = trim(filter_input(INPUT_POST, "name", FILTER_DEFAULT));
    $errors["name"] = validateNameDiscipline($name, $link);
    $errors = array_filter($errors);

    if (!count($errors)) {
        $sql = "INSERT INTO `Disciplines`(Name) VALUES(?)";
        $stmt = $link->prepare($sql);
        $stmt->execute([$name]);
        $lastInsertId = $link->lastInsertId();
        $nameFolder = $lastInsertId;
        $path = "../uploads/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $errors["name"]]);
    }

} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}