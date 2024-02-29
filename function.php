<?php
require_once("vendor/autoload.php"); 
require_once("server/connect.php");

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$path = "../templates";
if (!file_exists($path)) {
    $path = "templates";
}

$loader = new FilesystemLoader("$path");
$twig = new Environment($loader);

function validateName($value, $link, $refer) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    if($refer == "discipline") {
        $sql = "SELECT * FROM `Disciplines` WHERE `Name` = ?";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([$value]);
        $rowCount = $stmt -> rowCount();
    
        if($rowCount) {
            return "Дисциплина с таким названием существует";
        }
    }

    return null;
}

function validateNameDiscipline($value, $link) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    $sql = "SELECT * FROM `Disciplines` WHERE `Name` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$value]);
    $rowCount = $stmt -> rowCount();
    
    if($rowCount) {
        return "Дисциплина с таким названием существует";
    }

    return null;
}

function validateNameSection($value) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    return null;
}

function validateIdDiscipline($id, $arrayId) {
    if(!in_array($id, $arrayId)) {
        return "Некорректная дисциплина";
    }

    return null;
}

function validateNameTests($value, $link) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    $sql = "SELECT * FROM `Tests` WHERE `Name` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$value]);
    $rowCount = $stmt -> rowCount();

    if($rowCount) {
        return "Тест с таким названием существует";
    }

    return null;
}

function validateNameGroup($value, $link) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    $sql = "SELECT * FROM `Groups` WHERE `Name` = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$value]);
    $rowCount = $stmt -> rowCount();

    if($rowCount) {
        return "Группа с таким названием существует";
    }

    return null;
}

function getDisciplines($link) {
    $sql = "SELECT * FROM `Disciplines`";
    return $link->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getGroup($link) {
    $sql = "SELECT * FROM `Groups`";
    return $link->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getTests($link) {
    $sql = "SELECT * FROM `Tests`";
    return $link->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function validateUser($value) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    return null;
}

function validateGroup($value, $array) {
    if(!in_array($value, $array)) {
        return "Некорректная группа";
    }

    return null;
}

function validatePassword($value) {
    $length = strlen($value);
    if($length < 10) {
        return "Длина пароля не менее 10 символов";
    }

    return null;
}

function validateLogin($value, $link) {
    $length = strlen($value);
    if($length == 0) {
        return "Поле должно быть заполнено";
    }

    $sql = "SELECT * FROM `Users` WHERE Login = ?";
    $stmt = $link -> prepare($sql);
    $stmt -> execute([$value]);
    $count = $stmt -> rowCount();

    if($count > 0) {
        return "Логин занят";
    }

    return null;
}

function getQuestions($link) {
    $sql = "SELECT * FROM `Questions`";
    return $link->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function validateTests($id, $arrayId) {
    if(!in_array($id, $arrayId)) {
        return false;
    }

    return true;
}