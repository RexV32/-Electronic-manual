<?php
require_once ("../function.php");
if (!isset ($_SESSION["user"]) || $_SESSION["user"]["Role_id"] == 1) {
    header("Location: ../index.php");
}

function getResults($link, $id) {
    $sql = "SELECT
    Results.Id,
    Users.Name,
    Users.Surname,
    Users.Patronymic,
    Results.Score,
    `Groups`.`Name` as GroupName  FROM `Results` 
    INNER JOIN `Users` ON Users.Id = Results.Id_User 
    INNER JOIN `Groups` ON `Groups`.`Id` = `Users`.`Group_id` WHERE Results.Id_Test = ?";
    $stmt = $link->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$title = "ЭМКУ - Результаты теста";
$currentSection = "results";
$idTest = isset($_SESSION['idTest']) ? $_SESSION['idTest'] : $_GET["id"];
if (isset($_GET["id"])) {
    $_SESSION['idTest'] = $_GET["id"];
}

$results = getResults($link, $idTest);
$groups = getGroup($link);

if (isset ($_GET["surname"], $_GET["name"], $_GET["patronymic"])) {
    $group = isset ($_GET["group"]) ? $_GET["group"] : 0;
    $surname = trim($_GET["surname"]);
    $name = trim($_GET["name"]);
    $patronymic = trim($_GET["patronymic"]);

    $sql = "SELECT
    Results.Id,
    Users.Name,
    Users.Surname,
    Users.Patronymic,
    Results.Score,
    `Groups`.`Name` as GroupName  FROM `Results` 
    INNER JOIN `Users` ON Users.Id = Results.Id_User 
    INNER JOIN `Groups` ON `Groups`.`Id` = `Users`.`Group_id` WHERE 
    (`Users`.`Name` = :name OR `Users`.`Surname` = :surname OR `Users`.`Patronymic` = :patronymic OR `Users`.`Group_id` = :group) 
    AND `Results`.`Id_Test` = :idTest";
    $stmt = $link->prepare($sql);
    $stmt->execute([
        "name" => $name,
        "surname" => $surname,
        "patronymic" => $patronymic,
        "group" => $group,
        "idTest" => $idTest
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $group = 0;
}

$content = $twig->render(
    'results-list.twig',
    [
        "title" => $title,
        "results" => $results,
        "idTest" => $idTest,
        "groups" => $groups,
        "currentSection" => $currentSection
    ]
);
print ($content);