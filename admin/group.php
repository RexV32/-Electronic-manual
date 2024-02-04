<?php
require_once("../server/function.php");
$title = "ЭМКУ - Список групп";
$currentSection = "Group";

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$groups = getGroup($link);
$groupsSlice = array_slice($groups,$offset,$limit,true);
$groupsQuantity = count($groups);
$pages = ceil($groupsQuantity / $limit);

$content = $twig -> render('group-list.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "groups" => $groups,
    "pages" => $pages,
    "groupsSlice" => $groupsSlice,
    "page" => $page,
    "limit" => $limit
]);
print($content);
?>