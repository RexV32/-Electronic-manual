<?php
require_once ("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');
function deleteDirectory($dir){
    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $path = "$dir/$file";
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }

    return rmdir($dir);
}
function findAndDeleteFolder($parentFolder, $targetFolder){
    $dirs = glob($parentFolder . '/*', GLOB_ONLYDIR);

    foreach ($dirs as $dir) {
        if (basename($dir) === $targetFolder) {
            deleteDirectory($dir);
            return;
        } else {
            findAndDeleteFolder($dir, $targetFolder);
        }
    }
}

try {
    $data = filter_input_array(INPUT_POST, ["id" => FILTER_DEFAULT, "section" => FILTER_DEFAULT]);
    $id = $data["id"];
    $section = $data["section"];

    if (!$id || !$section) {
        throw new Exception("Недостаточно данных для выполнения операции");
    }

    switch ($section) {
        case "discipline":
            $sql = "DELETE FROM `Disciplines` WHERE Id = ?";
            $path = "../uploads";
            break;

        case "sections":
            $sql = "DELETE FROM `Sections` WHERE Id = ?";
            $path = "../uploads";
            break;

        case "subSection":
            $sql = "DELETE FROM `SubSections` WHERE Id = ?";
            $path = "../uploads";
            break;

        case "tests":
            $sql = "SELECT Id_disciplines as idDiscipline FROM `Tests` WHERE `Id` = ?";
            $stmt = $link -> prepare($sql);
            $stmt -> execute([$id]);
            $idDiscipline = $stmt -> fetchColumn();

            $sql = "DELETE FROM `Tests` WHERE Id = ?";
            $path = "../uploads/$idDiscipline/quiz";
            break;

        case "question":
            $sql = "SELECT Image, Id_test, Disciplines.Id as idTest FROM `Questions` 
            INNER JOIN Tests ON Tests.Id = Questions.Id_test 
            INNER JOIN Disciplines ON Disciplines.Id = Tests.Id_disciplines WHERE Questions.Id = ?";
            $stmt = $link->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $idTest = $data["Id_test"];
                $image = $data["Image"];
                $idDiscipline = $data["idTest"];
            } else {
                throw new Exception("Вопрос не найден");
            }

            $sql = "DELETE FROM `Questions` WHERE Id = ?";
            if ($image != null) {
                $imagePath = "../uploads/$idDiscipline/quiz/$idTest/$image";
                if (is_file($imagePath)) {
                    unlink($imagePath);
                }
            }
            break;

        default:
            throw new Exception("Недопустимая секция");
    }

    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);

    if (isset($path) && is_dir($path)) {
        findAndDeleteFolder($path, $id);
    }

    echo json_encode(["success" => true]);
} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос: " . $error->getMessage()]);
}