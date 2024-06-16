<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input_array(INPUT_POST, ["data" => FILTER_DEFAULT, "file" => FILTER_DEFAULT]);
    $decodedData = json_decode($data["data"], true);
    $multiple = $decodedData["multiple"] ? 1 : 0;
    $text = trim($decodedData["text"]);
    $id = $decodedData["id"];
    $answers = $decodedData["answers"];

    $tests = getTests($link);
    $testsIdArray = array_column($tests, "Id");
    $arr = [];

    if (validateTests($id, $testsIdArray)) {
        if (isset($_FILES['file'])) {
            $sql = "SELECT Id_disciplines FROM Tests WHERE Id = ?";
            $stmt = $link -> prepare($sql);
            $stmt -> execute([$id]);
            $idDiscipline = $stmt -> fetchColumn();

            $file = $_FILES['file'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $extension;
            $path = "../uploads/$idDiscipline/quiz/$id/" . $newFileName;
            move_uploaded_file($file["tmp_name"], $path);
        }
        
        $sql = "INSERT INTO `Questions`(`Text`, `Id_test`, `Multiple`, `Image`) VALUES (:text,:id,:multiple,:image)";
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "text" => $text,
            "id" => $id,
            "multiple" => $multiple,
            "image" => isset($_FILES['file']) ? $newFileName : null
        ]);
        $idQuestion = $link->lastInsertId();

        foreach ($answers as $answer) {
            $text = $answer["answer"];
            array_push($arr, $text);
            $correct = $answer["correct"] ? 1 : 0;
            $sql = "INSERT INTO `Answers`(`Id_question`, `Text`, `Correct`) VALUES (:id, :text, :correct)";
            $stmt = $link->prepare($sql);
            $stmt->execute([
                "id" => $idQuestion,
                "text" => $text,
                "correct" => $correct
            ]);
        }

        echo json_encode(["success" => true, "a" => $arr]);
    } else {
        echo json_encode(["success" => false, "message" => "Неудалось создать вопрос", "title" => "Ошибка"]);
    }
} catch (Exception $errors) {
    echo json_encode(["success" => false, "message" => "Неудалось создать вопрос. " . $errors->getMessage(), "title" => "Ошибка"]);
}