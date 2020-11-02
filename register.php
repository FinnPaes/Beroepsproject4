<?php
require("includes/database.php");
if(empty($_SESSION["naam"])) {
    // De sessie is leeg, dat betekent dat de persoon nog niet ingelogd is en mag inloggen.
} else {
    header("Location: dashboard.php");
    // Persoon wordt doorgestuurd naar de dashboard pagina, als je al ingelogd bent heeft het geen nut om opnieuw in te loggen :-)
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreer &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/style.css">
</head>
<body>

<?php
include("includes/nav.php"); // Importeer nav

if(isset($_POST["register"])) { // Als de register knop ingedrukt wordt wordt dit uitgevoerd
    $errorMessage = "";

    $naam = $_POST["naam"]; // Pak data op uit het formulier
    $email = $_POST["email"];
    $rawWachtwoord = $_POST["wachtwoord"];
    $wachtwoord = password_hash($rawWachtwoord, PASSWORD_BCRYPT); // Hash wachtwoord variable met BCRYPT.

    if ($naam == "") { // Als de naam leeg is voer dit uit
        $errorMessage = "Je moet alle velden invullen voordat je door kan gaan";
    }
    if ($email == "") {
        $errorMessage = "Je moet alle velden invullen voordat je door kan gaan";
    }
    if ($rawWachtwoord == "") {
        $errorMessage = "Je moet alle velden invullen voordat je door kan gaan";
    }
    if (strlen($rawWachtwoord) < 6) { // Als het wachtwoord minder dan 6 teken is:
        $errorMessage = "Je wachtwoord is niet lang genoeg. Minimaal 6 karakters.";
    }

    if ($errorMessage == "") { // Als de error message leeg is voer dit uit
        try {
            $stmt = $connect->prepare("INSERT INTO gebruikers (naam, email, wachtwoord) VALUES (:naam, :email, :wachtwoord)"); // sql query
            $stmt->execute(array( // Alle woorden met : worden gebind met variables doormiddel van een arrow array
                ":naam" => $naam,
                ":email" => $email,
                ":wachtwoord" => $wachtwoord
            ));
            header("Location: login.php"); // Stuur door naar login pagina, registratie succesvol
            exit;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}

?>

<div class="register-login-wrapper">
    <form action="" method="POST" class="register-login-form">
        <h1>Nieuw account aanmaken</h1>
        <h2>Vul de onderstaande velden in om een account te maken</h2>

        <input type="text" placeholder="Voornaam + Achternaam" name="naam" class="register-login-form-field" required>
        <input type="email" placeholder="Email" name="email" class="register-login-form-field" required>
        <input type="password" placeholder="Wachtwoord" name="wachtwoord" class="register-login-form-field" required>

        <input type="submit" value="Registreer" name="register" class="register-login-form-btn">
        <?php
        if(isset($errorMessage)) {
            echo '<div style="color:#FF0000;text-align:center;font-size:1rem;margin-top:5px;">'.$errorMessage.'</div>';
        }
    ?>
    </form>
</div>

    
</body>
</html>