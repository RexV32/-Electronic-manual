<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Добавить дисциплину";
$currentSection = "add-discipline";

$content = $twig -> render('add-disciplines.twig',
[
    "title" => $title,
    "currentSection" => $currentSection
]);
print($content);