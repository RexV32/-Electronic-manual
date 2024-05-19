<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - Добавить группу";
$currentSection = "addGroup";

$content = $twig -> render('add-group.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection
]);
print($content);