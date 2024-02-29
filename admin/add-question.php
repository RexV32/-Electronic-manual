<?php
require_once("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭМКУ - Добавить вопрос";
$currentSection = "addQuestion";

$tests = getTests($link);

$content = $twig->render(
    'add-question.twig',
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "tests" => $tests
    ]
);
print($content);