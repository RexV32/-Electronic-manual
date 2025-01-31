<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Список разделов";
$currentSection = "Section";
$sections = [];
$sectionsSlice = [];
$pages = 0;
$currentIdDiscipline = 0;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$disciplines = getDisciplines($link);
$disciplinesQuantity = count($disciplines);

if($disciplinesQuantity > 0) {
    $currentIdDiscipline = $disciplines[0]["Id"];
    $sql = "SELECT * FROM `Sections` WHERE `Id_discipline` = ?";
    $stmt = $link ->prepare($sql);
    $stmt -> execute([$currentIdDiscipline]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sectionsSlice = array_slice($sections,$offset,$limit,true);
    $sectionQuantity = count($sections);
    $pages = ceil($sectionQuantity / $limit);
}

if(isset($_GET["discipline"])) {
    $currentIdDiscipline = $_GET["discipline"];
    $sql = "SELECT * FROM `Sections` WHERE `Id_discipline` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$currentIdDiscipline]);
    $sections = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    $sectionsSlice = array_slice($sections,$offset,$limit,true);
    $sectionQuantity = count($sections);
    $pages = ceil($sectionQuantity / $limit);
}

$content = $twig -> render('section-list.twig', 
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "disciplines" => $disciplines,
        "sections" => $sections,
        "sectionsSlice" => $sectionsSlice,
        "pages" => $pages,
        "currentIdDiscipline" => $currentIdDiscipline,
        "limit" => $limit,
        "page" => $page
    ]);
print($content);