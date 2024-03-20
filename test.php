<?php
require_once("function.php");
$title = "ЭМКУ";
$currentSection = "test";
if(!isset($_SESSION["user"])) {
    header("Location: auth.php");
}

$array = [];
$_GET["id"] = 5;

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "SELECT Name FROM `Tests` WHERE Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);
    $TestName = $stmt->fetchColumn();


    $sql = "SELECT q.Id AS IdQuestions,
    q.Text AS TextQuestions,
    q.Multiple AS MultipleQuestions,
    q.Image AS ImageQuestions,
    GROUP_CONCAT(a.Id) AS IdAnswers,
    GROUP_CONCAT(a.Text) AS TextAnswers 
    FROM `Questions` AS q INNER JOIN `Answers` AS a ON a.Id_question = q.Id WHERE q.`Id_test` = ? GROUP BY q.Id";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($questions as $question) {
        $idAnswersArr = explode(",", $question["IdAnswers"]);
        $textAnswersArr = explode(",", $question["TextAnswers"]);
        unset($question["IdAnswers"]);
        unset($question["TextAnswers"]);
        $question["answers"] = [];
        for($i = 0; $i < count($idAnswersArr); $i++) {
            $id = $idAnswersArr[$i];
            $text = $textAnswersArr[$i];
            array_push($question["answers"], ["IdAnswer" => $id, "TextAnswer" => $text]);
        }
        array_push($array, $question);
    }
    $questions = $array;
} else {
    header("Location:index.php");
}

$sql = "";
$content = $twig->render('test.twig', [
    "title" => $title,
    "TestName" => $TestName,
    "questions" => $questions,
    "currentSection" => $currentSection
]);
print($content);