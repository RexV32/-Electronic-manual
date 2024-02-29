<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭМКУ - Добавить тест";
$errors = [];
$currentSection = "addTests";

$disciplines = getDisciplines($link);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = filter_input_array(INPUT_POST, ["name" => FILTER_DEFAULT, "discipline" => FILTER_DEFAULT]);
    $name = trim($data["name"]);
    $id = $data["discipline"];
    $disciplinesIdArray = array_column($disciplines, "Id");
    $errors["discipline"] = validateIdDiscipline($id, $disciplinesIdArray);
    $errors["name"] = validateNameTests($name, $link);
    $errors = array_filter($errors);

    if(!count($errors)) {
        $sql = "INSERT INTO `Tests` (Name,Id_disciplines) VALUES(:name, :id)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([
            "name" => $name,
            "id" => $id
        ]);
        $lastInsertId = $link -> lastInsertId();
        $nameFolder = $lastInsertId;
        $path = "../uploads/quiz/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        header("Location:test-list.php");
    }
}

$content = $twig -> render('add-tests.twig', 
[
    "title" => $title,
    "errors" => $errors,
    "currentSection" => $currentSection,
    "disciplines" => $disciplines
]);
print($content);