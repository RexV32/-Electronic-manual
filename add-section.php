<?php
require_once("server/function.php");
$title = "ЭМКУ - Добавить раздел";
$currentSection = "addSection";
$errors = [];

$sql = "SELECT * FROM `Disciplines`";
$stmt = $link ->query($sql);
$disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = filter_input_array(INPUT_POST, ["name" => FILTER_DEFAULT, "discipline" => FILTER_DEFAULT]);
    $id = $data["discipline"];
    $name = $data["name"];
    $disciplinesIdArray = array_column($disciplines, "Id");
    $errors["discipline"] = validateIdSection($id, $disciplinesIdArray);
    $errors["name"] = validateNameSection($name);
    $errors = array_filter($errors);

    if(!count($errors)) {
        $sql = "INSERT INTO `Sections`(Name,Id_discipline) VALUES(:name,:id)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([
            "name" => $name,
            "id" => $id
        ]);
        $lastInsertId = $link -> lastInsertId();
        $nameFolder = $lastInsertId;
        $path = "uploads/$id/$nameFolder";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        header("Location:sections.php");
    }
}

$content = $twig -> render('add-section.twig', 
    [
        "title" => $title, 
        "errors" => $errors, 
        "currentSection" => $currentSection,
        "disciplines" => $disciplines
    ]);
print($content);
?>