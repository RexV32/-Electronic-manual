<?php
require_once("../server/function.php");
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