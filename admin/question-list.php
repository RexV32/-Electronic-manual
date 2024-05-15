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
$currentIdTest = 0;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$tests = getTests($link);

if (count($tests) > 0) {
    $currentIdTest = $tests[0]["Id"];
    $sql = "SELECT * FROM `Questions` WHERE `Id_test` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$currentIdTest]);
    $questions = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    $questionSlice = array_slice($questions, $offset, $limit, true);
    $questionsQuantity = count($questions);
    $pages = ceil($questionsQuantity / $limit);
}

if(isset($_GET["test"])) {
    $currentIdTest = $_GET["test"];
    $sql = "SELECT * FROM `Questions` WHERE `Id_test` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$currentIdTest]);
    $questions = $stmt -> fetchAll(PDO::FETCH_ASSOC);
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
        "page" => $page,
        "currentIdTest" => $currentIdTest
    ]
);
print($content);