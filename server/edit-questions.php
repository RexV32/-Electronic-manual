<?php
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

function deleteFile($nameFolder, $nameFile)
{
    $path = "../uploads/quiz/$nameFolder/$nameFile";
    unlink($path);
}

function addAnswer($id, $text, $correct, $link)
{
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
    $currentPhoto = $data["currentPhoto"];
    $isDeletePhoto = $data["isDeletePhoto"] ? 1 : 0;

    $sql = "SELECT * FROM `Questions` WHERE Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$idQuestion]);
    $questionsFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
    $idTest = $questionsFromDB["Id_test"];

    $sql = "UPDATE `Questions` SET Text = :text, Multiple = :multiple WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "text" => $textQuestion,
        "multiple" => $multiple,
        "id" => $idQuestion
    ]);

    if (isset($_FILES['file'])) {
        $sql = "UPDATE `Questions` SET Image = :image WHERE Id = :id";
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "image" => basename($_FILES['file']["name"]),
            "id" => $idQuestion
        ]);

        $file = $_FILES['file'];
        $path = "../uploads/quiz/$idTest/" . basename($file['name']);
        move_uploaded_file($file["tmp_name"], $path);

        if ($currentPhoto != "") {
            deleteFile($idTest, $currentPhoto);
        }
    } else {
        if ($isDeletePhoto) {
            deleteFile($idTest, $currentPhoto);
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

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage(), "title" => "Ошибка"]);
}
