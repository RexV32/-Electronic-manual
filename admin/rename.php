<?php
require_once("../server/function.php");
$db = $link;
$title = "ЭМКУ - Изменить наименование";
$errors = [];
$nameDiscipline = "";
$refer = $_GET["section"];
$id = $_GET["id"];

switch($refer) {
    case $refer == "discipline":
        $sql = "SELECT Name FROM `Disciplines` WHERE Id = ?";
        break;
    case $refer == "section":
        $sql = "SELECT Name FROM `Sections` WHERE Id = ?";
        break;
}
$stmt = $db -> prepare($sql);
$stmt -> execute([$id]);
$nameDiscipline = $stmt -> fetch(PDO::FETCH_ASSOC)["Name"];


if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(filter_input(INPUT_POST, "name", FILTER_DEFAULT));
    $errors["name"] = validateName($name, $db, $refer);
    $errors = array_filter($errors);

    if(!count($errors)) {
       switch($refer) {
        case $refer == "discipline":
            $sql = "UPDATE `Disciplines` SET Name = :name WHERE id = :id";
            $url = "disciplines.php";
            break;
        case $refer == "section":
            $sql = "UPDATE `Sections` SET Name = :name WHERE id = :id";
            $url = "sections.php";
            break;
       }
       $stmt = $db -> prepare($sql);
       $stmt -> execute([
        "name" => $name,
        "id" => $id
       ]);

       header("Location:$url");
    }
}

$content = $twig -> render('rename.twig', ["errors" => $errors, "title" => $title, "nameDiscipline" => $nameDiscipline]);
print($content);