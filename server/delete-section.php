<?php
require_once("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');
function deleteDirectory($dir) {
    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $path = "$dir/$file";
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }

    return rmdir($dir);
}
function findAndDeleteFolder($parentFolder, $targetFolder) {
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

$data = filter_input_array(INPUT_POST, ["id" => FILTER_DEFAULT, "section" => FILTER_DEFAULT]);
$id = $data["id"];
$section = $data["section"];

switch ($section) {
    case "discipline":
        $sql = "DELETE FROM `Disciplines` WHERE Id = ?";
        $stmt = $link->prepare($sql);

        try {
            $path = "../uploads/$id";
            if (is_dir($path)) {
                deleteDirectory($path);
            }
            $success = $stmt->execute([$id]);
            echo json_encode(["success" => true]);
        } catch (PDOException $exception) {
            echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
        }
        break;

    case "sections":
        $sql = "DELETE FROM `Sections` WHERE Id = ?";
        $stmt = $link->prepare($sql);

        try {
            $path = "../uploads";
            if (is_dir($path)) {
                findAndDeleteFolder($path, $id);
            }
            $success = $stmt->execute([$id]);
            echo json_encode(["success" => true]);
        } catch (PDOException $exception) {
            echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
        }
        break;
    case "subSection":
        $sql = "SELECT Disciplines.Id as idDiscipline,Sections.Id as idSection,`SubSections`.`Content` FROM `SubSections` 
        INNER JOIN Sections ON Sections.Id = SubSections.Id_section 
        INNER JOIN Disciplines ON Disciplines.Id = Sections.Id_discipline WHERE SubSections.Id = ?";
        $stmt = $link->prepare($sql);
        $stmt -> execute([$id]);
        $data = $stmt -> fetch(PDO::FETCH_ASSOC);
        $idDiscipline = $data["idDiscipline"];
        $idSection = $data["idSection"];
        $json = json_decode($data["Content"]);
        for ($i = 0; $i < count($json->blocks); $i++) {
            if (isset($json->blocks[$i]->data)) {
                $data = $json->blocks[$i]->data;
                if (isset($data->file)) {
                    $file = $data->file;
                    if (isset($file->url)) {
                        $url = $file->url;
                        $fileName = explode("/", $url)[3];
                        $path = "../uploads/$idDiscipline/$idSection/$fileName";
                        if(file_exists($path)) {
                            unlink(realpath($path));
                        }
                    }
                }
            }
        }
        $sql = "DELETE FROM `SubSections` WHERE Id = ?";
        $stmt = $link->prepare($sql);
        try {
            $success = $stmt->execute([$id]);
            echo json_encode(["success" => true]);
        } catch (PDOException $exception) {
            echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
        }
        break;
}
