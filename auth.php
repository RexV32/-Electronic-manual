<?php
require_once("function.php");
$title = "ЭМКУ - Авторизация";
$currentSection = "reg";

$content = $twig -> render('auth.twig', ["title" => $title]);
print($content);