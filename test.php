<?php
require_once("function.php");
$title = "ЭМКУ";
$currentSection = "test";
if(!isset($_SESSION["user"])) {
    header("Location: auth.php");
}

$array = [];
$role = $_SESSION["user"]["Role_id"];

$sql = "SELECT Disciplines.Id as IdDisciplines,
    Disciplines.Name as NameDiscipline,
    GROUP_CONCAT(Sections.Name) as SectionName,
    GROUP_CONCAT(Sections.Id) as SectionId FROM `Disciplines` 
    INNER JOIN Sections ON Disciplines.Id = Sections.Id_discipline WHERE Disciplines.Status = 1 AND Sections.Status = 1 GROUP BY Disciplines.Id";
$stmt = $link->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $item) {
    $idArray = explode(",", $item["SectionId"]);
    $nameArray = explode(",", $item["SectionName"]);
    unset($item["SectionId"]);
    unset($item["SectionName"]);
    $item["Section"] = [];
    for ($i = 0; $i < count($idArray); $i++) {
        $id = $idArray[$i];
        $name = $nameArray[$i];
        array_push($item["Section"], ["Id" => $id, "Name" => $name]);
    }
    array_push($array, $item);
}
$data = $array;
$array = [];
if (isset($_GET["id"]) && $_GET["id"] != "") {
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

    if(count($questions) <= 0) {
        header("Location:index.php");
    }

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
$template = "test.twig";
$content = $twig->render($template, [
    "title" => $title,
    "TestName" => $TestName,
    "questions" => $questions,
    "currentSection" => $currentSection,
    "data" => $data,
    "role" => $role
]);
print($content);