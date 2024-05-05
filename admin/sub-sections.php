<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Список подразделов";
$currentSection = "subSection";

$subSectionsSlice = [];
$sections = [];
$subSections = [];
$pages = "";
$currentIdDiscipline = 0;
$currentIdSection = 0;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

function getSection($link, $id) {
    $sql = "SELECT * FROM `Sections` WHERE `Id_discipline` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSubSection($link, $idDiscipline, $idSection) {
    $sql = "SELECT Disciplines.Name as NameDiscipline,Sections.Name as NameSection,SubSections.Name as NameSubSection,SubSections.Id,SubSections.Status FROM `SubSections` INNER JOIN Sections ON SubSections.Id_section = Sections.Id INNER JOIN Disciplines ON Sections.Id_discipline = Disciplines.Id WHERE Disciplines.Id = :idDiscipline AND Sections.Id = :idSection";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([
        "idDiscipline" => $idDiscipline,
        "idSection" => $idSection
    ]);
    return $stmt -> fetchAll(PDO::FETCH_ASSOC);
}

function getCurrentNames($link, $idDiscipline, $idSection) {
    $sql = "SELECT `Disciplines`.`Name` as NameDiscipline, Sections.Name as NameSection FROM `Disciplines` INNER JOIN Sections ON Disciplines.Id = Sections.Id_discipline WHERE Disciplines.Id = :idDiscipline AND Sections.id = :idSection LIMIT 1";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([
        "idDiscipline" => $idDiscipline,
        "idSection" => $idSection
    ]);
    return $stmt -> fetch(PDO::FETCH_ASSOC);
}

$disciplines = getDisciplines($link);
$disciplinesQuantity = count($disciplines);

if($disciplinesQuantity > 0) {
    $currentIdDiscipline = $disciplines[0]["Id"];
    $sections = getSection($link, $currentIdDiscipline);
    if(count($sections) > 0) {
        $currentIdSection = $sections[0]["Id"];
        $subSections = getSubSection($link, $currentIdDiscipline, $currentIdSection);
        $subSectionsSlice = array_slice($subSections,$offset,$limit,true);
        $subSectionsQuantity = count($subSections);
        $pages = ceil($subSectionsQuantity / $limit);
    }
}

if(isset($_GET["discipline"], $_GET["section"])) {
    $idDiscipline = $_GET["discipline"];
    $idSection = $_GET["section"];
    $sections = getSection($link, $idDiscipline);
    $subSections = getSubSection($link, $idDiscipline, $idSection);
    $subSectionsSlice = array_slice($subSections,$offset,$limit,true);
    $subSectionsQuantity = count($subSections);
    $pages = ceil($subSectionsQuantity / $limit);
    $currentIdDiscipline = $idDiscipline;
    $currentIdSection = $idSection;
}

if(isset($_GET["id"], $_GET["status"])) {
    $newStatus = ($_GET["status"] == 1) ? 0 : 1;
    $sql = "UPDATE `SubSections` SET Status = :newStatus WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "newStatus" => $newStatus,
        "id" => $_GET["id"],
    ]);
    $currentUrl = isset($_SERVER["HTTP_REFERER"])?explode("/",$_SERVER["HTTP_REFERER"])[5] : "sections.php";
    header("Location:$currentUrl");
}
$content = $twig -> render('subSection-list.twig', 
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "disciplines" => $disciplines,
        "sections" => $sections,
        "subSections" => $subSections,
        "subSectionsSlice" => $subSectionsSlice,
        "pages" => $pages,
        "page" => $page,
        "currentIdDiscipline" => $currentIdDiscipline,
        "currentIdSection" => $currentIdSection,
        "limit" => $limit
    ]);
print($content);