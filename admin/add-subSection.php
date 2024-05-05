<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Добавить подраздел";
$currentSection = "addSubSection";

$currentIdDiscipline = 0;

$disciplines = getDisciplines($link);

$content = $twig -> render('add-subSection.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "disciplines" => $disciplines
]);

print($content);