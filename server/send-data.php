<?php
require_once("../server/connect.php");

header('Content-Type: application/json; charset=utf-8');

$data = filter_input_array(INPUT_POST, 
[
    "idDiscipline" => FILTER_DEFAULT,
    "idSection" => FILTER_DEFAULT,
    "name" => FILTER_DEFAULT,
    "content" => FILTER_DEFAULT
]);
$idDiscipline = $data["idDiscipline"];
$idSection = $data["idSection"];
$name = $data["name"];
$content = $data["content"];

$sql = "INSERT INTO `SubSections`(Name, Id_section, Content) VALUES (:name, :idSection, :content)";
$stmt = $link->prepare($sql);

try {
    $success = $stmt->execute([
        "name" => $name,
        "idSection" => $idSection,
        "content" => $content
    ]);
    $sourceFolder = "../uploads/temp";
    $targetFolder = "../uploads/$idDiscipline/$idSection";
    $files = scandir($sourceFolder);

    foreach ($files as $file) {
        $sourcePath = $sourceFolder . '/' . $file;
        if (is_file($sourcePath)) {
            $targetPath = $targetFolder . '/' . $file;
            rename($sourcePath, $targetPath);
        }
    }

    echo json_encode(["success" => true]);
} catch (PDOException $exception) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
}