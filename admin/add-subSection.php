<?php
require_once("../server/function.php");
$title = "ЭМКУ - Добавить подраздел";
$currentSection = "subSectionAdd";

$currentIdDiscipline = 0;

$disciplines = getDisciplines($link);

$content = $twig -> render('subSection-add.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "disciplines" => $disciplines
]);

print($content);