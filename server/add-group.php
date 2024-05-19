<?
require_once ("../function.php");
header('Content-Type: application/json; charset=utf-8');

$errors = [];

try {
    $name = trim(filter_input(INPUT_POST, "name", FILTER_DEFAULT));
    $errors["name"] = validateNameGroup($name, $link);
    $errors = array_filter($errors);

    if (!count($errors)) {
        $sql = "INSERT INTO `Groups` (Name) VALUES(?)";
        $stmt = $link -> prepare($sql);
        $stmt -> execute([$name]);

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $errors["name"]]);
    }

} catch (Exception $error) {
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}