<?php
require_once("../server/function.php");
$title = "ЭМКУ - Панель администратора";

$content = $twig -> render('stub.twig', ["title" => $title]);
print($content);
