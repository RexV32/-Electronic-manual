<?php
require_once("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Редактирование вопроса";
$currentSection = "editQuestion";

if(isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "SELECT * FROM `Answers` WHERE `Id_question` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$id]);
    $answers = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    if($stmt -> rowCount() == 0) {
        header("Location:question-list.php");
    }

    $sql = "SELECT Disciplines.Id as IdDiscipline, Questions.Id_test, Questions.Id as Id, Questions.Text, Questions.Multiple, Questions.Image
    FROM `Questions` 
    INNER JOIN Tests ON Tests.Id = Questions.Id_test 
    INNER JOIN Disciplines ON Disciplines.Id = Tests.Id_disciplines WHERE Questions.Id = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$id]);
    $question = $stmt -> fetch(PDO::FETCH_ASSOC);
}
else {
    header("Location:question-list.php");
}

$content = $twig->render(
    'edit-question.twig',
    [
        "title" => $title,
        "currentSection" => $currentSection,
        "question" => $question,
        "answers" => $answers
    ]
);
print($content);