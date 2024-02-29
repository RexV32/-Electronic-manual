<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

$title = "ЭМКУ - Тесты";
$currentSection = "test";
$questionSlice = [];
$pages = 0;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$tests = getTests($link);
$testSlice = array_slice($tests,$offset,$limit,true);
$testQuantity = count($tests);
$pages = ceil($testQuantity / $limit);

if(isset($_GET["id"], $_GET["status"])) {
    $newStatus = ($_GET["status"] == 1) ? 0 : 1;
    $sql = "UPDATE Tests SET Status = :newStatus WHERE Id = :id";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "newStatus" => $newStatus,
        "id" => $_GET["id"],
    ]);

    $currentUrl = isset($_SERVER["HTTP_REFERER"])?explode("/",$_SERVER["HTTP_REFERER"])[5] : "test-list.php";
    header("Location:$currentUrl");
}

$content = $twig -> render('test-list.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "tests" => $tests,
    "limit" => $limit,
    "pages" => $page,
    "testSlice" => $testSlice,

]);
print($content);