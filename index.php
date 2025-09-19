<?php
$szoveg ="";
if(isset($_POST["topic"])){
    $szoveg = "kaptam egy új topic post adatot";
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