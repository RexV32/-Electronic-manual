<?php
require_once ("../server/connect.php");

try {
    $id = filter_input(INPUT_POST, 'id', FILTER_DEFAULT);
    $sql = "SELECT Content, Name FROM `SubSections` WHERE Id = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($content);
} catch (Exception $error) {
    echo json_encode(["error" => $error->getMessage()]);
}