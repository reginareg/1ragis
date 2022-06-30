<?php

$host = '127.0.0.1';
$db   = '1ragis';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);


?>
   <fieldset>
    <legend>CREATE</legend>
    <form method="POST">
        Title: <input tipe="text" name="title">
        Height: <input tipe="text" name="height">
        tipe: <select name="tipe">
            <option value="1">Lapas</option>
            <option value="2">Spyglius</option>
            <option value="3">Palmė</option>
        </select>
        <input tipe="hidden" name="_method" value="post">
        <button tipe="submit">create</button>
    </form>
</fieldset>

<fieldset>
    <legend>DELETE</legend>
    <form method="POST">
        ID: <input tipe="text" name="id">
        <input tipe="hidden" name="_method" value="delete">
        <button tipe="submit">delete</button>
    </form>
</fieldset>

<fieldset>
    <legend>UPDATE</legend>
    <form method="POST">
        ID: <input tipe="text" name="id">
        Title: <input tipe="text" name="title">
        <input tipe="hidden" name="_method" value="put">
        <button tipe="submit">update</button>
    </form>
</fieldset>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // CREATE
    // INSERT INTO table_name (column1, column2, column3, ...)
    // VALUES (value1, value2, value3, ...);
    if ($_POST['_method'] == 'post') {
        $sql = "
        INSERT INTO trees
        (title, height, tipe)
        VALUES (:a, :z, :tipe)
        ";
        $stmt = $pdo->prepare($sql);
        // $stmt->execute([$_POST['title'], $_POST['height'], $_POST['tipe']]);
        $stmt->execute([
            'z' => $_POST['height'],
           'tipe' => $_POST['tipe'],
            'a' => $_POST['title']
        ]);
        header('Location: http://localhost/1ragis/');
        die;
    }


    if ($_POST['_method'] == 'delete') {
        // DELETE
        // DELETE FROM table_name WHERE condition;
        $sql = "
            DELETE FROM trees
            WHERE id = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['id']]);

        header('Location: http://localhost/1ragis/');
        die;
    }

    if ($_POST['_method'] == 'put') {
        // UPDATE
        // UPDATE table_name
        // SET column1 = value1, column2 = value2, ...
        // WHERE condition;
        $sql = "
            UPDATE trees
            SET title = ?
            WHERE id = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['title'], $_POST['id']]);

        header('Location: http://localhost/1ragis/');
        die;
    }
}


// READ
// SELECT column1, column2, ...
// FROM table_name;

$sql = "
    SELECT id, title, height, tipe
    FROM trees
    ORDER BY tipe, height DESC
    
";
$stmt = $pdo->query($sql);

$trees = $stmt->fetchAll();

echo '<ul>';
foreach($trees as $tree) {
    echo '<li>'. $tree['id'] . ' ' . $tree['title'] . ' ' . $tree['height'] . ' ' . ['Lapuotis', 'Sygliuotis', 'Palme'][$tree['tipe'] - 1] . '</li>';
}
echo '</ul>';


$sql = "
    SELECT tipe, sum(height) AS height_sum, count(id) as trees_count, GROUP_CONCAT(title, '^O-O^') AS titles
    FROM trees
    GROUP BY tipe
    
";
$stmt = $pdo->query($sql);

$trees = $stmt->fetchAll();

echo '<ul>';
foreach($trees as $tree) {
    echo '<li>'. $tree['height_sum'] . ' ' . $tree['trees_count'] . ' ' .$tree['titles'] . ' ' .['Lapuotis', 'Sygliuotis', 'Palme'][$tree['tipe'] - 1] . '</li>';
}
echo '</ul>';
