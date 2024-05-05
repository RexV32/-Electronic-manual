<?php
require_once("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Управление тестами";
$currentSection = "editorTests";
$questionSlice = [];
$questions = [];
$pages = 0;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$tests = getTests($link);

if (count($tests) > 0) {
    $questions = getQuestions($link);
    $questionSlice = array_slice($questions, $offset, $limit, true);
    $questionsQuantity = count($questions);
    $pages = ceil($questionsQuantity / $limit);
}

$content = $twig->render(
    'question-list.twig',
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "tests" => $tests,
        "questionSlice" => $questionSlice,
        "pages" => $pages,
        "questions" => $questions,
        "limit" => $limit,
        "page" => $page
    ]
);
print($content);