<?php
require_once("../function.php");
header('Content-Type: application/json; charset=utf-8');

$data = filter_input(INPUT_POST, "data", FILTER_DEFAULT);
$decodedData = json_decode($data, true);

$login = $decodedData["login"];
$password = $decodedData["password"];

try {
    $sql = "SELECT * FROM Users WHERE Login = ? AND Status = 1";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$login]);
    $rowCount = $stmt -> rowCount();
}catch(Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage(), "errors" => $errors,  "title" => "Произошла ошибка"]);
}

if($rowCount > 0) {
    $user = $stmt -> fetch(PDO::FETCH_ASSOC);
    if(password_verify($password, $user["Password"])) {
        $_SESSION["user"] = $user;
        echo json_encode(["success" => true, "message" => "Вы успешно авторизовались", "title" => "Успешно"]);
    }
    else {
        echo json_encode(["success" => false, "message" => "Неправильный логин или пароль",  "title" => "Произошла ошибка"]);
    }
}
else {
    echo json_encode(["success" => false, "message" => "Неправильный логин или пароль. Или же ваша регистрация не подтверждена",  "title" => "Произошла ошибка"]);
}