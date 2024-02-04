<?php
require_once("../server/function.php");
$title = "ЭМКУ - Список разделов";
$currentSection = "Section";
$currentDiscipline = "";
$sections = [];

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$disciplines = getDisciplines($link);
$disciplinesQuantity = count($disciplines);

if($disciplinesQuantity > 0) {
    $currentIdDiscipline = $disciplines[0]["Id"];
    $currentDiscipline = $disciplines[0]["Name"];
    $sql = "SELECT * FROM `Sections` WHERE `Id_discipline` = ?";
    $stmt = $link ->prepare($sql);
    $stmt -> execute([$currentIdDiscipline]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sectionsSlice = array_slice($sections,$offset,$limit,true);
    $sectionQuantity = count($sections);
    $pages = ceil($sectionQuantity / $limit);
}

if(isset($_GET["discipline"])) {
    $id = $_GET["discipline"];
    $sql = "SELECT Sections.Id,Sections.Name, Sections.Status,Disciplines.Name as NameDiscipline FROM `Sections` INNER JOIN 
    Disciplines ON Sections.Id_discipline = Disciplines.Id WHERE Sections.Id_discipline = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$id]);
    $sections = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    $sectionsSlice = array_slice($sections,$offset,$limit,true);
    $sectionQuantity = count($sections);
    $pages = ceil($sectionQuantity / $limit);
    $currentIdDiscipline = $id;
    if(count($sections) > 0) {
        $currentDiscipline = $sections[0]["NameDiscipline"];
    }
    else {
        $sql = "SELECT Name FROM `Disciplines` WHERE id = ? LIMIT 1";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([$id]);
        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        $currentDiscipline = $result["Name"];
    }
}

if(isset($_GET["id"], $_GET["status"])) {
    $newStatus = ($_GET["status"] == 1) ? 0 : 1;
    $sql = "UPDATE `Sections` SET Status = :newStatus WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "newStatus" => $newStatus,
        "id" => $_GET["id"],
    ]);
    $currentUrl = isset($_SERVER["HTTP_REFERER"])?explode("/",$_SERVER["HTTP_REFERER"])[5] : "sections.php";
    header("Location:$currentUrl");
}

$content = $twig -> render('section-list.twig', 
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "currentDiscipline" => $currentDiscipline,
        "disciplines" => $disciplines,
        "sections" => $sections,
        "sectionsSlice" => $sectionsSlice,
        "pages" => $pages,
        "currentIdDiscipline" => $currentIdDiscipline,
        "limit" => $limit,
        "page" => $page
    ]);
print($content);