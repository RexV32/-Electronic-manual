<?php
require_once ("../server/connect.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $data = filter_input(INPUT_POST, "data", FILTER_DEFAULT);

    $decodedData = json_decode($data, true);

    $name = $decodedData["name"] ?? null;
    $surname = $decodedData["surname"] ?? null;
    $patronymic = $decodedData["patronymic"] ?? null;
    $login = $decodedData["login"] ?? null;
    $group = $decodedData["group"] ?? null;

    $sql = "SELECT Users.Id, Users.Name, Users.Surname, Users.Patronymic, Users.Login, Groups.Name AS NameGroup 
    FROM `Users` 
    INNER JOIN `Groups` ON Users.Group_id = Groups.Id
    WHERE Users.Status = 1 AND Users.Role_id = 1";
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
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    $stmt = $link->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "users" => $users, "query" => $sql]);
} catch (Exception $ex) {
    echo json_encode(["success" => false, "message" => "Не удалось выполнить запрос", "title" => "Ошибка"]);
}