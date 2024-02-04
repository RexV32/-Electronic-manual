<?php
require_once("../server/function.php");
$title = "ЭМКУ - Добавить группу";

$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, "name", FILTER_DEFAULT);
    $errors["name"] = validateNameGroup($name, $link);
    $errors = array_filter($errors);

    if(!count($errors)) {
        $sql = "INSERT INTO `Groups` (Name) VALUES(?)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([$name]);

        header("Location:group.php");
    }
}

$content = $twig -> render('add-group.twig', 
[
    "title" => $title,
    "errors" => $errors
]);
print($content);