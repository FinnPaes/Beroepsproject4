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
    <title>Login &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/style.css">
</head>
<body>

<?php
include("includes/nav.php");

if(isset($_POST["login"])) { // als de login knop wordt ingedrukt
    $errorMessage = "";
    
    $email = $_POST["email"]; // Haal de email & wachtwoord op uit het formulier, zet het in een variable.
    $wachtwoord = $_POST["wachtwoord"];

    if ($email == "") { // Als de email leeg is voer dit uit
        $errorMessage = "Je email kan niet leeg zijn.";
    }
    if ($wachtwoord == "") {// Als het wachtwoord veld leeg is voer dit uit
        $errorMessage = "Je wachtwoord kan niet leeg zijn.";
    }

    if ($errorMessage == "") { // Als de errormessage variable leeg is, dan gaat hij door
        try {
            $stmt = $connect->prepare("SELECT * FROM gebruikers WHERE email = :email"); // SQL query
            $stmt->execute(array(
                ":email" => $email
            ));
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data == false) { // Als de data uit de sql query klopt, dus er komt een email in voor, stop met errormessage.
                $errorMessage = "Er bestaat geen account onder deze email.";
            } else { // Als de data wel klopt wordt het wachtwoord gecheckt en een sessie aangemaakt
                if (password_verify($wachtwoord, $data["wachtwoord"])) { // Als het wachtwoord overeenkomt met de hashed BCRYPT uit de Database voer dit uit
                    $_SESSION["id"] = $data["id"]; // Stop alle info in de sessie cookie
                    $_SESSION["naam"] = $data["naam"];
                    $_SESSION["email"] = $data["email"];
                    $_SESSION["wachtwoord"] = $data["wachtwoord"];

                    header("Location: dashboard.php"); // Stuur naar dashboard omdat gebruiker ingelogd is.
                    exit;
                } else {
                    $errorMessage = "Het opgegeven wachtwoord komt niet overeen met je account.";
                }
            }
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
        }
    }
}
?>

<div class="register-login-wrapper">
    <form action="" method="POST" class="register-login-form">
        <h1>Inloggen</h1>
        <h2>Als u al een account heeft kunt u hier inloggen</h2>

        <input type="email" placeholder="Email" name="email" class="register-login-form-field" required>
        <input type="password" placeholder="Wachtwoord" name="wachtwoord" class="register-login-form-field" required>

        <input type="submit" value="Inloggen" name="login" class="register-login-form-btn">
        <?php
        if(isset($errorMessage)) {
            echo '<div style="color:#FF0000;text-align:center;font-size:1rem;margin-top:5px;">'.$errorMessage.'</div>';
        }
    ?>
    </form>
</div>


</body>
</html>