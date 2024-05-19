<?php
require_once ("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

if (!isset($_GET["id"]) || !isset($_GET["section"])) {
    header("Location: admin.php");
}

$title = "ЭКУМО - Изменить наименование";
$currentSection = "rename";
$errors = [];
$nameDiscipline = "";
$refer = $_GET["section"];
$id = $_GET["id"];
switch ($refer) {
    case "discipline":
        $sql = "SELECT Name FROM `Disciplines` WHERE Id = ?";
        break;
    case "section":
        $sql = "SELECT Name FROM `Sections` WHERE Id = ?";
        break;
    case "test":
        $sql = "SELECT Name FROM `Tests` WHERE Id = ?";
        break;
}
$stmt = $link->prepare($sql);
$stmt->execute([$id]);
$nameDiscipline = $stmt->fetchColumn();

$content = $twig->render(
    'rename.twig',
    [
        "title" => $title,
        "nameDiscipline" => $nameDiscipline,
        "currentSection" => $currentSection
    ]
);
print ($content);