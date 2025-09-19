<?php
$filename = "data.json";
if(file_exists($filename)){
    $jsonString = file_get_contents($filename);
    $topics = json_decode($jsonString);
}else{
    $topics=[];
}


$szoveg ="";
if(isset($_POST["topic"])){
    $topics=[];
    array_push($topics, $_POST["topic"]);
    $jsonString= json_encode($topics);
    $szoveg = $jsonString;
    file_put_contents($filename, $jsonString);
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forum</title>
</head>
<body>

<?php
echo $szoveg
?>
    <h1>Témák:</h1>
    <form method=POST>
        <input type="text" name="topic">
        <input type="submit">
        

    </form>
</body>
</html>