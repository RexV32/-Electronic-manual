<?php
require_once("function.php");
if(!isset($_SESSION["user"])) {
    header("Location: auth.php");
}

$role = $_SESSION["user"]["Role_id"];
$title = "ЭМКУ";

$content = $twig -> render('index.twig', ["title" => $title, "role" => $role]);
print($content);
