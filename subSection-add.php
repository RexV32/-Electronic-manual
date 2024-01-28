<?php
require_once("server/function.php");
$title = "ЭМКУ - Добавить подраздел";
$currentSection = "subSectionAdd";

$currentIdDiscipline = 0;

function getDisciplines($link) {
    $sql = "SELECT Id, Name FROM `Disciplines`";
    return $link->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

$disciplines = getDisciplines($link);

$content = $twig -> render('subSection-add.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "disciplines" => $disciplines
]);
print($content);