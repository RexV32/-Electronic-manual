<?php
require_once("../function.php");
if(!isset($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}


$title = "ЭКУМО - список пользователей";
$currentSection = "listUser";
$users = [];

$groups = getGroup($link);

$sql = "SELECT Users.Id,Users.Name,Users.Surname,Users.Patronymic,Users.Login,Groups.Name as NameGroup 
FROM `Users` INNER JOIN `Groups` ON Users.Group_id = Groups.Id WHERE `Status` = 1 AND Users.Role_id = 1";
$stmt = $link ->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$content = $twig -> render('user-list.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "users" => $users,
    "groups" => $groups
]);
print($content);