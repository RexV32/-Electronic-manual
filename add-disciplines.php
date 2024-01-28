<?php
require_once("server/function.php");
$title = "ЭМКУ - Добавить дисциплину";
$errors = [];
$db = $link;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, "name", FILTER_DEFAULT);
    $errors["name"] = validateNameDiscipline($name, $db);
    $errors = array_filter($errors);

    if(!count($errors)) {
        $sql = "INSERT INTO `Disciplines`(Name) VALUES(?)";
        $stmt = $db -> prepare($sql);
        $stmt -> execute([$name]);
        $lastInsertId = $db -> lastInsertId();
        $nameFolder = $lastInsertId;
        $path = "uploads/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        header("Location:disciplines.php");
    }
}

$content = $twig -> render('add-disciplines.twig', ["title" => $title, "errors" => $errors]);
print($content);