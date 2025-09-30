<?php
$fileName = 'data.json';

// Fájl beolvasása
if (file_exists($fileName)) {
    $jsonString = file_get_contents($fileName);
    $topics = json_decode($jsonString);

    // Ha hibás vagy üres a JSON, legyen üres tömb
    if (!is_array($topics)) {
        $topics = [];
    }
} else {
    $topics = [];
}

// POST feldolgozása
if (isset($_POST['action'])) {
    $lastID = 0;

    if (!empty($topics)) {
        $lastitem = end($topics);
        $lastID = $lastitem->id;
    }

    $newid = $lastID + 1;

    // Új téma hozzáadása
    if ($_POST['action'] == 'add') {
        array_push($topics, (object)[
            "id" => $newid,
            "name" => $_POST['topic'],
            "created_at" => date('Y-m-d H:i:s') // Hozzáadás időpontja
        ]);

        $JsonString = json_encode($topics, JSON_PRETTY_PRINT);
        file_put_contents($fileName, $JsonString);
    }

    // Téma törlése
    elseif ($_POST['action'] == 'delete') {
        $deleteID = $_POST['id'];

        foreach ($topics as $key => $value) {
            if ($value->id == $deleteID) {
                unset($topics[$key]);
                break;
            }
        }

        $JsonString = json_encode(array_values($topics), JSON_PRETTY_PRINT);
        file_put_contents($fileName, $JsonString);
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fórum</title>
</head>
<body>
    <h1>Témák:</h1>
    <ol>
        <?php
        foreach ($topics as $value) {
            echo '<li>' . htmlspecialchars($value->name);

            // Ha van létrehozási dátum, írjuk ki
            if (isset($value->created_at)) {
                echo ' <small>(Hozzáadva: ' . htmlspecialchars($value->created_at) . ')</small>';
            }

            echo '
            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="' . $value->id . '">
                <input type="submit" name="action" value="delete">
            </form>
            </li>';
        }
        ?>
    </ol>

    <h2>Új téma hozzáadása:</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="topic" required>
        <input type="submit" value="Add">
    </form>
</body>
</html>
