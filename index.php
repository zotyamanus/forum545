<?php
$fileName = 'data.json';
if (file_exists($fileName)) {
    $jsonString = file_get_contents($fileName);
    $topics = json_decode($jsonString);
} else {
    $topics = [];
}

if (isset($_POST['action'])) {
    # Új téma hozzáadása
    if ($_POST['action'] == 'add') {
        # Az utolsó téma ID meghatározása
        $lastId = 0;
        if (!empty($topics)) {
            $lastItem = end($topics);
            $lastId = $lastItem->id;
        }
        array_push($topics,
            (object)[
                "id" => $lastId + 1,
                "name" => $_POST['topic']
            ]
        );
        $JsonString = json_encode($topics, JSON_PRETTY_PRINT);
        file_put_contents($fileName, $JsonString);
    }
    # Téma törlése
    elseif (($_POST['action'] == 'delete')) {
        $id = $_POST['id'];
        foreach ($topics as $key => $topic) {
            if ($topic->id == $id) break;
        }
        array_splice($topics, $key, 1);
        $JsonString = json_encode($topics, JSON_PRETTY_PRINT);
        file_put_contents($fileName, $JsonString);
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Forum</title>

    <style>
        /* Reset egy kicsit */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 0 15px 40px;
            color: #333;
            min-height: 100vh;
        }

        h1, h2 {
            color: #2c3e50;
            margin-bottom: 0.5em;
        }

        a {
            color: #2980b9;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        a:hover {
            color: #3498db;
            text-decoration: underline;
        }

        ol {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
            max-width: 600px;
            margin: 1em auto 2em;
        }

        ol li {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 1.1rem;
        }

        ol li:last-child {
            border-bottom: none;
        }

        ol li form {
            margin: 0;
        }

        ol li form input[type="submit"] {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        ol li form input[type="submit"]:hover {
            background: #c0392b;
        }

        input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            width: 220px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus {
            border-color: #2980b9;
            outline: none;
        }

        form {
            max-width: 600px;
            margin: 0 auto 2em;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        form input[type="submit"] {
            background: #2980b9;
            color: white;
            border: none;
            padding: 9px 20px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form input[type="submit"]:hover {
            background: #3498db;
        }

        /* GET form külön stílusa */
        .get-form {
            max-width: 600px;
            margin: 2em auto;
            padding: 20px 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
            text-align: center;
        }

        .get-form input[type="text"] {
            width: 60%;
        }

        .get-form input[type="submit"] {
            padding: 8px 16px;
        }

        /* Mobil optimalizálás */
        @media (max-width: 640px) {
            form {
                flex-direction: column;
            }
            input[type="text"] {
                width: 100%;
            }
            .get-form input[type="text"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php
if (!isset($_GET['topic'])) {
    echo '<h1>Témák:</h1><ol>';
    foreach ($topics as $value) {
        echo '<li>
            <a href="index.php?topic=' . htmlspecialchars($value->id) . '">' . htmlspecialchars($value->name) . '</a>
            <form method="post" onsubmit="return confirm(\'Biztos törölni akarod ezt a témát?\')">
                <input type="hidden" name="id" value="' . htmlspecialchars($value->id) . '" />
                <input type="hidden" name="action" value="delete" />
                <input type="submit" value="Törlés" />
            </form>
        </li>';
    }
    echo '</ol>';
} else {
    echo '<h1>Részletek a témáról (ID: ' . htmlspecialchars($_GET['topic']) . ')</h1>';
    echo '<p>Itt készítheted el a téma részletes nézetét, kommentekkel, stb.</p>';
    echo '<p><a href="index.php">Vissza a témákhoz</a></p>';
}
?>

<form method="POST">
    <input type="hidden" name="action" value="add" />
    <input type="text" name="topic" placeholder="Új téma neve..." required />
    <input type="submit" value="Hozzáadás" />
</form>

<div class="get-form">
    <h2>GET Form</h2>
    <?php
    if (isset($_GET['valami'])) {
        echo '<p>Az utolsó kapott érték: <strong>' . htmlspecialchars($_GET['valami']) . '</strong></p>';
    }
    ?>
    <form method="GET">
        <input type="text" name="valami" placeholder="Írj valamit..." />
        <input type="submit" value="Küld" />
    </form>
</div>

</body>
</html>
