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

    if (empty($data["idDiscipline"]) || empty($data["idSection"]) || empty($data["name"]) || empty($data["content"])) {
        throw new Exception("Некорректные входные данные");
    }

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

    $arrName = [];
    $json = json_decode($content, true);
    foreach ($json['blocks'] as &$block) {
        if ($block['type'] === 'image' || $block['type'] === 'attaches') {
            $currentUrl = $block['data']['file']['url'];
            $fileName = basename($currentUrl);
            $sourcePath = "../uploads/temp/" . $fileName;
            $targetPath = "../uploads/$idDiscipline/$idSection/$idSubSection/" . $fileName;
            if (is_file($sourcePath)) {
                if (!file_exists(dirname($targetPath))) {
                    mkdir(dirname($targetPath), 0777, true);
                }
                rename($sourcePath, $targetPath);
            }
            $block['data']['file']['url'] = $targetPath;
            array_push($arrName, $fileName);
        }
    }

    $content = json_encode($json, true);
    $sql = "UPDATE `SubSections` SET Content = :content WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "content" => $content,
        "id" => $idSubSection
    ]);

    $tempFiles = scandir("../uploads/temp");
    foreach ($tempFiles as $tempFile) {
        $tempFilePath = "../uploads/temp/" . $tempFile;
        if (is_file($tempFilePath)) {
            unlink($tempFilePath);
        }
    }

    echo json_encode(["success" => true]);
} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}