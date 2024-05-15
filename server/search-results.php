<?php
require_once ("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input(INPUT_POST, "data", FILTER_DEFAULT);

    $decodedData = json_decode($data, true);

    $name = $decodedData["name"];
    $surname = $decodedData["surname"];
    $patronymic = $decodedData["patronymic"];
    $login = $decodedData["login"];
    $group = $decodedData["group"];
    $idTest = $decodedData["idTest"];

    $sql = "SELECT Results.Id, Users.Name, Users.Surname, Users.Patronymic, Results.Score, `Groups`.`Name` as GroupName, Users.Login
    FROM `Results` 
    INNER JOIN `Users` ON Users.Id = Results.Id_User 
    INNER JOIN `Groups` ON `Groups`.`Id` = `Users`.`Group_id` 
    WHERE `Results`.`Id_Test` = :idTest AND Users.Status = 1 AND Users.Role_id = 1";

    $conditions = [];
    $params = [];
    if (!empty($name)) {
        $conditions[] = "Users.Name = :name";
        $params['name'] = $name;
    }
    if (!empty($surname)) {
        $conditions[] = "Users.Surname = :surname";
        $params['surname'] = $surname;
    }
    if (!empty($patronymic)) {
        $conditions[] = "Users.Patronymic = :patronymic";
        $params['patronymic'] = $patronymic;
    }
    if (!empty($login)) {
        $conditions[] = "Users.Login = :login";
        $params['login'] = $login;
    }
    if (!empty($group)) {
        $conditions[] = "Groups.Id = :group";
        $params['group'] = $group;
    }
    if (!empty($idTest)) {
        $conditions[] = "Results.Id_Test = :idTest";
        $params['idTest'] = $idTest;
    }
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    $stmt = $link->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "results" => $results]);
} catch (Exception $ex) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос", "title" => "Ошибка"]);
}