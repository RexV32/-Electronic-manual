<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭМКУ - Список дисциплин";
$currentSection = "Discipline";

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$disciplines = getDisciplines($link);
$disciplinesSlice = array_slice($disciplines,$offset,$limit,true);
$disciplinesQuantity = count($disciplines);
$pages = ceil($disciplinesQuantity / $limit);

if(isset($_GET["id"], $_GET["status"])) {
    $newStatus = ($_GET["status"] == 1) ? 0 : 1;
    $sql = "UPDATE Disciplines SET Status = :newStatus WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "newStatus" => $newStatus,
        "id" => $_GET["id"],
    ]);

    $currentUrl = isset($_SERVER["HTTP_REFERER"])?explode("/",$_SERVER["HTTP_REFERER"])[5] : "disciplines.php";
    header("Location:$currentUrl");
}

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