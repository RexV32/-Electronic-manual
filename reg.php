<?php
require_once("function.php");
$title = "ЭМКУ - Регистрация";
$errors = [];

$sql = "SELECT * FROM `Groups`";
$stmt = $link->query($sql);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* $name = isset($_POST["name"]) ? $_POST["name"] : "";
$surname = isset($_POST["surname"]) ? $_POST["surname"] : "";
$patronymic = isset($_POST["patronymic"]) ? $_POST["patronymic"] : "";
$login = isset($_POST["login"]) ? $_POST["login"] : ""; */


/* if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupsIdArray = array_column($groups, "Id");

    $data = array_map(
        "trim",
        filter_input_array(
            INPUT_POST,
            [
                "name" => FILTER_DEFAULT,
                "surname" => FILTER_DEFAULT,
                "patronymic" => FILTER_DEFAULT,
                "login" => FILTER_DEFAULT,
                "group" => FILTER_DEFAULT,
                "password" => FILTER_DEFAULT,
                "confirm" => FILTER_DEFAULT
            ]
        )
    );

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
        "login" => function ($value) {
            return validateUser($value);
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
    foreach ($data as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
    if ($data["password"] !== $data["confirm"]) {
        $errors["confirm"] = "Пароли не совпадают";
        $errors["password"] = "Пароли не совпадают";
    }
    $errors = array_filter($errors);
    if (!count($errors)) {
        $name = $data["name"];
        $surname = $data["surname"];
        $patronymic = $data["patronymic"];
        $login = $data["login"];
        $group = $data["group"];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `Users`(`Login`, `Password`, `Group_id`, `Name`, `Surname`, `Patronymic`) 
        VALUES (:login,:password,:group_id,:name,:surname,:patronymic)";
        $stmt = $link->prepare($sql);
        $stmt->execute([
            "login" => $login,
            "password" => $password,
            "group_id" => $group,
            "name" => $name,
            "surname" => $surname,
            "patronymic" => $patronymic
        ]);

        header("Location:auth.php");
    }
} */

$content = $twig->render('reg.twig', [
    "title" => $title,
    "groups" => $groups,
    "errors" => $errors
]);
print($content);