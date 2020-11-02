<?php
require("includes/database.php");
if(empty($_SESSION["naam"])) {
    header("Location: login.php");
    // Persoon wordt doorgestuurd naar de login pagina, hij is niet ingelogd.
} else {
    // Er hoeft niks te gebeuren, persoon is netjes ingelogd hij mag een nieuwe claim maken..
}

$id = $_SESSION["id"]; // Stop alle info in een sessie cookie
$naam = $_SESSION["naam"];
$email = $_SESSION["email"];
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Claim &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/style.css">
</head>
<body>

<?php
include("includes/dashboardnav.php");

if(isset($_POST["verstuur"])) { // Wanner de verstuur knop in het form wordt ingedrukt wordt dit uitgevoerd, anders niet.
    $errorMessage = "";

    $woonplaats = $_POST["woonplaats"]; // Stop de woonplaats van het formulier 
    $straat = $_POST["straat"];
    $huisnummer = $_POST["huisnummer"];
    $postcode = $_POST["postcode"];
    $telefoonnummer = $_POST["telefoonnummer"];
    $titel = $_POST["titel"];
    $beschrijving = $_POST["beschrijving"];

    if ($errorMessage == "") { // Als error message leeg is voer dit uit
        try {
            $stmt = $connect->prepare("INSERT INTO claims (gebruikerID, naam, woonplaats, straat, huisnummer, postcode, telefoonnummer, titel, beschrijving) VALUES (:gebruikerID, :naam, :woonplaats, :straat, :huisnummer, :postcode, :telefoonnummer, :titel, :beschrijving)"); // sql query zoals gewoonlijk
            $stmt->execute(array( // In de query gebruik ik :naam , hiermee link ik dit aan een variable door een arrow array.
                ":gebruikerID" => $id,
                ":naam" => $naam,
                ":woonplaats" => $woonplaats,
                ":straat" => $straat,
                ":huisnummer" => $huisnummer,
                ":postcode" => $postcode,
                ":telefoonnummer" => $telefoonnummer,
                ":titel" => $titel,
                ":beschrijving" => $beschrijving
            ));
            header("Location: dashboard.php"); // Stuur naar dashboard, persoon heeft netjes een nieuwe claim aangemaakt
            exit; // Stop PHP script
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}


?>


<div class="nieuwe-claim-container">
<form action="" method="POST">

    <h1>Nieuwe Claim</h1>
    <h2>Vul de onderstaande gegevens in om een nieuwe claim aan te maken.</h2>
    
    <p>Naam:</p>
    <input type="text" placeholder="<?php echo$naam; ?>" name="naam" class="register-login-form-field" disabled required>

    <p>Woonplaats:</p>
    <input type="text" placeholder="Woonplaats" name="woonplaats" class="register-login-form-field" required>

    <p>Straat:</p>
    <input type="text" placeholder="Straat" name="straat" class="register-login-form-field" required>

    <p>Huisnummer:</p>
    <input type="text" placeholder="Huisnummer" name="huisnummer" class="register-login-form-field" required>

    <p>Postcode:</p>
    <input type="text" placeholder="Postcode" name="postcode" class="register-login-form-field" required>

    <p>Telefoonnummer:</p>
    <input type="text" placeholder="Telefoonnummer" name="telefoonnummer" class="register-login-form-field" required>

    <p>Titel:</p>
    <input type="text" placeholder="Titel" name="titel" class="register-login-form-field" required>

    <p>Beschrijving:</p>
    <textarea placeholder="Beschrijving" name="beschrijving" class="register-login-form-field" required></textarea>


    <input type="submit" value="Verstuur Claim" name="verstuur" class="register-login-form-btn">

</form>
</div>

    
</body>
</html>