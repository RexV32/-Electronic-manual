<?php
require_once ("function.php");
if (!isset ($_SESSION["user"])) {
    header("Location: auth.php");
}

$role = $_SESSION["user"]["Role_id"];
$title = "ЭМКУ";
$template = "";
$context = [];
$array = [];
$data = [];
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$sql = "SELECT Disciplines.Id as IdDisciplines,
    Disciplines.Name as NameDiscipline,
    GROUP_CONCAT(Sections.Name) as SectionName,
    GROUP_CONCAT(Sections.Id) as SectionId FROM `Disciplines` 
    INNER JOIN Sections ON Disciplines.Id = Sections.Id_discipline WHERE Disciplines.Status = 1 AND Sections.Status = 1 GROUP BY Disciplines.Id";
$stmt = $link->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $item) {
    $idArray = explode(",", $item["SectionId"]);
    $nameArray = explode(",", $item["SectionName"]);
    unset($item["SectionId"]);
    unset($item["SectionName"]);
    $item["Section"] = [];
    for ($i = 0; $i < count($idArray); $i++) {
        $id = $idArray[$i];
        $name = $nameArray[$i];
        array_push($item["Section"], ["Id" => $id, "Name" => $name]);
    }
    array_push($array, $item);
}
$data = $array;

if (!isset ($_GET["section"]) && !isset ($_GET["tests"])) {
    $template = 'index.twig';
    $context = [
        "title" => $title,
        "role" => $role,
        "data" => $data,
        "template" => $template
    ];
} else {
    if (isset ($_GET["section"])) {
        $id = $_GET["section"];
        $sql = "SELECT SubSections.Id as IdSubSections,
        SubSections.Name as NameSubSections,
        Sections.Name as NameSection,
        Disciplines.Name as NameDiscipline,
        SubSections.Id_section as currentIdSection
        FROM `SubSections` INNER JOIN Sections ON Sections.Id = SubSections.Id_section 
        INNER JOIN Disciplines ON Disciplines.Id = Sections.Id_discipline WHERE `SubSections`.`Id_section` = ? AND SubSections.Status = 1";
        $stmt = $link->prepare($sql);
        $stmt->execute([$id]);
        $subSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $disciplineName = $subSections[0]["NameDiscipline"];
        $sectionName = $subSections[0]["NameSection"];
        $currentIdSection = $subSections[0]["currentIdSection"];
        $template = 'subSection.twig';

        $subSectionsSlice = array_slice($subSections, $offset, $limit, true);
        $subSectionsQuantity = count($subSections);
        $pages = ceil($subSectionsQuantity / $limit);
        $context = [
            "title" => $title,
            "role" => $role,
            "subSections" => $subSections,
            "data" => $data,
            "template" => $template,
            "disciplineName" => $disciplineName,
            "sectionName" => $sectionName,
            "pages" => $pages,
            "page" => $page,
            "limit" => $limit,
            "subSectionsSlice" => $subSectionsSlice,
            "currentIdSection" => $currentIdSection
        ];
    } else if (isset ($_GET["tests"])) {
        $idUser = $_SESSION["user"]["Id"];
        $sql = "SELECT * FROM `Tests` WHERE Status = 1";
        $stmt = $link->prepare($sql);
        $stmt->execute();
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT * FROM `Results` WHERE `Id_User` = ?";
        $stmt = $link->prepare($sql);
        $stmt->execute([$idUser]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $template = 'testList-user.twig';

        $testsSlice = array_slice($tests, $offset, $limit, true);
        $testsQuantity = count($tests);
        $pages = ceil($testsQuantity / $limit);
        $context = [
            "title" => $title,
            "role" => $role,
            "data" => $data,
            "tests" => $tests,
            "results" => $results,
            "template" => $template,
            "pages" => $pages,
            "page" => $page,
            "limit" => $limit,
            "testsSlice" => $testsSlice
        ];
    } else {
        header("Location: index.php");
    }
}

$content = $twig->render($template, $context);
print ($content);
