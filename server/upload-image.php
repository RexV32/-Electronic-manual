<?php
if(isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $extension;
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