<?php
require_once ("function.php");
$title = "ЭКУМО";
$currentSection = "test";
if (!isset($_SESSION["user"])) {
    header("Location: auth.php");
}

$data = [];
$questionsAarray = [];
$role = $_SESSION["user"]["Role_id"];
$idUser = $_SESSION["user"]["Id"];
$idTest = $_GET["id"];

$sql = "SELECT * FROM `Results` WHERE Id_User = :idUser AND Id_Test = :IdTest";
$stmt = $link->prepare($sql);
$stmt->execute([
    "idUser" => $idUser,
    "IdTest" => $idTest
]);

if ($stmt->rowCount() > 0) {
    header("Location:index.php");
}

$sql = "SELECT Id as IdDisciplines, Name as NameDiscipline FROM Disciplines WHERE Status = 1";
$stmt = $link->prepare($sql);
$stmt->execute();
$disciplinesArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT Id, Name, Id_discipline FROM Sections WHERE Status = 1";
$stmt = $link->prepare($sql);
$stmt->execute();
$sectionsArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT Id_disciplines FROM Tests";
$stmt = $link->prepare($sql);
$stmt->execute();
$testDisciplines = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($disciplinesArray as $discipline) {
    $idDiscipline = $discipline["IdDisciplines"];
    $discipline["Section"] = [];

    foreach ($sectionsArray as $section) {
        if ($section["Id_discipline"] == $idDiscipline) {
            $id = $section["Id"];
            $name = $section["Name"];
            array_push($discipline["Section"], ["Id" => $id, "Name" => $name]);
        }
    }

    $discipline["isTest"] = in_array($idDiscipline, $testDisciplines);

    array_push($data, $discipline);
}

if (isset($_GET["id"]) && $_GET["id"] != "") {
    $sql = "SELECT Name FROM `Tests` WHERE Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$idTest]);
    $TestName = $stmt->fetchColumn();


    $sql = "SELECT q.Id AS IdQuestions,
    q.Text AS TextQuestions,
    q.Multiple AS MultipleQuestions,
    q.Image AS ImageQuestions,
    GROUP_CONCAT(a.Id) AS IdAnswers,
    GROUP_CONCAT(a.Text) AS TextAnswers 
    FROM `Questions` AS q INNER JOIN `Answers` AS a ON a.Id_question = q.Id WHERE q.`Id_test` = ? GROUP BY q.Id";
    $stmt = $link->prepare($sql);
    $stmt->execute([$idTest]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as $question) {
        $idAnswersArr = explode(",", $question["IdAnswers"]);
        $textAnswersArr = explode(",", $question["TextAnswers"]);
        unset($question["IdAnswers"]);
        unset($question["TextAnswers"]);
        $question["answers"] = [];
        for ($i = 0; $i < count($idAnswersArr); $i++) {
            $id = $idAnswersArr[$i];
            $text = $textAnswersArr[$i];
            array_push($question["answers"], ["IdAnswer" => $id, "TextAnswer" => $text]);
        }
        array_push($questionsAarray, $question);
    }
} else {
    header("Location:index.php");
}
$content = $twig->render("test.twig", [
    "title" => $title,
    "TestName" => $TestName,
    "questions" => $questionsAarray,
    "currentSection" => $currentSection,
    "data" => $data,
    "role" => $role
]);
print ($content);