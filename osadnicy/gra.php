<?php
    session_start();
    if(!isset($_SESSION['zalogowany']))
    {
        header('Location:index.php');
        exit();
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
    <?php
    echo " Witaj ".$_SESSION['user']."!<br>";
    echo '<a href="logout.php">Wyloguj</a></p>';
    echo "<p><b>Drewno</b>:".$_SESSION['drewno'];
    echo "<p><b>Kamien</b>:".$_SESSION['kamien'];
    echo "<p><b>Zboże</b>:".$_SESSION['zboze']; 
    echo "<p><b>E-mail</b>:".$_SESSION['email'];
    echo "<p><b>Data wygaśnięcia premium</b>:".$_SESSION['dnipremium']."</p>";

    $dataczas = new DateTime();

    echo "Data i czas serwera: ".$dataczas->format('Y-m-d H:i:s')."<br>";
    
    $koniec = DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['dnipremium']);

    $roznica = $dataczas->diff($koniec);

    if($dataczas<$koniec)
        echo "Pozostało premium: ".$roznica->format('%m mies, %d dni, %h godz, %i min, %s sek');
    else
        echo "Premium nie aktywne od: ".$roznica->format('%y lat, %m mies, %d dni, %h godz, %i min %s sek');

    ?>
</body>
</html>