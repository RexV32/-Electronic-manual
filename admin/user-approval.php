<?php
require_once("../server/function.php");
$title = "ЭМКУ - Заявки на регистрацию";
$currentSection = "userApproval";

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT Users.Id,Users.Name,Users.Surname,Users.Patronymic,Users.Login,Groups.Name as NameGroup 
FROM `Users` INNER JOIN `Groups` ON Users.Group_id = Groups.Id WHERE `Status` = 0";
$stmt = $link -> query($sql);
$users = $stmt -> fetchAll(PDO::FETCH_ASSOC);
$usersSlice = array_slice($users,$offset,$limit,true);
$usersQuantity = count($users);
$pages = ceil($usersQuantity / $limit);

$content = $twig -> render('user-approval.twig',
[
    "title" => $title,
    "currentSection" => $currentSection,
    "users" => $users,
    "usersSlice" => $usersSlice,
    "pages" => $pages,
    "limit" => $limit,
    "page" => $page
]);
print($content);