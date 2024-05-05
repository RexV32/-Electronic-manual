<?php
require_once("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

if (!isset($_GET["id"]) || !isset($_GET["section"])) {
    header("Location: admin.php");
}

$title = "ЭКУМО - Изменить наименование";
$errors = [];
$nameDiscipline = "";
$refer = $_GET["section"];
$id = $_GET["id"];

switch ($refer) {
    case $refer == "discipline":
        $sql = "SELECT Name FROM `Disciplines` WHERE Id = ?";
        break;
    case $refer == "section":
        $sql = "SELECT Name FROM `Sections` WHERE Id = ?";
        break;
    case $refer == "test":
        $sql = "SELECT Name FROM `Tests` WHERE Id = ?";
        break;
}
$stmt = $link->prepare($sql);
$stmt->execute([$id]);
$nameDiscipline = $stmt->fetch(PDO::FETCH_ASSOC)["Name"];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(filter_input(INPUT_POST, "name", FILTER_DEFAULT));
    $errors["name"] = validateName($name, $link, $refer);
    $errors = array_filter($errors);

    if (!count($errors)) {
        switch ($refer) {
            case $refer == "discipline":
                $sql = "UPDATE `Disciplines` SET Name = :name WHERE id = :id";
                $url = "disciplines.php";
                break;
            case $refer == "section":
                $sql = "UPDATE `Sections` SET Name = :name WHERE id = :id";
                $url = "sections.php";
                break;
            case $refer == "test":
                $sql = "UPDATE `Tests` SET Name = :name WHERE id = :id";
                $url = "test-list.php";
                break;
        }
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "name" => $name,
            "id" => $id
        ]);

        header("Location:$url");
    }
}

$content = $twig->render('rename.twig', ["errors" => $errors, "title" => $title, "nameDiscipline" => $nameDiscipline]);
print($content);