<?php
require_once("../server/connect.php");
$id = filter_input(INPUT_POST, 'id', FILTER_DEFAULT);

$sql = "SELECT Id,Name FROM `Sections` WHERE `Id_discipline` = ?";
$stmt = $link->prepare($sql);

try {
    $stmt->execute([$id]);
    $section = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($section);
} catch (PDOException $exception) {
    echo json_encode(["error" => "Не удалось выполнить запрос"]);
}
