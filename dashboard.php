<?php
require("includes/database.php"); // Importeer database
if(empty($_SESSION["naam"])) {
    header("Location: login.php");
    // Persoon wordt doorgestuurd naar de login pagina, hij is niet ingelogd.
} else {
    // Er hoeft niks te gebeuren, persoon is netjes ingelogd hij mag zijn dashboard bekijken.
}

// Variables voor gebruik, deze zijn opgeslagen tijdens de login, hier haal ik ze op uit de session cookie
$id = $_SESSION["id"];
$naam = $_SESSION["naam"];
$email = $_SESSION["email"];

$datum = date("d-m-Y"); // Datum van dit moment, voor verwelkom.

// Ophalen van alle claims die de persoon heeft aangemaakt

try { // Try function om SQL uit te voeren, als er een error is wordt hij opgevangen
    $stmt = $connect->prepare("SELECT * FROM claims WHERE gebruikerID = :gebruikerID ORDER BY claimID DESC");
    $stmt->execute(array(
        ":gebruikerID" => $id
    ));
    $fetchClaims = $stmt->fetchAll(PDO::FETCH_OBJ); // Pak alle rows die opgekomen zijn uit de query
    $claimsTeller = $stmt->rowCount(); // Tel alle rows die uit de SQL query zijn gekomen
} catch(PDOException $e) { // Als er een error is, echo die eruit.
    $errorMessage = $e->getMessage();
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/style.css">
</head>
<body>

<?php
include("includes/dashboardnav.php");
?>

<section class="dashboard-welkom">
    <h1>Goededag <span class="dashboard-welkom-naam"><?php echo $naam ?></span>!</h1>

    <div class="dashboard-welkom-statistiek-wrapper">
        <div class="dashboard-welkom-statistiek-box">
            <h2>Datum</h2>
            <p><?php echo $datum; ?></p>
        </div>
        <div class="dashboard-welkom-statistiek-box">
            <h2>Uw Claims</h2>
            <p><?php echo $claimsTeller; ?></p>
        </div>
    </div>
</section>

<section class="dashboard-claims">
<h1>Hier zijn al uw claims:</h1>
    <div class="dashboard-claims-all">
        <table class="dashboard-claims-table">
            <tr class="dashboard-claims-table-head">
                <th>ID</th>
                <th>Titel</th>
                <th>Beschrijving</th>
                <th>Status</th>
            </tr>
            <!-- Start van content -->

            <?php
            foreach($fetchClaims as $claim) // fetchclaims komt van de query hierboven, hier wordt elk resultaat uitgeprint.
            {
                $beschrijvingGeknipt = mb_strimwidth($claim->beschrijving, 0, 80, "..."); // Laat 80 characters toe, en zet er puntjes achter als het te lang wordt.
                $claimStatus = $claim->status;

                if ($claimStatus == 0) { // 0 in DB (automatisch) = In Behandeling
                    $claimStatus = "<span style='color: #FFA500;'>In Behandeling</span>";
                } else if ($claimStatus == 1) { // 1 in DB = Goedgekeurd
                    $claimStatus = "<span style='color: #00CC00;'>Goedgekeurd</span>";
                } else if ($claimStatus == 2) { // 2 in DB = Afgekeurd
                    $claimStatus = "<span style='color: #FF4040;'>Afgekeurd</span>";
                }

            ?>
            <tr>
                <td><?php echo htmlentities($claim->claimID);?></td>
                <td><?php echo htmlentities($claim->titel);?></td>
                <td><?php echo $beschrijvingGeknipt;?></td>
                <td><?php echo $claimStatus;?></td>
            </tr>
            <?php
            }
            ?>
            <!-- Einde van content -->
        </table>
        <button onclick="nieuweClaim()">Nieuwe Claim Maken</button>
    </div>
    

</section>


<script type="text/javascript">
    function nieuweClaim() {
        window.location.href = "nieuweclaim.php"; // Stuurt je naar de nieuwe claim pagina wanneer je op de knop drukt d.m.v. onclick function
    }
</script>
</body>
</html>
