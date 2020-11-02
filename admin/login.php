<?php
require("includes/database.php");
if(empty($_SESSION["username"])) {
    // Sessie is leeg, dus admin moet zichzelf eerst indentificeren (inloggen).
} else {
    header("Location: panel.php");
    // Admin is al ingelogd, direct naar panel redirected.
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
</head>
<body>

<?php
include("includes/adminnav.php");

if(isset($_POST["login"])) {
    $errorMessage = "";
    
    $gebruikersnaam = $_POST["gebruikersnaam"];
    $wachtwoord = $_POST["wachtwoord"];

    if ($gebruikersnaam == "") {
        $errorMessage = "Je gebruikersnaam kan niet leeg zijn.";
    }
    if ($wachtwoord == "") {
        $errorMessage = "Je wachtwoord kan niet leeg zijn.";
    }

    if ($errorMessage == "") {
        try {
            $stmt = $connect->prepare("SELECT * FROM admins WHERE gebruikersnaam = :gebruikersnaam");
            $stmt->execute(array(
                ":gebruikersnaam" => $gebruikersnaam
            ));
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data == false) {
                $errorMessage = "Er bestaat geen account onder deze gebruikersnaam.";
            } else {
                if ($wachtwoord === $data["wachtwoord"]) {
                    $_SESSION["id"] = $data["id"];
                    $_SESSION["gebruikersnaam"] = $data["gebruikersnaam"];

                    header("Location: panel.php");
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
        <h1>Admin Login</h1>
        <h2>Als u een admin bent, kunt u inloggen.</h2>

        <input type="text" placeholder="Gebruikersnaam" name="gebruikersnaam" class="register-login-form-field" required>
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