<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭКУМО - Панель администратора";

$content = $twig -> render('stub.twig', ["title" => $title]);
print($content);
