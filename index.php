<?php
include_once "config.php";

?>
<style>
    * {
        padding: 0;
        margin: 0;
    }
</style>
<nav>
    <ul>
        <li><a href="index.php?catalog=product&action=index">Товары</a></li>
        <li><a href="index.php?catalog=category&action=index">Категории</a></li>
    </ul>
</nav>

<?php
$catalog = $_GET["catalog"] ?? null;

if ($catalog) {
    $controller = "pages/" . $catalog . "/". $catalog . "Controller.php";
    if (file_exists($controller)) {
        include $controller;
    } else {
        echo "Ошибка 404";
    }

    $action = $_GET["action"] ?? null;
    $id = $_GET["id"] ?? null;
    switch ($action) {
        case "index": index($conn); break;
        case "create": create($conn); break;
        case "store": store($conn); break;
        case "show": show($conn, $id); break;
        case "edit": edit($conn, $id); break;
        case "update": update($conn,$id); break;
        case "destroy": destroy($conn, $id); break;
        default: index(); break;
    }


}

