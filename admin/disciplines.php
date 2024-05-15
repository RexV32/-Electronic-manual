<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Список дисциплин";
$currentSection = "Discipline";

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$disciplines = getDisciplines($link);
$disciplinesSlice = array_slice($disciplines,$offset,$limit,true);
$disciplinesQuantity = count($disciplines);
$pages = ceil($disciplinesQuantity / $limit);

$content = $twig -> render('disciplines-list.twig', 
[
    "disciplines" => $disciplines,
    "title" => $title,
    "currentSection" => $currentSection,
    "disciplinesSlice" => $disciplinesSlice,
    "pages" => $pages,
    "page" => $page,
    "limit" => $limit
]);
print($content);