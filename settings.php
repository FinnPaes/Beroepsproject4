<?php
require("includes/database.php");
if(empty($_SESSION["naam"])) {
    header("Location: login.php");
    // Persoon wordt doorgestuurd naar de login pagina, hij is niet ingelogd.
} else {
    // Er hoeft niks te gebeuren, persoon is netjes ingelogd hij mag zijn instellingen bekijken.
}

// Zet alle info in een sessie
$id = $_SESSION["id"];
$naam = $_SESSION["naam"];
$email = $_SESSION["email"];

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instellingen &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/style.css">
</head>
<body>

<?php
include("includes/dashboardnav.php");

// Haal profiel foto op!
$stmt = $connect->prepare("SELECT foto FROM gebruikers WHERE id = :id"); // sql query
$stmt->execute(array(
    ":id" => $id
));
$profielfoto = $stmt->fetch(PDO::FETCH_OBJ);


// Profielfoto form
if(isset($_FILES["file"])) {
    $file = $_FILES["file"];

    // Bestand info
    $file_name = $file["name"];
    $file_tmp = $file["tmp_name"];
    $file_size = $file["size"];
    $file_error = $file["error"];

    $file_ext = explode(".", $file_name);
    $file_ext = strtolower(end($file_ext));

    $toegestaan = array("png", "jpg", "jpeg");

    if (in_array($file_ext, $toegestaan)) { // Als het bestand extensie type in de array staat ->
        if ($file_error === 0) { // Als er geen file errors zijn
            if ($file_size <= 52428800) { // Als het bestand kleiner of gelijk is aan dit aantal bytes

                $file_name_new = uniqid("", true) . '.' . $file_ext; // Nieuwe file name wordt gegenereerd
                $file_destination = "assets/profielfotos/" . $file_name_new; // Bestandslocatie met file name wordt in vairable gezet voor later gebruik

                if (move_uploaded_file($file_tmp, $file_destination)) { // Als het bestand succesvol geupload is voer dit uit
                    $stmt = $connect->prepare("UPDATE gebruikers SET foto = :file_name_new WHERE id = :id"); // sql query
                    $stmt->execute(array( // bind alle : met variables/data
                        ":file_name_new" => $file_name_new,
                        ":id" => $id
                    ));
                    header("Location: settings.php"); // Klaar, ga weer naar settings page zodat je je profiel foto kan zien
                }

            }
        }
    }

}


// Wachtwoord form
if(isset($_POST["verander"])) { // Wanneer de wachtwoord wijzig knop aangedrukt wordt
    $errorMessage = "";

    $rawWachtwoord = $_POST["wachtwoord"]; // Het wachtwoord uit de post
    $hashedWachtwoord = password_hash($rawWachtwoord, PASSWORD_BCRYPT); // hier wordt het wachtwoord uit de post formulier gencrypt met BCRYPT

    if ($rawWachtwoord == "") { // Als het wachtwoord leeg is voer dit uit
        $errorMessage = "Wachtwoord kan niet leeg zijn.";
    }

    if ($errorMessage == "") { // geen error message, dus variable leeg? voer dit uit
        try {
            $stmt = "UPDATE gebruikers SET wachtwoord = :wachtwoord WHERE id = :id"; // sql query
            $stmt = $connect->prepare($stmt);
            $stmt->execute(array( // bind data weer met arrow array
                ":wachtwoord" => $hashedWachtwoord,
                ":id" => $id
            ));
            header("Location: settings.php?action=succes"); // Stuur persoon door + get request eraan verbonden
            exit;
        } catch(PDOException $e) {
            $errorMessage = $e->getMessage();
        }
    }
}

if(isset($_GET["action"]) && $_GET["action"] == "succes") { // Als de get achter de URL overeenkomt met ?action=succes voer dit uit, anders niet.
    echo "<script>alert('Wachtwoord bijgewerkt!');</script>"; // Geef popup weer door middel van javascript alert
    
}

?>

<br><br><br>

<form action="" method="POST" enctype="multipart/form-data" class="register-login-form">
    <h1>Profiel foto</h1>
    <h2>Hieronder kunt u uw profielfoto aanpassen, png, jpg of jpeg bestanden zijn toegestaan!</h2>
    <img src="assets/profielfotos/<?php echo $profielfoto->foto;?>" class="settings-profielfoto" />

    <br><br>
    <center><input type="file" name="file">
    <input type="submit" value="Uploaden"></center>
</form>

<br><br><br>

<div class="register-login-wrapper">
    <form action="" method="POST" class="register-login-form">
        <h1>Wachtwoord aanpassen</h1>
        <h2>Vul de onderstaande velden in om uw wachtwoord aan te passen!</h2>

        <input type="password" placeholder="**************" name="oldPass" class="register-login-form-field" style="cursor:not-allowed;" disabled required>
        <input type="password" placeholder="Nieuw wachtwoord" name="wachtwoord" class="register-login-form-field" required>

        <input type="submit" value="Registreer" name="verander" class="register-login-form-btn">
        <?php
        if(isset($errorMessage)) {
            echo '<div style="color:#FF0000;text-align:center;font-size:1rem;margin-top:5px;">'.$errorMessage.'</div>';
        }
    ?>
    </form>
</div>

    
</body>
</html>