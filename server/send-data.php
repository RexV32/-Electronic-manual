<?php
require_once ("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input_array(
        INPUT_POST,
        [
            "idDiscipline" => FILTER_DEFAULT,
            "idSection" => FILTER_DEFAULT,
            "name" => FILTER_DEFAULT,
            "content" => FILTER_DEFAULT
        ]
    );
    $idDiscipline = $data["idDiscipline"];
    $idSection = $data["idSection"];
    $name = $data["name"];
    $content = $data["content"];

    $sql = "INSERT INTO `SubSections`(Name, Id_section) VALUES (:name, :idSection)";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "name" => $name,
        "idSection" => $idSection
    ]);
    $idSubSection = $link->lastInsertId();
    $path = "../uploads/$idDiscipline/$idSection/$idSubSection";
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

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
            $newUrl = "../uploads/$idDiscipline/$idSection/$idSubSection/$fileName";
            $block['data']['file']['url'] = $newUrl;
        }
    }
    $content = json_encode($json, true);
    $sql = "UPDATE `SubSections` SET Content = :content WHERE Id_section = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "content" => $content,
        "id" => $idSection
    ]);

    echo json_encode(["success" => true]);
} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос"]);
}