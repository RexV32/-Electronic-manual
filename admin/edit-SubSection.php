<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭМКУ - Редактировать подраздел";
$currentSection = "subSectionEdit";

$id = isset($_GET["id"]) ? $_GET["id"] : 0;

$sql = "SELECT Name FROM `SubSections` WHERE Id = ?";
$stmt = $link -> prepare($sql);
$stmt -> execute([$id]);
$name = $stmt -> fetchColumn();

$content = $twig -> render('edit-SubSection.twig', [
    "title" => $title,
    "currentSection" => $currentSection,
    "name" => $name
]);
print($content);