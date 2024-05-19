<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

$errors = [];
$idDiscipline = "";

try {
    $data = filter_input_array(INPUT_POST, ["name" => FILTER_DEFAULT, "id" => FILTER_DEFAULT, "refer" => FILTER_DEFAULT]);
    $name = trim($data["name"]);
    $id = $data["id"];
    $refer = $data["refer"];
    if($refer == "test") {
        $sql = "SELECT `Tests`.`Id_disciplines` FROM `Tests` WHERE Id = ?";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([$id]);
        $idDiscipline = $stmt -> fetchColumn();
    }
    $errors["name"] = validateName($name, $link, $refer, $idDiscipline);
    $errors = array_filter($errors);

    if (!count($errors)) {
        switch ($refer) {
            case "discipline":
                $sql = "UPDATE `Disciplines` SET Name = :name WHERE id = :id";
                break;
            case "section":
                $sql = "UPDATE `Sections` SET Name = :name WHERE id = :id";
                break;
            case "test":
                $sql = "UPDATE `Tests` SET Name = :name WHERE id = :id";
                break;
        }
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "name" => $name,
            "id" => $id
        ]);

        echo json_encode(["success" => true, "url" => $refer]);
    } else {
        echo json_encode(["success" => false, "message" => $errors["name"]]);
    }
} catch(Exception $error) {
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}