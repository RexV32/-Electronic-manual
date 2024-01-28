<?php
require_once("server/function.php");
$title = "ЭМКУ - Список дисциплин";
$currentSection = "Discipline";

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM `Disciplines`";
$stmt = $link ->query($sql);
$disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    header("Location:disciplines.php");
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