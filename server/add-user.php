<?php
require_once("../function.php");
header('Content-Type: application/json; charset=utf-8');

$data = filter_input(INPUT_POST, "data", FILTER_DEFAULT);
$decodedData = json_decode($data, true);
$groups = getGroup($link);
$groupsIdArray = array_column($groups, "Id");
$errors = [];

$rules = [
    "name" => function ($value) {
        return validateUser($value);
    },
    "surname" => function ($value) {
        return validateUser($value);
    },
    "patronymic" => function ($value) {
        return validateUser($value);
    },
    "login" => function ($value) use ($link) {
        return validateLogin($value, $link);
    },
    "group" => function ($value) use ($groupsIdArray) {
        return validateGroup($value, $groupsIdArray);
    },
    "password" => function ($value) {
        return validatePassword($value);
    },
    "confirm" => function ($value) {
        return validatePassword($value);
    }
];

foreach ($decodedData as $key => $value) {
    if (isset($rules[$key])) {
        $rule = $rules[$key];
        $errors[$key] = $rule($value);
    }
}

if ($decodedData["password"] !== $decodedData["confirm"]) {
    $errors["confirm"] = "Пароли не совпадают";
    $errors["password"] = "Пароли не совпадают";
}

$errors = array_filter($errors);

if (!count($errors)) {
    try {
        $name = $decodedData["name"];
        $surname = $decodedData["surname"];
        $patronymic = $decodedData["patronymic"];
        $login = $decodedData["login"];
        $group = $decodedData["group"];
        $password = password_hash($decodedData["password"], PASSWORD_DEFAULT);
        $sql = "INSERT INTO `Users`(`Login`, `Password`, `Group_id`, `Name`, `Surname`, `Patronymic`) 
        VALUES (:login, :password, :group_id, :name, :surname, :patronymic)";
        
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "login" => $login,
            "password" => $password,
            "group_id" => $group,
            "name" => $name,
            "surname" => $surname,
            "patronymic" => $patronymic
        ]);

        echo json_encode(["success" => true, "message" => "Вы успешно зарегистрировались. Подождите пока преподователь потвердит вашу регистрацию", "title" => "Успешная регистрация"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage(), "errors" => $errors,  "title" => "Произошла ошибка"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Неправильно заполнены поля формы", "errors" => $errors,  "title" => "Произошла ошибка"]);
}