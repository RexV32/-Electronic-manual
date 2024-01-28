<?php
require_once("../server/connect.php");

$data = filter_input_array(INPUT_POST, 
[
    "name" => FILTER_DEFAULT,
    "content" => FILTER_DEFAULT,
    "id" => FILTER_DEFAULT
]);
$id = $data["id"];
$name = $data["name"];
$content = $data["content"];

$sql = "SELECT Sections.Id as idSection, Disciplines.Id as idDiscipline FROM SubSections 
INNER JOIN Sections ON SubSections.Id_section = Sections.Id 
INNER JOIN Disciplines ON Sections.Id_discipline = Disciplines.Id WHERE SubSections.Id = ?";
$stmt = $link -> prepare($sql);
$stmt -> execute([$id]);
$data = $stmt -> fetch(PDO::FETCH_ASSOC);

$idDiscipline = $data["idDiscipline"];
$idSection = $data["idSection"];
$idSubSection = $id;
$arrName = [];

$sourceFolder = "../uploads/temp";
$targetFolder = "../uploads/$idDiscipline/$idSection/$idSubSection";
$files = scandir($sourceFolder);
foreach ($files as $file) {
    $sourcePath = $sourceFolder . '/' . $file;
    if (is_file($sourcePath)) {
        $targetPath = $targetFolder . '/' . $file;
        rename($sourcePath, $targetPath);
    }
}

$json = json_decode($content, true);
foreach ($json['blocks'] as &$block) {
    if ($block['type'] === 'image' || $block['type'] === 'attaches') {
        $currentUrl = $block['data']['file']['url'];
        $fileName = basename($currentUrl);
        $newUrl = "./uploads/$idDiscipline/$idSection/$idSubSection/$fileName";
        $block['data']['file']['url'] = $newUrl;
        array_push($arrName, $fileName);
    }
}

$path = "../uploads/$idDiscipline/$idSection/$idSubSection";
$files = scandir($path);

foreach($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }
    if (!in_array($file, $arrName)) {
        $filePath = $path . '/' . $file;
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}
$content = json_encode($json, true);

$sql = "UPDATE `SubSections` SET Content = :content, Name = :name WHERE Id = :id";
$stmt = $link -> prepare($sql);
$stmt -> execute([
    "content" => $content,
    "name"  => $name,
    "id" => $id
]);

try {
    $success = $stmt -> execute([
        "content" => $content,
        "name"  => $name,
        "id" => $id
    ]);
    echo json_encode(["success" => true]);
} catch (PDOException $exception) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
}