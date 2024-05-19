<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Добавить раздел";
$currentSection = "addSection";
$errors = [];

$disciplines = getDisciplines($link);

$content = $twig -> render('add-section.twig', 
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "disciplines" => $disciplines
    ]);
print($content);