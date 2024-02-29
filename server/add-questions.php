<?php
require_once("../function.php");
header('Content-Type: application/json; charset=utf-8');

$data = filter_input_array(INPUT_POST, ["data" => FILTER_DEFAULT, "file" => FILTER_DEFAULT]);
$decodedData = json_decode($data["data"], true);;
$multiple = $decodedData["multiple"] ? 1 : 0;
$text = $decodedData["text"];
$id = $decodedData["id"];
$questions = $decodedData["questions"];

$tests = getTests($link);
$testsIdArray = array_column($tests, "Id");

if(validateTests($id, $testsIdArray)) {
    $sql = "INSERT INTO `Questions`(`Text`, `Id_test`, `Multiple`, `Image`) VALUES (:text,:id,:multiple,:image)";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([
        "text" => $text,
        "id" => $id,
        "multiple" => $multiple,
        "image" => isset($_FILES['file'])? basename($_FILES['file']["name"]) : null
    ]);
    $idQuestion = $link -> lastInsertId();

    if(isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $path = "../uploads/quiz/$id/" . basename($file['name']);
        move_uploaded_file($file["tmp_name"], $path);
    }

    foreach($questions as $question) {
        $text = $question["answer"];
        $correct = $question["correct"] ? 1 : 0;
        $sql = "INSERT INTO `Answers`(`Id_question`, `Text`, `Correct`) VALUES (:id, :text, :correct)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([
            "id" => $idQuestion,
            "text" => $text,
            "correct" => $correct
        ]);
    }

    echo json_encode(["success" => true]);
}
else {
    echo json_encode(["success" => false, "message" => "Некорректный тест", "title" => "Ошибка"]);
}
