<?php
    $fileName = 'data.json';
    if (file_exists($fileName)) {
        $jsonString = file_get_contents($fileName);
        $topics = json_decode($jsonString);
    } else {
        $topics = [];
    }
    if (isset($_POST['action'])) {
        $lastID = 0;
        if(!empty($topics)){
            $lastitem = end($topics);
            $lastID = $lastitem->id;

        }
        $newid = $lastID + 1;
    if($_POST['action']=='add'){    
        array_push($topics,
        (object)[
            "id"=>"1234",
            "name"=>$_POST['topic']
        ] 
        );
    
    $JsonString = json_encode($topics, JSON_PRETTY_PRINT);
    file_put_contents($fileName,$JsonString);
    
    }
    elseif ($_POST['action'] == 'delete') {
    // el: A törlendő téma ID-ja
    $deleteID = $_POST['id'];  // A törlendő téma ID-ja
    foreach ($topics as $key => $value) {
        if ($value->id == $deleteID) {
            // el: Ha megtaláltuk az ID-t, akkor eltávolítjuk a tömbből
            unset($topics[$key]);
            break;  // el: Leállunk, miután megtaláltuk a törlendő elemet
        }
    }

    // el: Az új topics tömb JSON-ba alakítása és elmentése a fájlba
    $JsonString = json_encode(array_values($topics), JSON_PRETTY_PRINT);  // el: új indexek
    file_put_contents($fileName, $JsonString);
}

    }


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
</head>
<body>
    <h1>Témák:</h1>
    <ol>
    <?php
        foreach ($topics as $value) {
            echo '<li>' . $value->name . '
            <form method="post">
            <input type="hidden" name="id" value="'.$value->id.'">
            <input type="hidden" value="törlés">
            <input type="submit" name="action" value="delete">
            </form>';
        }
    ?>
    </ol>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="topic">
        <input type="submit" value="Add">
    </form>
</body>
</html>