<?php
require_once("function.php");
$title = "ЭМКУ - Авторизация";

$content = $twig -> render('auth.twig', ["title" => $title]);
print($content);