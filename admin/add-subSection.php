<?php
require_once("../server/function.php");
$title = "ЭМКУ - Добавить подраздел";
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