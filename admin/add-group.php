<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


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