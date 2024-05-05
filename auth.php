<?php
require_once("function.php");
$title = "ЭКУМО - Авторизация";

$content = $twig -> render('auth.twig', ["title" => $title]);
print($content);