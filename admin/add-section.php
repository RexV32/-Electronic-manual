<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Добавить раздел";
$currentSection = "addSection";
$errors = [];

$disciplines = getDisciplines($link);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = filter_input_array(INPUT_POST, ["name" => FILTER_DEFAULT, "discipline" => FILTER_DEFAULT]);
    $id = $data["discipline"];
    $name = trim($data["name"]);
    $disciplinesIdArray = array_column($disciplines, "Id");
    $errors["discipline"] = validateIdDiscipline($id, $disciplinesIdArray);
    $errors["name"] = validateNameSection($name);
    $errors = array_filter($errors);

    if(!count($errors)) {
        $sql = "INSERT INTO `Sections`(Name,Id_discipline) VALUES(:name,:id)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([
            "name" => $name,
            "id" => $id
        ]);
        $nameFolder = $link -> lastInsertId();
        $path = "../uploads/$id/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        header("Location:sections.php");
    }
}

$content = $twig -> render('add-section.twig', 
    [
        "title" => $title, 
        "errors" => $errors, 
        "currentSection" => $currentSection,
        "disciplines" => $disciplines
    ]);
print($content);