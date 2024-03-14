<?php
require_once("../function.php");
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭМКУ - Редактирование вопроса";
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

    $sql = "SELECT * FROM `Questions` WHERE `Id` = ?";
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