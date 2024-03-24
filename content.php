<?php
require_once ("function.php");
if (!isset ($_SESSION["user"])) {
    header("Location: auth.php");
}
$title = "ЭМКУ";
$array = [];
$data = [];
$template = "content.twig";
$currentSection = "content";
$role = $_SESSION["user"]["Role_id"];

$sql = "SELECT Disciplines.Id as IdDisciplines,
    Disciplines.Name as NameDiscipline,
    GROUP_CONCAT(Sections.Name) as SectionName,
    GROUP_CONCAT(Sections.Id) as SectionId FROM `Disciplines` 
    INNER JOIN Sections ON Disciplines.Id = Sections.Id_discipline WHERE Disciplines.Status = 1 AND Sections.Status = 1 GROUP BY Disciplines.Id";
$stmt = $link->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $item) {
    $idArray = explode(",", $item["SectionId"]);
    $nameArray = explode(",", $item["SectionName"]);
    unset($item["SectionId"]);
    unset($item["SectionName"]);
    $item["Section"] = [];
    for ($i = 0; $i < count($idArray); $i++) {
        $id = $idArray[$i];
        $name = $nameArray[$i];
        array_push($item["Section"], ["Id" => $id, "Name" => $name]);
    }
    array_push($array, $item);
}
$data = $array;

$content = $twig->render($template, [
    "title" => $title,
    "data" => $data,
    "template" => $template,
    "currentSection" => $currentSection,
    "role" => $role
]);
print($content);