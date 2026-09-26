<?php

function index($conn) {
    echo '<a href="index.php?catalog=category&action=create">Добавить категорию</a>';

    $sql = "SELECT * FROM categories";
    $categories = mysqli_query($conn, $sql);
    if (mysqli_num_rows($categories) > 0) {
        while ($row = mysqli_fetch_assoc($categories)) {
            echo "<p>Наименование: " . $row['name'] . "</p>";
            echo'<a style="padding-right: 10px;" href="index.php?catalog=category&action=show&id='.$row["id"].'">Показать</a>';
            echo'<a style="padding-right: 10px;" href="index.php?catalog=category&action=edit&id='.$row["id"].'">Изменить</a>';
            echo'<a href="index.php?catalog=category&action=destroy&id='.$row["id"].'">Уничтожить</a>';

        }
    } else {
        echo "<p>Категория не найдена</p><br>";
    }


}
function create($conn) {
    echo '<a href="index.php?catalog=category&action=index">Назад</a>';
    echo '<form action="index.php?catalog=category&action=store" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Наименование товара"><br>
            <button type="submit">Добавить товар</button>
            </form>';
}
function store($conn) {
    $name = $_POST["name"] ?? null;
    if ($name) {
        $sql = "INSERT INTO categories (name) VALUES ('$name')";
        mysqli_query($conn, $sql);
    }
    echo '<p>Категория добавлена</p>';
    echo '<a href="index.php?catalog=category&action=index">Назад</a>';
}
function show($conn,$id) {
    echo '<a href="index.php?catalog=category&action=index">Назад</a>';
    if ($id != null) {
        $sql = "SELECT * FROM categories WHERE id = $id";
        $categories = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }
    if (!isset($categories)) {
        echo "Категория не найдена";
    } else {
        echo "<p>Наименование: " . $categories['name'] . "</p>";
    }
}
function edit($conn,$id) {
    echo '<a href="index.php?catalog=category&action=index">Назад</a>';
    if ($id != null) {
        $sql = "SELECT * FROM categories WHERE id = $id";
        $category = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }
    if (!isset($category)) {
        echo "Категория не найдена";
    }
    echo '<form action="index.php?catalog=category&action=update&id='.$id.'" method="post" >
            <input type="text" name="name" placeholder="имя товара переименуй" value="'.$category['name'].'"><br>
            <button type="submit">Редактировать категорию</button>
          </form>';
}
function update($conn, $id) {
    $category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id = '$id'"));
    if ($category) { mysqli_query($conn, "UPDATE categories SET name='". $_POST['name'] . "' WHERE id = '$id' "); }
    echo "Информация обновлена";
    echo '<a href="index.php?catalog=category&action=index"></a>';
}