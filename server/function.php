<?php
require_once("../vendor/autoload.php"); 
require_once("connect.php");

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
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

function validateIdSection($id, $arrayId) {
    if(!in_array($id, $arrayId)) {
        return "Некорректная дисциплина";
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