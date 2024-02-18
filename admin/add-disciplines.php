<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭМКУ - Добавить дисциплину";
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(filter_input(INPUT_POST, "name", FILTER_DEFAULT));
    $errors["name"] = validateNameDiscipline($name, $link);
    $errors = array_filter($errors);

    if(!count($errors)) {
        $sql = "INSERT INTO `Disciplines`(Name) VALUES(?)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([$name]);
        $lastInsertId = $link -> lastInsertId();
        $nameFolder = $lastInsertId;
        $path = "../uploads/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        header("Location:disciplines.php");
    }
}

$content = $twig -> render('add-disciplines.twig', ["title" => $title, "errors" => $errors]);
print($content);