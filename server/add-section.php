<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

$errors = [];

try {
    $disciplines = getDisciplines($link);
    $data = filter_input_array(INPUT_POST, ["name" => FILTER_DEFAULT, "discipline" => FILTER_DEFAULT]);
    $id = $data["discipline"];
    $name = trim($data["name"]);
    $disciplinesIdArray = array_column($disciplines, "Id");
    $errors["discipline"] = validateIdDiscipline($id, $disciplinesIdArray);
    $errors["name"] = validateNameSection($name);
    $errors = array_filter($errors);

    if (!count($errors)) {
        $sql = "INSERT INTO `Sections`(Name,Id_discipline) VALUES(:name,:id)";
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "name" => $name,
            "id" => $id
        ]);
        $nameFolder = $link->lastInsertId();
        $path = "../uploads/$id/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        echo json_encode(["success" => true]);
    } else {
        if(!empty($errors["name"])) {
            echo json_encode(["success" => false, "message" => $errors["name"]]);   
        }
        else {
            echo json_encode(["success" => false, "message" => $errors["discipline"]]);
        }
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}