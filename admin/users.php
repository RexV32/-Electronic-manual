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

if(isset($_GET["surname"], $_GET["name"], $_GET["patronymic"], $_GET["login"])) {
    $group = isset($_GET["group"]) ? $_GET["group"] : 0;
    $surname = trim($_GET["surname"]);
    $name = trim($_GET["name"]);
    $patronymic = trim($_GET["patronymic"]);
    $login = trim($_GET["login"]);

    $sql = "SELECT Users.Id, Users.Name, Users.Surname, Users.Patronymic, Users.Login, Groups.Name AS NameGroup 
    FROM `Users` 
    INNER JOIN `Groups` ON Users.Group_id = Groups.Id 
    WHERE (Users.Group_id = :idGroup OR Users.Surname = :surname OR Users.Name = :name OR Users.Patronymic = :patronymic OR Users.Login = :login) 
    AND Users.Status = 1 AND Users.Role_id = 1";
    $stmt = $link ->prepare($sql);
    $stmt -> execute([
        "idGroup" => $group,
        "surname" => $surname,
        "name" => $name,
        "patronymic" => $patronymic,
        "login" => $login
    ]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $group = 0;
}

$content = $twig -> render('user-list.twig', 
[
    "title" => $title,
    "currentSection" => $currentSection,
    "users" => $users,
    "groups" => $groups
]);
print($content);