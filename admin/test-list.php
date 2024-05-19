<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Тесты";
$currentSection = "test";
$tests = [];
$testSlice = [];
$pages = 0;
$currentIdDiscipline = 0;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$disciplines = getDisciplines($link);
$disciplinesQuantity = count($disciplines);

if($disciplinesQuantity > 0) {
    $currentIdDiscipline = $disciplines[0]["Id"];
    $sql = "SELECT * FROM `Tests` WHERE `Id_disciplines` = ?";
    $stmt = $link ->prepare($sql);
    $stmt -> execute([$currentIdDiscipline]);
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $testSlice = array_slice($tests,$offset,$limit,true);
    $testQuantity = count($tests);
    $pages = ceil($testQuantity / $limit);
}

if(isset($_GET["discipline"])) {
    $currentIdDiscipline = $_GET["discipline"];
    $sql = "SELECT * FROM `Tests` WHERE `Id_disciplines` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$currentIdDiscipline]);
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $testSlice = array_slice($tests,$offset,$limit,true);
    $testQuantity = count($tests);
    $pages = ceil($testQuantity / $limit);
}

$content = $twig -> render('test-list.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "tests" => $tests,
    "limit" => $limit,
    "pages" => $page,
    "testSlice" => $testSlice,
    "page" => $page,
    "disciplines" => $disciplines,
    "currentIdDiscipline" => $currentIdDiscipline

]);
print($content);