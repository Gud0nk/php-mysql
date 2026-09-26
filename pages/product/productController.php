<?php
// index - показать список всех записей
function index($conn)
{
    echo' <a href="index.php?catalog=product&action=create">Добавить товар</a>';

    $sql = "SELECT * FROM products";
    $products = mysqli_query($conn, $sql);
    if (mysqli_num_rows($products) > 0) {
        $categories = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categories"), MYSQLI_ASSOC);
        while ($row = mysqli_fetch_assoc($products)) {
            echo "<p>Наименование: " . $row['name'] . "</p>";
            echo "<p>Цена: " . $row['price'] . "</p>";
            echo "<p>Описание: " . $row['description'] . "</p>";
            foreach ($categories as $category) {
                if ($category['id'] == $row['category_id']) {
                    echo "<p>Название категории: " . $category['name'] . "</p>";
                }

            }
            if (!is_null($row["path"])) {
                echo "<img width='150' alt='".$row['name']."' src='".$row["path"]."'>";
            }
            echo'<a href="index.php?catalog=product&action=show&id='.$row["id"].'"><img alt="Просмотреть" src="images/show.jpg" width="30"></a>';
            echo'<a href="index.php?catalog=product&action=edit&id='.$row["id"].'"><img alt="Редактировать" src="images/edit.png" width="30"></a>';
            echo'<a href="index.php?catalog=product&action=destroy&id='.$row["id"].'"><img alt="Удалить" src="images/delete.png" width="30"></a>';
            echo "<br><br>";
        }
    } else {
        echo "<p>Товары не найдены</p><br>";
    }
}

// create - показать форму для создания новой записи
function create($conn)
{
    echo' <a href="index.php?catalog=product&action=index">Назад</a>';
    $categories = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categories"), MYSQLI_ASSOC);

    echo '<form action="index.php?catalog=product&action=store" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Наименование товара"><br>
            <input type="text" name="description" placeholder="Описание товара"><br>
            <input type="number" name="price" min="0.01" step="0.01"  placeholder="Цена"><br>
            <input type="file" name="path"><br>
            <select name="category_id">';
    foreach ($categories as $category) {
        echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
    }
    echo '</select><br>
            <button type="submit">Добавить товар</button>
            </form>';
}

// store - сохранить новую запись в базу данных
function store($conn)
{
    $name = $_POST["name"] ?? null;
    $description = $_POST["description"] ?? null;
    $price = $_POST["price"] ?? null;
    $category_id = $_POST["category_id"] ?? null;
    if (($name) && ($description) && ($price) && ($category_id)) {
        $path = null;
        if ($_FILES && $_FILES["path"]["error"] == UPLOAD_ERR_OK) {
            // $_FILES["имя_поля"]["name"] - имя файла
            // $_FILES["имя_поля"]["type"] - тип содержимого файла, например, image/jpeg
            // $_FILES["имя_поля"]["size"] - размер файла в байтах
            // $_FILES["имя_поля"]["tmp_name"] - имя временного файла, сохраненного на сервере
            // $_FILES["имя_поля"]["error"] - код ошибки при загрузке
            // Получение расширение файла
            $extension = pathinfo($_FILES["path"]["name"], PATHINFO_EXTENSION);
            // Перемещение временного файла по новому пути
            $path = "uploads/" . hash('sha256', $_FILES["path"]["name"] . " - " . microtime() ) . "." . $extension;
            move_uploaded_file($_FILES["path"]["tmp_name"], $path);
        }


        $sql = "INSERT INTO products (name, description, price, path, category_id) values ('$name', '$description', '$price', '$path' ,'$category_id')";
        mysqli_query($conn, $sql);
        // Перенаправление
        echo '<p>Товар добавлен!</p>';
        echo '<a href="index.php?catalog=product&action=index">Назад</a>';
    } else {
        echo '<a href="index.php?catalog=product&action=create">Назад</a>';
        if (!($name)){ echo '<p>Наименование товара обязательно для заполнения</p>';}
        if (!($description)){ echo '<p>Описание товара обязательно для заполнения</p>';}
        if (!($price)){ echo '<p>Цена товара обязательно для заполнения</p>';}
        if (!($category_id)){ echo '<p>Категория товара обязательно для заполнения</p>';}
    }

}

// show - показать одну конкретную запись
function show($conn, $id)
{

    echo '<a href="index.php?catalog=product&action=index">Назад</a>';
    if ($id != null) {
        $sql = "SELECT * FROM products WHERE id = '$id'";
        $product = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }
    if (!isset($product)) {
        echo "Товар не найден";
    } else {
        echo "<p>Наименование: " . $product['name'] . "</p>";
        echo "<p>Цена: " . $product['price'] . "</p>";
        echo "<p>Описание: " . $product['description'] . "</p>";
        $categories = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categories"), MYSQLI_ASSOC);
        foreach ($categories as $category) {
            if ($category['id'] == $product['category_id']) {
                echo "<p>Название категории: " . $category['name'] . "</p>";
            }
        }
    }
}
// edit - показать форму для изменения записи
function edit($conn, $id)
{
    echo '<a href="index.php?catalog=product&action=index">Назад</a>';
    if ($id != null) {
        $sql = "SELECT * FROM products WHERE id = '$id'";
        $product = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }
    if (!isset($product)) {
        echo "Товар не найден";
    } else {
        $categories = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categories"), MYSQLI_ASSOC);

        echo '<form action="index.php?catalog=product&action=update&id='.$id.'" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Наименование товара" value="' . $product["name"] . '"><br>
            <input type="text" name="description" placeholder="Описание товара" value="'.$product["description"].'"><br>
            <input type="number" name="price" min="0.01" step="0.01"  placeholder="Цена" value="'. $product["price"] .'"><br>
            <select name="category_id">';
        foreach ($categories as $category) {
            echo '<option value="' . $category['id'] . '" ' . (($category['id']==$product['category_id']) ? "selected" : null ). '>' . $category['name'] . '</option>';
        }
        echo '</select><br>
            <button type="submit">Редактировать товар</button>
            </form>';
    }
}

// update - обновит запись в базе данных
function update($conn, $id)
{
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'"));
    if ($product) {
        mysqli_query($conn, "UPDATE products SET category_id=" . $_POST['category_id'] . ", name ='" . $_POST['name'] .
            "', description ='" . $_POST['description'] . "', price =" . $_POST['price'] . " WHERE id = '$id' ");
    }
    if ($_FILES && $_FILES["path"]["error"] == UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES["path"]["name"], PATHINFO_EXTENSION);
        $path = "uploads/" . hash('sha256', $_FILES["path"]["name"] . " - " . microtime() ) . "." . $extension;
        move_uploaded_file($_FILES["path"]["tmp_name"], $path);
        if (file_exists($product["path"])) {
            unlink($product["path"]);
        }
        mysqli_query($conn, "UPDATE products SET path ='$path' WHERE id = '$id' ");
    }




    echo "Данные обновлены";
    echo '<a href="index.php?catalog=product&action=index">Назад</a>';
}
// destroy - удалить запись
function destroy($conn, $id){
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'"));
    if ($product) {
        if (file_exists($product['path'])){
            unlink($product['path']);
        }
        mysqli_query($conn, "DELETE FROM products WHERE id = '$id'");
        echo '<a href="index.php?catalog=product&action=index">Назад</a>';
        echo "Товар успешно удален";
    } else {
        echo '<a href="index.php?catalog=product&action=index">Назад</a>';
        echo "Товар не найден!";
    }
}