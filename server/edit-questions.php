<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

function deleteFile($nameFolder, $nameFile, $idDiscipline) {
    $path = "../uploads/$idDiscipline/quiz/$nameFolder/$nameFile";
    unlink($path);
}

function addAnswer($id, $text, $correct, $link) {
    $sql = "INSERT INTO `Answers`(`Id_question`, `Text`, `Correct`) VALUES (?, ?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id, $text, $correct]);
}

try {
    $data = filter_input_array(INPUT_POST, ["data" => FILTER_DEFAULT, "file" => FILTER_DEFAULT, "currentPhoto" => FILTER_DEFAULT, "isDeletePhoto" => FILTER_DEFAULT]);
    $decodedData = json_decode($data["data"], true);
    $multiple = $decodedData["multiple"] ? 1 : 0;
    $textQuestion = trim($decodedData["text"]);
    $idQuestion = $decodedData["id"];
    $answers = $decodedData["answers"];
    $currentPhoto = pathinfo($data["currentPhoto"], PATHINFO_BASENAME);
    $isDeletePhoto = $data["isDeletePhoto"] ? 1 : 0;

    $sql = "SELECT Questions.Id_test, Disciplines.Id FROM `Questions` 
    INNER JOIN Tests ON Tests.Id = Questions.Id_test 
    INNER JOIN Disciplines ON Disciplines.Id = Tests.Id_disciplines WHERE Questions.Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$idQuestion]);
    $questionsFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
    $idTest = $questionsFromDB["Id_test"];
    $idDiscipline = $questionsFromDB["Id"];

    $sql = "UPDATE `Questions` SET Text = :text, Multiple = :multiple WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "text" => $textQuestion,
        "multiple" => $multiple,
        "id" => $idQuestion
    ]);

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $extension;
        $path = "../uploads/$idDiscipline/quiz/$idTest/" . $newFileName;
        move_uploaded_file($file["tmp_name"], $path);

        $sql = "UPDATE `Questions` SET Image = :image WHERE Id = :id";
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "image" => isset($_FILES['file']) ? $newFileName : null,
            "id" => $idQuestion
        ]);

        if ($currentPhoto != "") {
            deleteFile($idTest, $currentPhoto, $idDiscipline);
        }
    } else {
        if ($isDeletePhoto) {
            deleteFile($idTest, $currentPhoto, $idDiscipline);
            $sql = "UPDATE `Questions` SET Image = :image WHERE Id = :id";
            $stmt = $link->prepare($sql);
            $stmt->execute([
                "image" => null,
                "id" => $idQuestion
            ]);
        }
    }

    foreach ($answers as $answer) {
        if (isset($answer["id"])) {
            $id = $answer["id"];
            $text = $answer["answer"];
            $correct = $answer["correct"] ? 1 : 0;
            $isDelete = $answer["isDelete"] ? 1 : 0;
            $sql = "SELECT * FROM `Answers` WHERE Id = ?";
            $stmt = $link->prepare($sql);
            $stmt->execute([$id]);
            if ($isDelete && $stmt->rowCount()) {
                $sql = "DELETE FROM `Answers` WHERE Id = ?";
                $stmt = $link->prepare($sql);
                $stmt->execute([$id]);
            } else {
                if ($stmt->rowCount()) {
                    $sql = "UPDATE `Answers` SET Text = :text, Correct = :correct  WHERE Id = :id";
                    $stmt = $link->prepare($sql);
                    $stmt->execute([
                        "text" => $text,
                        "correct" => $correct,
                        "id" => $id
                    ]);
                } else {
                    addAnswer($idQuestion, $text, $correct, $link);
                }
            }
        } else {
            $text = $answer["answer"];
            $correct = $answer["correct"] ? 1 : 0;
            addAnswer($idQuestion, $text, $correct, $link);
        }
    }

    echo json_encode(["success" => true, "current" => $currentPhoto]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage(), "title" => "Ошибка"]);
}
