<?php
require_once("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Добавить вопрос";
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