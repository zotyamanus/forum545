<?php
$filename = "data.json";
if (file_exists($filename)) {
    $jsonString = file_get_contents($filename);
    $topics = json_decode($jsonString);
} else {
    $topics = [];
}

if (isset($_POST['action'])) {
    $lastId = 0;
    if (!empty($topics)) {
        $lastItem = end($topics);
        $lastId = $lastItem->id;
    }
    $newId = $lastId + 1;

    if ($_POST['action'] == 'add') {
        array_push($topics, (object)[
            "id" => $newId,
            "name" => $_POST['topic'],
            "date" => date("Y-m-d H:i:s")
        ]);
        $jsonString = json_encode($topics, JSON_PRETTY_PRINT);
        file_put_contents($filename, $jsonString);
    } elseif ($_POST['action'] == 'delete') {
        $idToDelete = $_POST['id'];
        $topics = array_filter($topics, function ($topic) use ($idToDelete) {
            return $topic->id != $idToDelete;
        });
        $topics = array_values($topics);
        $jsonString = json_encode($topics, JSON_PRETTY_PRINT);
        file_put_contents($filename, $jsonString);
    } elseif ($_POST['action'] == 'comment') {
        $topicId = $_POST['topic_id'];
        $commentFile = "comments_{$topicId}.json";

        if (file_exists($commentFile)) {
            $comments = json_decode(file_get_contents($commentFile));
        } else {
            $comments = [];
        }

        $lastId = 0;
        if (!empty($comments)) {
            $lastItem = end($comments);
            $lastId = $lastItem->id;
        }
        $newId = $lastId + 1;

        $newComment = (object)[
            "id" => $newId,
            "topic_id" => $topicId,
            "author" => $_POST['author'],
            "message" => $_POST['message'],
            "date" => date("Y-m-d H:i:s")
        ];

        $comments[] = $newComment;
        file_put_contents($commentFile, json_encode($comments, JSON_PRETTY_PRINT));
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Fórum</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@700&family=Crimson+Text&display=swap');

    :root {
        --text-color: #3a2b1d;
        --primary: #6e1e1e;
        --gold: #d4af37;
        --blue: #1a3a5f;
        --border-color: #b49163;
        --overlay-bg: rgba(255, 248, 224, 0.92);
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Crimson Text', serif;
        color: var(--text-color);

        /* Perzsa szőnyeg mintás háttér */
        background-image: url('images/persian_texture.jpg'); /* ide írd be a mintád elérési útját */
        background-repeat: repeat;
        background-size: auto; /* ne nyújtsa, ismétlődjön */
        background-position: 0 0;
        background-attachment: fixed;
    }

    .container {
        background-color: var(--overlay-bg); 
        border: 8px double var(--gold);
        border-radius: 18px;
        box-shadow: 0 0 30px rgba(0,0,0,0.25);
        padding: 40px;
        max-width: 960px;
        margin: 50px auto;
        position: relative;
    }

    .container::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 100px;
        height: 100px;
        background-image: url('https://upload.wikimedia.org/wikipedia/commons/6/6f/Arabesque_pattern.png');
        background-size: contain;
        opacity: 0.2;
        transform: rotate(10deg);
    }

    h1, h2, h3 {
        font-family: 'Scheherazade New', serif;
        font-weight: 700;
        color: var(--primary);
        text-align: center;
        font-size: 2.2em;
        margin-bottom: 25px;
    }
    h1::after, h2::after {
        content: "";
        display: block;
        width: 60px;
        height: 4px;
        background: var(--gold);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li.topic-item {
        background: linear-gradient(135deg, #fbe8d3, #fff5da);
        border: 4px solid var(--border-color);
        margin-bottom: 20px;
        padding: 20px 24px;
        border-radius: 16px;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.15);
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }
    li.topic-item:hover {
        transform: scale(1.01);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        background: linear-gradient(135deg, #ffeccf, #fff8e4);
    }

    .topic-left a {
        color: var(--blue);
        font-weight: bold;
        font-size: 1.3em;
        text-decoration: none;
    }
    .topic-left a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .topic-date {
        font-size: 0.85em;
        color: #7f6b5c;
    }

    .delete-form input[type="submit"] {
        background-color: var(--primary);
        border: 2px solid var(--gold);
        color: white;
        padding: 8px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.9em;
        float: right;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }
    .delete-form input[type="submit"]:hover {
        background-color: var(--gold);
        color: var(--primary);
    }

    form {
        margin-top: 25px;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 16px;
        border: 2px solid var(--gold);
        border-radius: 10px;
        background-color: #fffef9;
        font-size: 1em;
        font-family: 'Crimson Text', serif;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.08);
    }
    textarea {
        resize: vertical;
    }

    input[type="submit"] {
        background-color: var(--blue);
        color: white;
        padding: 12px 22px;
        border: none;
        border-radius: 10px;
        font-size: 1em;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }
    input[type="submit"]:hover {
        background-color: var(--gold);
        color: var(--blue);
    }

    .comment {
        background-color: #fff9e6;
        border-left: 6px double var(--primary);
        padding: 16px 20px;
        margin-bottom: 20px;
        border-radius: 12px;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
    }
    .comment strong {
        color: var(--primary);
        font-size: 1.1em;
        display: block;
        margin-bottom: 6px;
    }

    .back-link {
        display: inline-block;
        margin-top: 30px;
        color: var(--primary);
        text-decoration: none;
        font-weight: bold;
        font-style: italic;
        font-size: 1em;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: var(--blue);
    }

    @media (max-width: 600px) {
        .delete-form input[type="submit"] {
            float: none;
            margin-top: 10px;
        }
    }
    </style>
</head>
<body>
<div class="container">
<?php
if (!isset($_GET['topic'])) {
    echo "<h1>Fórum témák</h1><ul>";
    foreach ($topics as $value) {
        echo "<li class='topic-item'>
        <div class='topic-left'>
            <a href='?topic=" . $value->id . "'>" . htmlspecialchars($value->name) . "</a>
            <span class='topic-date'>(" . $value->date . ")</span>
        </div>
        <form method='post' class='delete-form'>
            <input type='hidden' name='id' value='" . $value->id . "'>
            <input type='hidden' name='action' value='delete'>
            <input type='submit' value='🗑️' title='Törlés'>
        </form>
      </li>";
    }
    echo "</ul>";
    echo '<h3>Új téma hozzáadása:</h3>
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="topic" required placeholder="Új téma neve">
            <input type="submit" value="Hozzáadás">
          </form>';
} else {
    $selectedId = $_GET['topic'];
    $selectedTopic = null;
    foreach ($topics as $topic) {
        if ($topic->id == $selectedId) {
            $selectedTopic = $topic;
            break;
        }
    }

    if ($selectedTopic) {
        echo "<h1>" . htmlspecialchars($selectedTopic->name) . "</h1>";

        $commentFile = "comments_{$selectedTopic->id}.json";
        if (file_exists($commentFile)) {
            $comments = json_decode(file_get_contents($commentFile));
        } else {
            $comments = [];
        }

        echo "<h2>Hozzászólások:</h2>";
        if (empty($comments)) {
            echo "<p>Nincsenek hozzászólások.</p>";
        } else {
            foreach ($comments as $comment) {
                echo "<div class='comment'>
                        <strong>" . htmlspecialchars($comment->author) . "</strong> (" . $comment->date . ")<br>
                        " . nl2br(htmlspecialchars($comment->message)) . "
                      </div>";
            }
        }
        

        echo '<h3>Új hozzászólás:</h3>
              <form method="POST">
                  <input type="hidden" name="action" value="comment">
                  <input type="hidden" name="topic_id" value="' . $selectedTopic->id . '">
                  <input type="text" name="author" placeholder="Neved" required>
                  <textarea name="message" placeholder="Írd ide a hozzászólásodat..." rows="4" required></textarea>
                  <input type="submit" value="Hozzászólás küldése">
              </form>';
    } else {
        echo "<p>A megadott ID-hoz nem található téma.</p>";
    }

    echo '<p><a class="back-link" href="index.php">Vissza a főoldalra</a></p>';
}
?>
</div>
</body>
</html>
