<?php
require("includes/database.php");

// Variables voor gebruik, deze zijn opgeslagen tijdens de login.
$id = $_SESSION["id"];
$naam = $_SESSION["naam"];
$email = $_SESSION["email"];

$datum = date("d-m-Y"); // Datum van dit moment, voor verwelkom.

// Ophalen van alle claims die de persoon heeft aangemaakt

try {
    $stmt = $connect->prepare("SELECT * FROM claims ORDER BY claimID DESC");
    $stmt->execute();
    $fetchClaims = $stmt->fetchAll(PDO::FETCH_OBJ);
    $claimsTeller = $stmt->rowCount();
} catch(PDOException $e) {
    $errorMessage = $e->getMessage();
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel &mdash; Alphen aan den Rijn Kraanongeval</title>
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
    <link rel="stylesheet" type="text/css" href="includes/admin.css">
</head>
<body>

<?php
include("includes/adminnav.php");
?>

<section class="dashboard-welkom">
    <h1>Goededag admin!</h1>

    <div class="dashboard-welkom-statistiek-wrapper">
        <div class="dashboard-welkom-statistiek-box">
            <h2>Datum</h2>
            <p><?php echo $datum; ?></p>
        </div>
        <div class="dashboard-welkom-statistiek-box">
            <h2>Totaal Aantal Claims</h2>
            <p><?php echo $claimsTeller; ?></p>
        </div>
    </div>
</section>

<section class="dashboard-claims">
<h1>Hier zijn alle ingediende claims:</h1>
    <div class="dashboard-claims-all">
        <table class="dashboard-claims-table">
            <tr class="dashboard-claims-table-head">
                <th>ID</th>
                <th>Titel</th>
                <th>Beschrijving</th>
                <th>Status</th>
                <th>Beheer</th>
            </tr>
            <!-- Start van content -->

            <?php
            foreach($fetchClaims as $claim)
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
                <td><a href="panel.php?goedkeuren=<?php echo $claim->claimID;?>" class="goedkeuren">Goedkeuren</a> &mdash; <a href="panel.php?afwijzen=<?php echo $claim->claimID;?>" class="afwijzen">Afwijzen</a><br><br><a href="pdfexport.php?pdf=<?php echo $claim->claimID;?>" class="exporteerpdf">Exporteer PDF</a></td>
            </tr>
            <?php
            }
            ?>
            <!-- Einde van content -->
        </table>
    </div>
</section>

<?php // Het stuk om claims goed & af te keuren (goedkeuren, afwijzen knoppen)

// Goedkeuren
if(isset($_REQUEST["goedkeuren"])) {
    $GETclaimID = intval($_GET["goedkeuren"]); // De ID van de claim die goedgekeurd moet worden.

    $stmt = $connect->prepare("UPDATE claims SET status = 1 WHERE claimID = :claimid");
    $stmt->execute(array(
        ":claimid" => $GETclaimID
    ));
    header("Location: panel.php");
}

// Afwijzen
if(isset($_REQUEST["afwijzen"])) {
    $GETclaimID = intval($_GET["afwijzen"]); // De ID van de claim die afgewezen moet worden.

    $stmt = $connect->prepare("UPDATE claims SET status = 2 WHERE claimID = :claimid");
    $stmt->execute(array(
        ":claimid" => $GETclaimID
    ));
    header("Location: panel.php");
}


?>

</body>
</html>