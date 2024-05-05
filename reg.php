<?php
require_once("function.php");
$title = "ЭКУМО - Регистрация";
$errors = [];

$sql = "SELECT * FROM `Groups`";
$stmt = $link->query($sql);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

$content = $twig->render('reg.twig', [
    "title" => $title,
    "groups" => $groups,
    "errors" => $errors
]);
print($content);