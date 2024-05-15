<?php
require_once ("../function.php");
if (!isset ($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Результаты теста";
$currentSection = "results";
$id = isset($_GET['id']) ? $_GET['id'] : 0;

$results = getResults($link, $id);
$groups = getGroup($link);

$content = $twig->render(
    'results-list.twig',
    [
        "title" => $title,
        "results" => $results,
        "groups" => $groups,
        "currentSection" => $currentSection
    ]
);
print ($content);