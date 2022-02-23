<?php
    session_start();

    if(isset($_POST['email']))
    {
        //Udana walidacja
        $wszystko_ok=true;

        //Sprawdzenie nickname'u
        $nick = $_POST['nick'];

        //Sprawdzenie długości nicku
        if(strlen($nick)<3||strlen($nick)>20)
        {
            $wszystko_ok = false;
            $_SESSION['e_nick'] = "Nick musi posiadać od 3 do 20 znaków!";
        }

        if(ctype_alnum($nick)==false)
        {
            $wszystko_ok = false;
            $_SESSION['e_nick']="Tylko litery i cyfry, bez polskich znaków !";
        }

        //Sprawdz poprawnosc adresu email
        $unsafe_email=$_POST['email'];
        $email = filter_var($unsafe_email,FILTER_SANITIZE_EMAIL);
        
        if(filter_var($email,FILTER_VALIDATE_EMAIL) === false || ($unsafe_email!=$email))
        {
            $wszystko_ok = false;
            $_SESSION['e_email'] = "Dodaj poprawny adres e-mail";
        }

        //Sprawdz poprawnosc hasła
        $haslo1 = $_POST['haslo1'];
        $haslo2 = $_POST['haslo2'];

        if(strlen($haslo1)<8 || strlen($haslo1)>20 )
        {
            $wszystko_ok = false;
            $_SESSION['e_haslo'] = "Haslo musi posiadac od 8 do 20 znaków";
        }

        if($haslo1!=$haslo2)
        {
            $wszystko_ok = false;
            $_SESSION['e_haslo'] = "Hasla nie sa identyczne";
        }

        $haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
        
        //Czy zaakceptowano regulamin
        if(!isset($_POST['regulamin']))
        {
            $wszystko_ok = false;
            $_SESSION['e_regulamin'] = "Regulamin musi zostać zaakceptowany.";
        }

        //Bot or not ?
        $secret_key = "6LcKNoUeAAAAAHW8uVzCmr0FXocjbtoy3tjXzoL6";

        $sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='
        .$secret_key.'&response='
        .$_POST['g-recaptcha-response']);

        $odpowiedz = json_decode($sprawdz);

        if(!$odpowiedz->success)
        {
            $wszystko_ok = false;
            $_SESSION['e_bot'] = "Potwierdź, że nie jestes botem !";
        }
        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try
        {

            $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
            if($polaczenie->connect_errno!=0)
            {
                throw new Exception(mysqli_connect_errno());
            }
            else
            {
                //Czy email juz istnieje ?
                $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");

                if(!$rezultat)
                {
                    throw new Exception($polaczenie->error);
                }
                $ile_takich_maili = $rezultat->num_rows;
                if($ile_takich_maili>0)
                {
                    $wszystko_ok = false;
                    $_SESSION['e_email'] = "Istnieje juz konto z takim adresem e-mail.";
                }

                //Czy nick juz istnieje ?
                $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");

                if(!$rezultat)
                {
                    throw new Exception($polaczenie->error);
                }
                $ile_takich_nickow = $rezultat->num_rows;
                if($ile_takich_nickow>0)
                {
                    $wszystko_ok = false;
                    $_SESSION['e_nick'] = "Istnieje juz konto z takim nickiem.";
                }

                if($wszystko_ok === true)
                {
                    //Dodajemy gracza do bazy
                    if($polaczenie->query("INSERT INTO uzytkownicy VALUES 
                    (NULL,'$nick','$haslo_hash','$email',100,100,100,now() + INTERVAL 14 DAY)"))
                    {
                        $_SESSION['udanarejestrajca'] = true;
                        header('Location:witamy.php');
                    }
                    else
                    {
                        throw new Exception($polaczenie->error);
                    }
                }
                $polaczenie->close();
            }

        }
        catch(Exception $e)
        {
            echo '<span style="color:red;">Błąd serwera, przepraszamy za niedogodności!</span>';
            //echo '<br> Informacja developerska: '.$e;
        }

        
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        .error
        {
            color:red;
            margin-top:10px;
            margin-bottom:10px;

        }
    </style>

    <title>Osadnicy - załóż darmowe konto</title>
</head>
<body>
    <form method="post" >
    Nickname: <br> <input type="text" name="nick"><br>
    <?php
        if(isset($_SESSION['e_nick']))
        {
            echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
            unset($_SESSION['e_nick']);
        }
    ?>

    E-mail: <br> <input type="text" name="email"> <br>
    <?php
        if(isset($_SESSION['e_email']))
        {
            echo '<div class="error">'.$_SESSION['e_email'].'</div>';
            unset($_SESSION['e_email']);
        }
    ?>

    Hasło: <br> <input type="password" name="haslo1"> <br>
    <?php
        if(isset($_SESSION['e_haslo']))
        {
            echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
            unset($_SESSION['e_haslo']);
        }
    ?>

    Powtórz hasło: <br> <input type="password" name="haslo2"> <br>
    <label>
    <input type="checkbox" name="regulamin"> Akceptuję regulamin

    </label>
    <?php
        if(isset($_SESSION['e_regulamin']))
        {
            echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
            unset($_SESSION['e_regulamin']);
        }
    ?>
    <div class="g-recaptcha" data-sitekey="6LcKNoUeAAAAADZ3oSFz0RsKAlLEIKwNZkLp_dqS"></div>
    <br>
    <?php
        if(isset($_SESSION['e_bot']))
        {
            echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
            unset($_SESSION['e_bot']);
        }
    ?>
    <input type="submit" value="Zarejestruj się">



    </form>

</body>
</html>