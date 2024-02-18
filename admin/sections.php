<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭМКУ - Список разделов";
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
        "disciplines" => $disciplines,
        "sections" => $sections,
        "sectionsSlice" => $sectionsSlice,
        "pages" => $pages,
        "currentIdDiscipline" => $currentIdDiscipline,
        "limit" => $limit,
        "page" => $page
    ]);
print($content);