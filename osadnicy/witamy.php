<?php
    session_start();
    if(isset($_SESSION['udanarejestracja']))
    {
        header('Location: index.php');
        exit();
    }
    else
    {
        unset($_SESSION['udanarejestracja']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osadnicy - gra przeglądarkowa</title>
</head>
<body>
    Dziękujemy za rejestrację w serwisie, można się zalogować.<br/><br/>
    <a href="index.php"> Zaloguj się
    </a> <br/> <br/>
</body>
</html>