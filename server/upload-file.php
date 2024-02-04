<?php
if(isset($_FILES)) {
    $file = $_FILES["file"];
    $fileName = basename($file["name"]);
    $targetFile = "../uploads/temp/$fileName";
    $url = "../uploads/temp/$fileName";

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo json_encode(['success' => true, 'file' => ['url' => $url]], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при загрузке файла.'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Файл не был загружен.'], JSON_UNESCAPED_UNICODE);
}